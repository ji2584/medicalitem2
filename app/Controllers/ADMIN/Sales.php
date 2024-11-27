<?php

namespace App\Controllers\ADMIN;

use App\Controllers\BaseController;
use App\Models\SalesModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;


class Sales extends BaseController
{

    public function index(): string
    {
        helper(['check_session']);
        $session = session();
        check_session_login();

        $data['title'] = '메인 페이지';
        $data['session'] = $session->get("username");

        return view('content/sales', $data);
    }


    public function uploadSale()
    {
        helper(['ut_result', 'check_session']);
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

        if (!$validation->run($inputData)) {
            log_message('errror', print_r($validation->getErrors(), true));
            ut_ProcError('엑셀파일 유효성 검사 오류');
            exit();
        }

        $excelFile = $this->request->getFile('uploadfile');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');


        try {
            //엑셀 파일 읽기 시작
            $spreadSheet = $reader->load($excelFile->getTempName());

            //시트이름을 확인하여 파일 구분 및 러ㅣ
            $sheetNames = $spreadSheet->getSheetNames(); // 모든 시트이름을 배열로 반환
            log_message('debug', print_r($sheetNames, true));
            if (in_array('Sales', $sheetNames)) {
                //영도소아.xlsx sheet 이름
                $this->processSalesExcel($spreadSheet);
            } else {
                ut_ProcError('올바른 시트가 없습니다. Sales 시트가 필요합니다');
                exit();

            }

            ut_procSuccess();
            exit();


        } catch (\Exception $e) {
            log_message('error', '엑셀 파일 로드 실패: ' . $e->getMessage());
            ut_ProcError('엑셀파일 로드중 문제가 발생했습니다.' . $e->getMessage());
            exit();
        }

    }

    //매출현황 엑셀 파일 업로드 저장
    public function processSalesExcel($spreadSheet)
    {
        helper(['check_session', 'ut_result']);
        check_session_login();

        try {
            $sheet = $spreadSheet->getActiveSheet();
            $lastRow = $sheet->getHighestRow() - 1;
            $lastColumn = $sheet->getHighestColumn();
            $salesData = $sheet->rangeToArray("A5:{$lastColumn}{$lastRow}");
            log_message('debug', "데이터 범위: A5:{$lastColumn}{$lastRow}");
            log_message('debug', "추출된 데이터 내용: " . print_r($salesData, true));

            $ArrSalesData = []; //변수 초기화

            for ($i = 0; $i < count($salesData); $i++) {
                //엑셀 스프레드 시트를 읽을떄 날짜 데이터를 자동으로  m/d/y 형식으로 읽어서 y-m-d로 바꿔준다
                $transDate = \DateTime::createFromFormat('m/d/Y', $salesData[$i][0]);
                $salesDate = \DateTime::createFromFormat('m/d/Y', $salesData[$i][1]);

                $PrTempData = [
                    "Trans_Date" => $transDate ? $transDate->format('Y-m-d') : null, // 수불일자
                    "Sales_Date" => $salesDate ? $salesDate->format('Y-m-d') : null, // 매출일자
                    "InstName" => $salesData[$i][2],                 //거래처
                    "Functionality" => $salesData[$i][3],           //기능
                    "Sales_ProductName" => $salesData[$i][4],       //상품명
                    "Sales_Specification" => $salesData[$i][5],     //규격
                    "Sales_Unit" => $salesData[$i][6],              //단위
                    "Sales_Manufacturer" => $salesData[$i][7],      //제조회사
                    "Sales_UnitPrice" => str_replace(',', '', $salesData[$i][8]),       // 단가
                    "Sales_Quantity" => str_replace(',', '', $salesData[$i][9]),      // 수량
                    "Sales_TotalAmount" => str_replace(',', '', $salesData[$i][10]),   // 금액
                    "Sales_InsurancePrice" => str_replace(',', '', $salesData[$i][11]), // 보험약가
                    "Sales_InsuranceCode" => $salesData[$i][12],    //보험코드
                    "Sales_Remarks" => $salesData[$i][13],          //적요
                ];

                $ArrSalesData[] = $PrTempData;
            }
            log_message('debug', print_r($ArrSalesData, true));
            if (empty($ArrSalesData)) {
                ut_ProcError("엑셀 데이터를 추출하지 못햇습니다");
                exit();
            }

            $db = db_connect();
            $SalesModel = new SalesModel();

            $db->transBegin();

            $SalesModel->builder()->truncate(); // 모든 데이터 삭제
            $SalesModel->builder()->insertBatch($ArrSalesData);
            $db->transComplete();

            if ($db->transStatus() === false) {
                $db->transRollback();
                ut_ProcError("데이터 저장 중 오류 발생");
            } else {
                ut_procSuccess();
            }
            exit();

        } catch (\Exception $e) {
            log_message('error', 'processSalesExcel 오류: ' . $e->getMessage());
            ut_ProcError('processSalesExcel 오류: ' . $e->getMessage());
            exit();
        }
    }


    // 데이터테이블 리스트 출력
    public function SalesList()
    {
        helper(['check_session', 'ut_result']);
        check_session_login();


        try {
            $SalesModel = new SalesModel();
        /*    $adminType = session()->get('AdminType');
            $adminInstIdx = session()->get('AdminInstIdx');*/



         /*   // 일반 관리자(`A`)는 InstIdx로 필터링
            if ($adminType === 'A') {
                $SalesModel->select('Sales.*, Institute.InstIdx')
                    ->join('Institute', 'Sales.InstName = Institute.InstName')
                    ->where('Institute.InstIdx', $adminInstIdx);
            }*/
            //DataTables 요청
            $dataTable = $this->request->getPost();

            if (!empty($dataTable)) {
                $draw = $dataTable['draw'];
                $start = $dataTable['start'];
                $length = $dataTable['length'];
                $searchValue = $dataTable['searchValue'] ?? '';

            } else {
                $draw = null;
                $length = null;
                $start = null;
            }

            log_message('debug', print_r($dataTable, true));

            //전체 레코드 수 및 필터된 레코드수
            $totalRecords = $SalesModel->builder()->countAll();


            // 검색 조건 적용: 상품명 or 제조회사에서 검색어 포함되는 항목
            if (!empty($searchValue)) {
                $SalesModel->groupStart()
                    ->like('Sales_ProductName', $searchValue)
                    ->orLike('Sales_Manufacturer', $searchValue)
                    ->groupEnd();
            }//WHERE (ProductName LIKE '%searchValue%' OR Company LIKE '%searchValue%')

            $filteredRecords = $SalesModel->countAllResults(false); //false 추가 query 작업 필요 초기화 x
            // 페이지당 데이터 가져오기
            $data = $SalesModel->findAll($length, $start);
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

    // 체크박스 선택 삭제
    public function SalesDelete()
    {
        helper(['check_session', 'ut_result']);
        check_session_login();

        try {

            $reqData = $this->request->getJSON(true);
            $validation = \Config\Services::validation();
            $validation->setRules([
                'SalesIdx' => ['label' => '매출id', 'rules' => 'required'],
            ]);

            if (!$validation->run($reqData)) {
                $errMsg = $validation->getErrors();
                log_message('error', 'Server delete Validation ERROR');
                log_message('error', print_r($errMsg, true));
                exit();
            }

            $SalesModel = new SalesModel();

            $SalesModel->whereIn('SalesIdx', $reqData['SalesIdx'])->delete();

            ut_procSuccess();
            exit();


        } catch (\Exception $e) {
            ut_ProcError('삭제중 문제발생');
            log_message('error', print_r($e, true));
            exit();
        }


    }

    //매출 현황 수정 기능
    public function SalesUpdate()
    {
        helper(['check_session', 'ut_result']);
        check_session_login();


        try {
            $reqData = $this->request->getJSON(true);
            log_message('debug',print_r($reqData, true));
            $validation = \Config\Services::validation();
            $validation->setRules([
                'SalesIdx' => ['label' => '상품Id', 'rules' => 'required'],
                'Trans_Date' => ['label' => '수불일자', 'rules' => 'required'],
                'Sales_Date' => ['label' => '매출일자', 'rules' => 'required'],
                'InstName' => ['label' => '거래처', 'rules' => 'required'],
                'Functionality' => ['label' => '기능', 'rules' => 'required'],
                'Sales_ProductName' => ['label' => '상품명', 'rules' => 'required'],
                'Sales_Specification' => ['label' => '규격', 'rules' => 'permit_empty'],
                'Sales_Unit' => ['label' => '단위', 'rules' => 'permit_empty'],
                'Sales_Manufacturer' => ['label' => '제조회사', 'rules' => 'required'],
                'Sales_UnitPrice' => ['label' => '단가', 'rules' => 'required'],
                'Sales_Quantity' => ['label' => '수량', 'rules' => 'required'],
                'Sales_TotalAmount' => ['label' => '금액', 'rules' => 'required'],
                'Sales_InsurancePrice' => ['label' => '보험약가', 'rules' => 'required'],
                'Sales_InsuranceCode' => ['label' => '보험코드', 'rules' => 'permit_empty'],
                'Sales_Remarks' => ['label' => '적요', 'rules' => 'permit_empty']
            ]);

            if (!$validation->run($reqData)) {

                log_message('error', 'Server update Validation ERROR');
                ut_ProcError('수정 정보를 다시 확인해주세요');
                exit();
            }

            $data = [

                'Trans_Date' => $reqData['Trans_Date'],
                'Sales_Date' => $reqData['Sales_Date'],
                'InstName' => $reqData['InstName'],
                'Functionality' => $reqData['Functionality'],
                'Sales_ProductName' => $reqData['Sales_ProductName'],
                'Sales_Specification' => $reqData['Sales_Specification'],
                'Sales_Unit' => $reqData['Sales_Unit'],
                'Sales_Manufacturer' => $reqData['Sales_Manufacturer'],
                'Sales_UnitPrice' => $reqData['Sales_UnitPrice'],
                'Sales_Quantity' => $reqData['Sales_Quantity'],
                'Sales_TotalAmount' => $reqData['Sales_TotalAmount'],
                'Sales_InsurancePrice' => $reqData['Sales_InsurancePrice'],
                'Sales_InsuranceCode' => $reqData['Sales_InsuranceCode'],
                'Sales_Remarks' => $reqData['Sales_Remarks'],


            ];

            $SalesModel = new SalesModel();

            $SalesModel->update($reqData['SalesIdx'], $data);
            ut_procSuccess();
            exit();

        } catch (\Exception $e) {
            log_message('error', '상품 수정 중 오류 발생: ' . $e->getMessage());
            ut_ProcError('상품 저장 중 오류가 발생하였습니다.');
            exit();
        }
    }


    //매출현황 삽입버튼

    public function SalesInsert()
    {
        helper(['check_session', 'ut_result']);
        check_session_login();


        try {
            $reqData = $this->request->getJSON(true);
            $validation = \Config\Services::validation();
            $validation->setRules([
                'Trans_Date' => ['label' => '수불일자', 'rules' => 'required'],
                'Sales_Date' => ['label' => '매출일자', 'rules' => 'required'],
                'InstName' => ['label' => '거래처', 'rules' => 'required'],
                'Functionality' => ['label' => '기능', 'rules' => 'required'],
                'Sales_ProductName' => ['label' => '상품명', 'rules' => 'required'],
                'Sales_Specification' => ['label' => '규격', 'rules' => 'permit_empty'],
                'Sales_Unit' => ['label' => '단위', 'rules' => 'permit_empty'],
                'Sales_Manufacturer' => ['label' => '제조회사', 'rules' => 'required'],
                'Sales_UnitPrice' => ['label' => '단가', 'rules' => 'required'],
                'Sales_Quantity' => ['label' => '수량', 'rules' => 'required'],
                'Sales_TotalAmount' => ['label' => '금액', 'rules' => 'required'],
                'Sales_InsurancePrice' => ['label' => '보험약가', 'rules' => 'required'],
                'Sales_InsuranceCode' => ['label' => '보험코드', 'rules' => 'permit_empty'],
                'Sales_Remarks' => ['label' => '적요', 'rules' => 'permit_empty']
            ]);

            if (!$validation->run($reqData)) {
                log_message('error', 'Server insert Validation ERROR'.print_r($validation, true));
                ut_ProcError('Server insert Validation ERROR');
                exit();
            }


            $data = [


                'Trans_Date' => $reqData['Trans_Date'],
                'Sales_Date' => $reqData['Sales_Date'],
                'InstName' => $reqData['InstName'],
                'Functionality' => $reqData['Functionality'],
                'Sales_ProductName' => $reqData['Sales_ProductName'],
                'Sales_Specification' => $reqData['Sales_Specification'],
                'Sales_Unit' => $reqData['Sales_Unit'],
                'Sales_Manufacturer' => $reqData['Sales_Manufacturer'],
                'Sales_UnitPrice' => $reqData['Sales_UnitPrice'],
                'Sales_Quantity' => $reqData['Sales_Quantity'],
                'Sales_TotalAmount' => $reqData['Sales_TotalAmount'],
                'Sales_InsurancePrice' => $reqData['Sales_InsurancePrice'],
                'Sales_InsuranceCode' => $reqData['Sales_InsuranceCode'],
                'Sales_Remarks' => $reqData['Sales_Remarks'],


            ];


            $db = db_connect();


            $db->transBegin();
            $SalesModel = new SalesModel();

            $SalesModel->insert($data);
            $db->transComplete();

            if ($db->transStatus() === false) {
                $db->transRollback();
                ut_ProcError("데이터 저장 중 오류 발생");
            } else {
                ut_procSuccess();
            }
            exit();

        } catch (\Exception $e) {
            log_message('error', 'Server insert Validation ERROR: ' . $e->getMessage());
            ut_ProcError('Server insert Validation ERROR');
            exit();

        }


    }
}

