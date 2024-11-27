<?php

namespace App\Controllers\ADMIN;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Product extends BaseController
{

    public function index(): string
    {
        helper(['check_session']);
        $session = session();
        check_session_login();

        $data['title'] = '상품 현황';
        $data['session'] = $session->get("username");

        return view('content/product', $data);
    }




    public function saveExcel()
    {
        helper(['ut_result','check_session']);
        ini_set('memory_limit', '-1');
        check_session_login();

        //입력데이터 및 유효성 검사 설정
        $inputData = $this->request->getPost('uploadfile');
        $validation = \Config\Services::validation();
        $validation->setRules([
            'uploadfile' => ['label' => '엑셀파일', 'rules' => 'uploaded[uploadfile]'
                . '|mime_in[uploadfile, application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]'
            ],
        ]);

        // 유효성 검사 실행
        if (!$validation->run($inputData)) {
            log_message('error', print_r($validation->getErrors(), true));
            ut_ProcError('엑셀파일 유효성 검사 오류');
            exit();
        }

        //엑셀 파일 읽기 준비
        $excelFile = $this->request->getFile('uploadfile');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');

        try {
            //엑셀 파일을 읽기 시작
            $spreadSheet = $reader->load($excelFile->getTempName());


            //시트 이름을 확인하여 파일 구분 및 처리
            $sheetNames = $spreadSheet->getSheetNames(); //모든 시트이름을 배열로 반환
            log_message('debug', print_r($sheetNames, true));
            if (in_array('Sheet2', $sheetNames)) {
                // proudct.xlsx sheet 이름
                $this->processProductExcel($spreadSheet);
            } else if (in_array('medicine', $sheetNames)) {
                // 의약품.xlsx sheet 이름 medicine
                $this->processMedicineExcel($spreadSheet);
            } else {
                ut_ProcError('올바른 시트가 없습니다. Sheet2 또는 medicine이 필요합니다.');
                exit();
            }

            ut_procSuccess();
            exit();
        } catch(\Exception $e) {
            log_message('error', '엑셀 파일 로드 실패: ' . $e->getMessage());
            ut_ProcError("엑셀 파일 로드 중 문제가 발생했습니다. " . $e->getMessage());
            exit();
        }

    }

    //소모품 processProductExcel
    public function processProductExcel($spreadSheet) : void
    {
        helper(['ut_result','check_session']);
        check_session_login();

        try{

            $sheet = $spreadSheet->getActiveSheet();
            $lastRow = $sheet->getHighestRow();
            $lastColumn = $sheet->getHighestColumn();
            $ProductData = $sheet->rangeToArray("A5:{$lastColumn}{$lastRow}");




            for ($i = 0; $i < count($ProductData); $i++) { // count($ProductData) = 요소 수 (행의 갯수)
                if (empty($ProductData[$i][0])) {
                    continue;
                }



                $PrTempData = [
                    "ProductName" => $ProductData[$i][0],                   // 상품명
                    "Specification" => $ProductData[$i][1],                 // 규격
                    "Unit" => $ProductData[$i][2],                          // 단위
                    "Company" => $ProductData[$i][3],                       // 제조회사
                    "ConvertedPrice" => str_replace(',','',$ProductData[$i][4]),                // 환산약가
                    "ConvertedQuantity" => str_replace(',','',$ProductData[$i][5]),             // 환산수량
                    "StandardCode" => $ProductData[$i][6],                  // 표준코드
                    "SpecialtyDivision" => $ProductData[$i][7],              // 전문구분
                    "InsuranceClassification" => $ProductData[$i][8],       // 보험구분
                    "SpecificItem" => $ProductData[$i][9],                  // 특정품목
                    "InvoiceIssue" => $ProductData[$i][10],                 // 계산서발행
                    "TransactionStatus" => $ProductData[$i][11],            // 거래여부
                    "SupplySubmission" => $ProductData[$i][12],           // 공급내역제출
                    "DosageForm" => $ProductData[$i][13],                   // 제형구분
                    "MedicineType" => $ProductData[$i][14],                 // 약품구분
                    "ProductCode" => $ProductData[$i][15],                  // 상품코드
                    "EnglishName" => $ProductData[$i][16],                 // 영문명
                    "Remarks" => $ProductData[$i][17],                      // 비고
                    "SellerName" => $ProductData[$i][18],                   // 판매처명
                    "InsurancePrice" => str_replace(',','',$ProductData[$i][19]),               // 보험약가
                    "RotationDays" => $ProductData[$i][20],               // 기준회전일
                    "InsuranceCode" => $ProductData[$i][21],                // 보험코드
                    "ItemClassification" => $ProductData[$i][22],           // 품목구분
                    "PurchasePrice" => $ProductData[$i][23],                // 매입단가
                    "DrugInfo" => $ProductData[$i][24],                   // 마약정보

                ];

                $ArrProductData[] = $PrTempData;//배열의 끝에 추가방식 단일요소 x

            }


            $db = db_connect();
            $ProductModel = new ProductModel();

            $db->transBegin(); // 트랜잭션 시작

            // 1. 기존 약품 데이터 삭제
            $ProductModel->where('MedicineType', '소모품')->delete(); // 약품 데이터만 삭제

            // 2. 새로운 약품 데이터 일괄 삽입
            $ProductModel->insertBatch($ArrProductData); // 약품 데이터 일괄 삽입

            $db->transComplete(); // 트랜잭션 종료

            if ($db->transStatus() === false) {
                $db->transRollback();
                ut_ProcError("데이터 저장 중 오류 발생");
            } else {
                ut_procSuccess();
            }
            exit();

        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            ut_ProcError('processProductExcel 오류'.$e->getMessage());
            exit();
        }



    }


    //소모품 processMedicineExcel
    public function processMedicineExcel($spreadSheet) : void
    {
        helper(['ut_result','check_session']);
        check_session_login();

        try{

            $sheet = $spreadSheet->getActiveSheet();
            $lastRow = $sheet->getHighestRow();
            $lastColumn = $sheet->getHighestColumn();
            $ProductData = $sheet->rangeToArray("A5:{$lastColumn}{$lastRow}");

            log_message('debug','마지막 열 : {$lastColumn}, 마지막 행: {$lastRow}');

            $ArrProductData = [];

            for ($i = 0; $i < count($ProductData); $i++) { // count($ProductData) = 요소 수 (행의 갯수)
                if (empty($ProductData[$i][0])) {
                    continue;
                }

                $PrTempData = [
                    "ProductName" => $ProductData[$i][0],                   // 상품명
                    "Specification" => $ProductData[$i][1],                 // 규격
                    "Unit" => $ProductData[$i][2],                          // 단위
                    "Company" => $ProductData[$i][3],                       // 제조회사
                    "ConvertedPrice" => str_replace(',','',$ProductData[$i][4]),                // 환산약가
                    "ConvertedQuantity" => str_replace(',','',$ProductData[$i][5]),             // 환산수량
                    "StandardCode" => $ProductData[$i][6],                  // 표준코드
                    "Category_Name" => $ProductData[$i][7],                 // 분류명
                    "Ingredient_Name" => $ProductData[$i][8],               // 주성분명
                    "SpecialtyDivision" => $ProductData[$i][9],              // 전문구분
                    "InsuranceClassification" => $ProductData[$i][10],       // 보험구분
                    "SpecificItem" => $ProductData[$i][11],                  // 특정품목
                    "InvoiceIssue" => $ProductData[$i][12],                 // 계산서발행
                    "TransactionStatus" => $ProductData[$i][13],            // 거래여부
                    "SupplySubmission" => $ProductData[$i][14],           // 공급내역제출
                    "DosageForm" => $ProductData[$i][15],                   // 제형구분
                    "MedicineType" => $ProductData[$i][16],                 // 약품구분
                    "ProductCode" => $ProductData[$i][17],                  // 상품코드
                    "EnglishName" => $ProductData[$i][18],                 // 영문명
                    "Remarks" => $ProductData[$i][19],                      // 비고
                    "SellerName" => $ProductData[$i][20],                   // 판매처명
                    "InsurancePrice" => str_replace(',','',$ProductData[$i][21]), // 보험약가
                    "RotationDays" => $ProductData[$i][22],               // 기준회전일
                    "Category_Number" => $ProductData[$i][23],              // 분류번호
                    "InsuranceCode" => $ProductData[$i][24],                // 보험코드
                    "Ingredient_Code" => $ProductData[$i][25],              // 주성분코드
                    "ItemClassification" => $ProductData[$i][26],           // 품목구분
                    "PurchasePrice" => $ProductData[$i][27],                // 매입단가
                    "DrugInfo" => $ProductData[$i][28],                   // 마약정보

                ];

                $ArrProductData[] = $PrTempData;//배열의 끝에 추가방식 단일요소 x

            }

            $db = db_connect();
            $ProductModel = new ProductModel();



            $db->transBegin(); // 트랜잭션 시작

            // 1. 기존 약품 데이터 삭제
            $ProductModel->where('MedicineType', '약품')->delete(); // 약품 데이터만 삭제

            // 2. 새로운 약품 데이터 일괄 삽입
            $ProductModel->insertBatch($ArrProductData); // 약품 데이터 일괄 삽입

            $db->transComplete(); // 트랜잭션 종료

            if ($db->transStatus() === false) {
                $db->transRollback();
                ut_ProcError("데이터 저장 중 오류 발생");
            } else {
                ut_procSuccess();
            }
            exit();


        } catch (\Exception $e) {
            log_message('error', 'processMedicineExcel 오류:'.$e->getMessage());
            ut_ProcError('processProductExcel 오류'.$e->getMessage());
            exit();
        }



    }





    //데이터 테이블 업로드시 리스트
    public function List()
    {
        helper(['ut_result', 'check_session']);
        check_session_login();


        try {
            $ProductModel = new ProductModel();

            //관리자 유형 확인
            $adminType = session()->get('AdminType');
            if($adminType === 'A') {
                //일반 관리자에게는 빈 데이터 반환
                return $this->response->setJSON([ 'draw' => $this->request->getPost('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => []]);
            }

            //DataTables 요청 매개ㅂ
//            $dataTable = $this->request->getPost();
            $dataTable = $this->request->getPost();
            // log_message('debug',print_r($dataTable, true));
            if (!empty($dataTable)) { //empty 0 true
                $draw = $dataTable['draw'];
                $start = intval($dataTable['start']);
                $length = intval($dataTable['length']);
                $searchValue = $dataTable['searchValue'] ?? ''; // 검색어 (없을 경우 빈 문자열)

            } else {
                $draw = null;
                $length = null;
                $start = null;
            }


            log_message('debug', print_r($dataTable, true));

            //전체 레코드 수 및 필터된 레코드수
            $totalRecords = $ProductModel->builder()->countAll();



            // 검색 조건 적용: ProductName or Comapany에서 검색어 포함되는 항목
            if (!empty($searchValue)) {
                $ProductModel->groupStart()
                    ->like('ProductName', $searchValue)
                    ->orLike('Company', $searchValue)
                    ->orLike('ProductCode', $searchValue)
                    ->groupEnd();
            }//WHERE (ProductName LIKE '%searchValue%' OR Company LIKE '%searchValue%')

            $filteredRecords = $ProductModel->countAllResults(false); //false 추가 query 작업 필요 초기화 x
            // 페이지당 데이터 가져오기
            $data = $ProductModel->findAll($length, $start);
            //log_message('debug',print_r($data,true));

            $resData = [
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ];




            ut_procSuccess($resData);

            exit();


        } catch (\Exception $e) {
            ut_ProcError('리스트 업로드 조회 문제발생');
            exit();
        }

    }


    //데이터 테이블 수정 저장
    public function save() : void
    {
        helper(['ut_result', 'check_session']);
        check_session_login();

        try {
            $reqData = $this->request->getJSON(true);
            $validation = \Config\Services::validation();
            log_message('debug', print_r($reqData, true));
            $validation->setRules([
                'ProductCode' => ['label' => '상품코드', 'rules' => 'required'],
                'ProductName' => ['label' => '상품명', 'rules' => 'required'],
                'Specification'            => ['label' => '규격','rules' => 'permit_empty'],
                'Unit'                     => ['label' => '단위','rules' => 'permit_empty'],
                'Company'                  => ['label' => '제조회사', 'rules' => 'required'],
                'ConvertedPrice'           => ['label' => '환산약가', 'rules' => 'required'],
                'ConvertedQuantity'        => ['label' => '환산수', 'rules' => 'required'],
                'StandardCode'             => ['label' => '표준코드','rules' => 'permit_empty'],
                'SpecialtyDivision'        => ['label' => '전문구분', 'rules' => 'required'],
                'InsuranceClassification'  => ['label' => '보험구분', 'rules' => 'required'],
                'SpecificItem'             => ['label' => '특정품목', 'rules' => 'permit_empty'],
                'InvoiceIssue'             => ['label' => '계산서발행', 'rules' => 'required'],
                'TransactionStatus'        => ['label' => '거래여부', 'rules' => 'required'],
                'SupplySubmission'         => ['label' => '공급내역제출', 'rules' => 'required'],
                'DosageForm'               => ['label' => '제형구분', 'rules' => 'required'],
                'MedicineType'             => ['label' => '약품구분', 'rules' => 'required'],
                'EnglishName'              => ['label' => '영문명','rules' => 'permit_empty'],
                'Remarks'                  => ['label' => '비고','rules' => 'permit_empty'],
                'SellerName'               => ['label' => '판매처명','rules' => 'required'],
                'InsurancePrice'           => ['label' => '보험약가','rules' => 'required'],
                'RotationDays'             => ['label' => '기준회전일','rules' => 'required'],
                'InsuranceCode'            => ['label' => '보험코드','rules' => 'permit_empty'],
                'ItemClassification'       => ['label' => '품목구분','rules' => 'required'],
                'PurchasePrice'            => ['label' => '매입단가','rules' => 'required'],
                'DrugInfo'                 => ['label' => '마약정보','rules' => 'required'],
                'Category_Name'            => ['label' => '분류명','rules' => 'permit_empty'],
                'Ingredient_Name'          => ['label' => '주성분명','rules' => 'permit_empty'],
                'Category_Number'          => ['label' => '분류번호','rules' => 'permit_empty'],
                'Ingredient_Code'          => ['label' => '주성분코드','rules' => 'permit_empty']
            ]);

            // $test = $reqData['PurchasePrice'] == null ?  null : $reqData['PurchasePrice'];

            if (!$validation->run($reqData)) {

                log_message('error', print_r($validation->getErrors(), true));
                ut_ProcError('비어있는 칸 정보를 확인해주세요');
                exit();
            }


            $data = [
                // 'ProductCode' => $reqData['ProductCode'],
                'ProductName' => $reqData['ProductName'],
                'Specification' => $reqData['Specification'],
                'Unit' => $reqData['Unit'],
                'Company' =>  $reqData['Company'],
                'ConvertedPrice' => $reqData['ConvertedPrice'],
                'ConvertedQuantity' => $reqData['ConvertedQuantity'],
                'StandardCode' => $reqData['StandardCode'],
                'SpecialtyDivision' => $reqData['SpecialtyDivision'],
                'InsuranceClassification' => $reqData['InsuranceClassification'],
                'SpecificItem' => $reqData['SpecificItem'],
                'InvoiceIssue' => $reqData['InvoiceIssue'],
                'TransactionStatus' => $reqData['TransactionStatus'],
                'SupplySubmission' => $reqData['SupplySubmission'],
                'DosageForm' => $reqData['DosageForm'],
                'MedicineType' => $reqData['MedicineType'],
                'EnglishName' => $reqData['EnglishName'],
                'Remarks' => $reqData['Remarks'],
                'SellerName' => $reqData['SellerName'],
                'InsurancePrice' => $reqData['InsurancePrice'],
                'RotationDays' => $reqData['RotationDays'],
                'InsuranceCode' => $reqData['InsuranceCode'],
                'ItemClassification' => $reqData['ItemClassification'],
                'PurchasePrice' => $reqData['PurchasePrice'],
                'DrugInfo' =>  $reqData['DrugInfo'],
                'Category_Name' => $reqData['Category_Name'],
                'Ingredient_Name' => $reqData['Ingredient_Name'],
                'Category_Number' => $reqData['Category_Number'],
                'Ingredient_Code' => $reqData['Ingredient_Code']

            ];
            log_message('debug', print_r($data, true));

            $productModel = new ProductModel();

            $productModel->update($reqData['ProductCode'], $data);
            ut_procSuccess();
            exit();

        } catch (\Exception $e) {
            log_message('error', '상품 수정 중 오류 발생: ' . $e->getMessage());
            ut_ProcError('상품 저장 중 오류가 발생하였습니다.');
            exit();
        }


    }

    public function delete()
    {
        helper(['ut_result', 'check_session']);
        check_session_login();
        try {

            $reqData = $this->request->getJSON(true);
            $validation = \Config\Services::validation();
            log_message('debug', print_r($reqData, true));
            $validation->setRules([
                'ProductCodes' => ['label' => '상품코드', 'rules' => 'required'],
            ]);

            if (!$validation->run($reqData)) {
                $errMsg = $validation->getErrors();
                log_message('error', 'Server delete Validation ERROR');
                log_message('error', print_r($errMsg, true));
                exit();
            }

            $productModel = new ProductModel();


            $productModel->whereIn('ProductCode',$reqData['ProductCodes'])->delete();


            ut_procSuccess();
            exit();


        } catch (\Exception $e) {
            ut_ProcError('삭제중 문제발생');
            log_message('error', print_r($e, true));
            exit();
        }


    }


    //////insert Batch 방식
    public function batchExcel()
    {
        helper(['ut_result', 'check_session']);
        ini_set('memory_limit', '-1'); // 메모리 제한 해제
        check_session_login();

        $inputData = $this->request->getPost();
        $validation = \Config\Services::validation();
        $validation->setRules([
            'uploadfile' => ['label' => '엑셀파일', 'rules' => 'uploaded[uploadfile]'
                . '|mime_in[uploadfile, application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]'
            ],
        ]);

        if (!$validation->run($inputData)) {
            $errorMsg = implode(",", array_values($validation->getErrors()));
            ut_ProcError($errorMsg);
            exit();
        }

        $excelFile = $this->request->getFile('uploadfile');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');

        try {
            $sheetName = "Sheet2"; // 원하는 시트명 지정
            $reader->setLoadSheetsOnly($sheetName);
            $spreadSheet = $reader->load($excelFile->getTempName());

            // 데이터 범위를 설정하고 A5부터 시작
            $lastRow = $spreadSheet->getActiveSheet()->getHighestRow();
            $lastColumn = $spreadSheet->getActiveSheet()->getHighestColumn();
            $ProductData = $spreadSheet->getActiveSheet()->rangeToArray("A5:{$lastColumn}{$lastRow}");


            error_log("UPload error log .$lastRow");

            $ArrProductData = [];// 빈배열로 초기화

            for ($i = 0; $i < count($ProductData); $i++) { // count($ProductData) = 요소 수 (행의 갯수)
                if (empty($ProductData[$i][0])) {
                    continue;
                }
                $PrTempData = [
                    "ProductName" => $ProductData[$i][0],                   // 상품명
                    "Specification" => $ProductData[$i][1],                 // 규격
                    "Unit" => $ProductData[$i][2],                          // 단위
                    "Company" => $ProductData[$i][3],                       // 제조회사
                    "ConvertedPrice" => $ProductData[$i][4],                // 환산약가
                    "ConvertedQuantity" => $ProductData[$i][5],             // 환산수량
                    "StandardCode" => $ProductData[$i][6],                  // 표준코드
                    "SpecialtyDivision" => $ProductData[$i][7],              // 전문구분
                    "InsuranceClassification" => $ProductData[$i][8],       // 보험구분
                    "SpecificItem" => $ProductData[$i][9],                  // 특정품목
                    "InvoiceIssue" => $ProductData[$i][10],                 // 계산서발행
                    "TransactionStatus" => $ProductData[$i][11],            // 거래여부
                    "SupplySubmission" => $ProductData[$i][12],           // 공급내역제출
                    "DosageForm" => $ProductData[$i][13],                   // 제형구분
                    "MedicineType" => $ProductData[$i][14],                 // 약품구분
                    "ProductCode" => $ProductData[$i][15],                  // 상품코드
                    "EnglishName" => $ProductData[$i][16],                 // 영문명
                    "Remarks" => $ProductData[$i][17],                      // 비고
                    "SellerName" => $ProductData[$i][18],                   // 판매처명
                    "InsurancePrice" => $ProductData[$i][19],               // 보험약가
                    "RotationDays" => $ProductData[$i][20],               // 기준회전일
                    "InsuranceCode" => $ProductData[$i][21],                // 보험코드
                    "ItemClassification" => $ProductData[$i][22],           // 품목구분
                    "PurchasePrice" => $ProductData[$i][23],                // 매입단가
                    "DrugInfo" => $ProductData[$i][24],                     // 마약정보
                ];

                $ArrProductData[] = $PrTempData;//배열의 끝에 추가방식 단일요소 x

            }

            // 데이터베이스 처리
            $db = db_connect();
            $ProductModel = new ProductModel();



            $db->transBegin(); // 트랜잭션 시작

            // 1. 기존 데이터 삭제
            $ProductModel->builder()->truncate(); // 모든 데이터 삭제

            // 2. 새로운 데이터 일괄 삽입
            $ProductModel->builder()->insertBatch($ArrProductData);

            $db->transComplete(); // 트랜잭션 종료

            if ( $db->transStatus() === false) {
                $db->transRollback();
                ut_ProcError("데이터 저장 중 오류 발생");
            } else {
                ut_procSuccess();
            }
            exit();

        } catch (\Exception $e) {
            log_message('error', '엑셀 파일 처리 오류: ' . $e->getMessage());
            ut_ProcError("엑셀 파일 처리 중 오류가 발생했습니다.");
            exit();
        }
    }

    public function dupliExcel()
    {
        helper(['ut_result', 'check_session']);
        ini_set('memory_limit', '-1');
        check_session_login();

        $inputData = $this->request->getPost();
        $validation = \Config\Services::validation();
        $validation->setRules([
            'uploadfile' => ['label' => '엑셀파일', 'rules' => 'uploaded[uploadfile]'
                . '|mime_in[uploadfile, application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]'
            ],
        ]);

        if (!$validation->run($inputData)) {
            error_log("벨리데이션");
            $errorMsg = array_values($validation->getErrors());

            $errorJson = implode(",", $errorMsg);
            error_log("벨리데이션 : {$errorJson}");
            ut_ProcError($errorJson);
            exit();

        }
        $excelFile = $this->request->getFile('uploadfile');
        // log_message('debug', '임시파일경로:' . $excelFile->getTempName());
        // log_message('debug', print_r($excelFile, true));
        // log_message('debug', print_r($_FILES, true));

        $reader = new Xlsx();

        try {
            $sheetName = "Sheet2"; // 읽을 시트 이름 , 파일명이 아님!!
            $reader = IOFactory::createReader('Xlsx');
            $reader->setLoadSheetsOnly($sheetName);

            // log_message('debug', 'Excel 파일 로드 시작');
            // log_message('debug', print_r($reader, true));
            $spreadSheet = $reader->load($excelFile->getTempName());
            //  log_message('debug', 'Excel 파일 로드 완료');
            // log_message('debug', print_r($spreadSheet, true));

            // A5부터 마지막 행까지의 데이터 범위를 지정
            $lastRow = $spreadSheet->getActiveSheet()->getHighestRow();
            $lastColumn = $spreadSheet->getActiveSheet()->getHighestColumn();
            // log_message('debug', '범위: A5:{$lastColumn}{$lastRow}');
            $ProductData = $spreadSheet->getActiveSheet()->rangeToArray("A5:{$lastColumn}{$lastRow}");
            // log_message('debug', print_r($ProductData, true));




            error_log("UPload error log .$lastRow");

            $ArrProductData = [];

            for ($i = 0; $i < count($ProductData); $i++) { // count($ProductData) = 요소 수 (행의 갯수)
                if (empty($ProductData[$i][0])) {
                    continue;
                }
                $PrTempData = [
                    "ProductName" => $ProductData[$i][0],                   // 상품명
                    "Specification" => $ProductData[$i][1],                 // 규격
                    "Unit" => $ProductData[$i][2],                          // 단위
                    "Company" => $ProductData[$i][3],                       // 제조회사
                    "ConvertedPrice" => str_replace(',','',$ProductData[$i][4]),                // 환산약가
                    "ConvertedQuantity" => str_replace(',','',$ProductData[$i][5]),             // 환산수량
                    "StandardCode" => $ProductData[$i][6],                  // 표준코드
                    "SpecialtyDivision" => $ProductData[$i][7],              // 전문구분
                    "InsuranceClassification" => $ProductData[$i][8],       // 보험구분
                    "SpecificItem" => $ProductData[$i][9],                  // 특정품목
                    "InvoiceIssue" => $ProductData[$i][10],                 // 계산서발행
                    "TransactionStatus" => $ProductData[$i][11],            // 거래여부
                    "SupplySubmission" => $ProductData[$i][12],           // 공급내역제출
                    "DosageForm" => $ProductData[$i][13],                   // 제형구분
                    "MedicineType" => $ProductData[$i][14],                 // 약품구분
                    "ProductCode" => $ProductData[$i][15],                  // 상품코드
                    "EnglishName" => $ProductData[$i][16],                 // 영문명
                    "Remarks" => $ProductData[$i][17],                      // 비고
                    "SellerName" => $ProductData[$i][18],                   // 판매처명
                    "InsurancePrice" => str_replace(',','',$ProductData[$i][19]),               // 보험약가
                    "RotationDays" => $ProductData[$i][20],               // 기준회전일
                    "InsuranceCode" => $ProductData[$i][21],                // 보험코드
                    "ItemClassification" => $ProductData[$i][22],           // 품목구분
                    "PurchasePrice" => $ProductData[$i][23],                // 매입단가
                    "DrugInfo" => $ProductData[$i][24],                     // 마약정보
                ];

                $ArrProductData[] = $PrTempData;

            }

            $productModel = new ProductModel();
            $productModel->saveProducts($ArrProductData);



            ut_procSuccess();
            exit();

        } catch (\Exception $e) {
            log_message('error', '엑셀 파일 처리 오류: ' . $e->getMessage());
            ut_ProcError("엑셀 파일 처리 중 오류가 발생했습니다.");
            exit();
        }


    }

    //데이터 삽입
    public function Insert()
    {
        helper(['ut_result', 'check_session']);
        check_session_login();




        try {
            $reqData = $this->request->getJSON(true);
            $validation = \Config\Services::validation();
            log_message('debug', print_r($reqData, true));
            $validation->setRules([
                'ProductCode' => ['label' => '상품코드', 'rules' => 'required'],
                'ProductName' => ['label' => '상품명', 'rules' => 'permit_empty'],
                'Specification'            => ['label' => '규격','rules' => 'permit_empty'],
                'Unit'                     => ['label' => '단위','rules' => 'permit_empty'],
                'Company'                  => ['label' => '제조회사', 'rules' => 'permit_empty'],
                'ConvertedPrice'           => ['label' => '환산약가', 'rules' => 'permit_empty'],
                'ConvertedQuantity'        => ['label' => '환산수', 'rules' => 'permit_empty'],
                'StandardCode'             => ['label' => '표준코드','rules' => 'permit_empty'],
                'SpecialtyDivision'        => ['label' => '전문구분', 'rules' => 'permit_empty'],
                'InsuranceClassification'  => ['label' => '보험구분', 'rules' => 'permit_empty'],
                'SpecificItem'             => ['label' => '특정품목', 'rules' => 'permit_empty'],
                'InvoiceIssue'             => ['label' => '계산서발행', 'rules' => 'permit_empty'],
                'TransactionStatus'        => ['label' => '거래여부', 'rules' => 'permit_empty'],
                'SupplySubmission'         => ['label' => '공급내역제출', 'rules' => 'required'],
                'DosageForm'               => ['label' => '제형구분', 'rules' => 'permit_empty'],
                'MedicineType'             => ['label' => '약품구분', 'rules' => 'permit_empty'],
                'EnglishName'              => ['label' => '영문명','rules' => 'permit_empty'],
                'Remarks'                  => ['label' => '비고','rules' => 'permit_empty'],
                'SellerName'               => ['label' => '판매처명','rules' => 'permit_empty'],
                'InsurancePrice'           => ['label' => '보험약가','rules' => 'permit_empty'],
                'RotationDays'             => ['label' => '기준회전일','rules' => 'permit_empty'],
                'InsuranceCode'            => ['label' => '보험코드','rules' => 'permit_empty'],
                'ItemClassification'       => ['label' => '품목구분','rules' => 'permit_empty'],
                'PurchasePrice'            => ['label' => '매입단가','rules' => 'permit_empty'],
                'DrugInfo'                 => ['label' => '마약정보','rules' => 'permit_empty'],
                'Category_Name'            => ['label' => '분류명','rules' => 'permit_empty'],
                'Ingredient_Name'          => ['label' => '주성분명','rules' => 'permit_empty'],
                'Category_Number'          => ['label' => '분류번호','rules' => 'permit_empty'],
                'Ingredient_Code'          => ['label' => '주성분코드','rules' => 'permit_empty']
            ]);

            // $test = $reqData['PurchasePrice'] == null ?  null : $reqData['PurchasePrice'];

            if (!$validation->run($reqData)) {

                log_message('error', print_r($validation->getErrors(), true));
                ut_ProcError('삽입 정보를 다시 한번 확인해주세요');
                exit();
            }
            //상품코드 중복 검사 (primary key)
            $productModel = new ProductModel();
            if($productModel->where('ProductCode', $reqData['ProductCode'])->first()){
                ut_ProcError('중복된 상품코드가 존재합니다');
                exit();
            }


            $data = [
                'ProductCode' => $reqData['ProductCode'],
                'ProductName' => $reqData['ProductName'],
                'Specification' => $reqData['Specification'],
                'Unit' => $reqData['Unit'],
                'Company' =>  $reqData['Company'],
                'ConvertedPrice' => $reqData['ConvertedPrice'],
                'ConvertedQuantity' => $reqData['ConvertedQuantity'],
                'StandardCode' => $reqData['StandardCode'],
                'SpecialtyDivision' => $reqData['SpecialtyDivision'],
                'InsuranceClassification' => $reqData['InsuranceClassification'],
                'SpecificItem' => $reqData['SpecificItem'],
                'InvoiceIssue' => $reqData['InvoiceIssue'],
                'TransactionStatus' => $reqData['TransactionStatus'],
                'SupplySubmission' => $reqData['SupplySubmission'],
                'DosageForm' => $reqData['DosageForm'],
                'MedicineType' => $reqData['MedicineType'],
                'EnglishName' => $reqData['EnglishName'],
                'Remarks' => $reqData['Remarks'],
                'SellerName' => $reqData['SellerName'],
                'InsurancePrice' => $reqData['InsurancePrice'],
                'RotationDays' => $reqData['RotationDays'],
                'InsuranceCode' => $reqData['InsuranceCode'],
                'ItemClassification' => $reqData['ItemClassification'],
                'PurchasePrice' => $reqData['PurchasePrice'],
                'DrugInfo' =>  $reqData['DrugInfo'],
                'Category_Name' => $reqData['Category_Name'],
                'Ingredient_Name' => $reqData['Ingredient_Name'],
                'Category_Number' => $reqData['Category_Number'],
                'Ingredient_Code' => $reqData['Ingredient_Code']

            ];
            log_message('debug', print_r($data, true));



            $productModel->insert($data);
            ut_procSuccess();
            exit();

        } catch (\Exception $e) {
            log_message('error', '상품 수정 중 오류 발생: ' . $e->getMessage());
            ut_ProcError('상품 저장 중 오류가 발생하였습니다.');
            exit();
        }








    }


}