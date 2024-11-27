<?php

namespace App\Controllers\ADMIN;

use App\Controllers\BaseController;
use App\Models\SalesModel;

class Overview extends BaseController
{

    public function index(): string
    {
        helper(['check_session']);
        $session = session();
        check_session_login();

        $data['title'] = '매출현황 통계';
        $data['session'] = $session->get("username");

        return view('content/overview', $data);
    }


    ///연간 데이터
    public function YearList()
    {

        helper(['check_session', 'ut_result']);
        check_session_login();
        $session = session();
        $adminType = $session->get('AdminType');

        try {

            $SalesModel = new SalesModel();

            // AdminType이 'A'일 경우 빈 데이터 반환
            if ($adminType === 'A') {
                return $this->response->setJSON([
                    'draw' => $this->request->getPost('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => []
                ]);
            }


            $dataTable = $this->request->getPost();
            if (!empty($dataTable)) {
                $draw = $dataTable["draw"];
                $start = intval($dataTable["start"]);
                $length = intval($dataTable["length"]);
                $searchValue = $dataTable["searchValue"];

            } else {
                $draw = null;
                $length = null;
                $start = null;

            }



            // 전체 레코드 수
            $totalRecords = $SalesModel->getTotalRecordsCount();

            // 필터링된 레코드 수
            $filteredRecords = $SalesModel->getFilteredRecordsCount($searchValue);

            // 연간 요약 데이터 가져오기
            $data = $SalesModel->getAnnualSalesSummary($searchValue, $length, $start);
            $resData = [
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ];
           log_message('debug','////////////////////////'.print_r($resData, true));

            ut_procSuccess($resData);

            exit();
        } catch (\Exception $e) {
            ut_ProcError('연간 리스트 조회문제 발생');
            exit();


        }


    }




    public function MonthlyList()
    {
        helper(['check_session', 'ut_result']);
        check_session_login();
        $session = session();
        $adminType = $session->get('AdminType');

        try {
            $SalesModel = new SalesModel();
            $dataTable = $this->request->getPost();

            // AdminType이 'A'일 경우 빈 데이터 반환
            if ($adminType === 'A') {
                return $this->response->setJSON([
                    'data' => []
                ]);
            }

            //선택된 상품명을 받아오기
            $productName = $dataTable["productName"]; //Monthlyoverviewlist 에서 준 d.productName
            if(empty($productName)){
               return $this->response->setJSON([
                   'data' => []
               ]);
            }

            // 상품명에 대한 월별 요약 쿼리 수행
            $data = $SalesModel->getMonthlyDataByProduct($productName);


            $checkmonth = null; // 첫 i 값
            $firstQuantity = null; // 수량
            $countmonth = 1;  // 개월 수 카운트
            $a = null;  // 수량평균값
            $b = null; // 마지막 j 값 , 첫매출 이후 다음 매출을 수량이 있기 전달
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['TotalQuantity'] != 0) {
                    $firstQuantity = $data[$i]['TotalQuantity'];
                    $data[$i]['AvgQuantity'] = $firstQuantity;
                    $checkmonth = $i; //매출을 발견한 달
                    for ($j = $i + 1; $j < count($data); $j++) {
                        if ($data[$j]['TotalQuantity'] != 0) { //매출 연속 유무
                            break;
                        } else {
                            // 0 이면
                            $b = $j;
                            $countmonth++;
                        }
                    }
                    if ($b !== null) {
                        $a = $firstQuantity / $countmonth;  // 평균값
                        //$a값 소수점인 경우 2자리로 포맷
                         $a = floor($a) != $a ? number_format($a,2) : $a;

                        for ($k = $checkmonth; $k <= $b; $k++) {
                            $data[$k]['AvgQuantity'] = $a;
                        }
                        $countmonth = 1;
                        $b = null;
                    }
                } elseif ($checkmonth === null) {
                    $data[$i]['AvgQuantity'] = 0;
                }
            }

            $resData = [
                'data' => $data
            ];

            log_message('debug', print_r($data,true));

            log_message('debug', print_r($resData, true));

            ut_procSuccess($resData);
            exit();



        } catch (\Exception $e) {
            log_message('error', 'MonthlyList 조회 오류: ' . $e->getMessage());
            ut_ProcError('MonthlyList 조회 오류');
            exit();
        }





    }


    //test////////////////////
    public function getAnnualData()
    {

        helper(['check_session', 'ut_result']);
        check_session_login();

        try {
            $reqData = $this->request->getJSON(true);
            $productName = $reqData["productName"];

            log_message('debug', print_r($productName, true));


            $SalesModel = new SalesModel();
            $data = $SalesModel->getMonthlyDataByProduct($productName);

            $checkmonth = null; // 첫 i 값
            $firstQuantity = null; // 수량
            $countmonth = 1;  // 개월 수 카운트
            $a = null;  // 수량평균값
            $b = null; // 마지막 j 값 , 첫매출 이후 다음 매출을 수량이 있기 전달
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['TotalQuantity'] != 0) {
                    $firstQuantity = $data[$i]['TotalQuantity'];
                    $data[$i]['AvgQuantity'] = $firstQuantity;
                    $checkmonth = $i; //매출을 발견한 달
                    for ($j = $i + 1; $j < count($data); $j++) {
                        if ($data[$j]['TotalQuantity'] != 0) { //매출 연속 유무
                            break;
                        } else {
                            // 0 이면
                            $b = $j;
                            $countmonth++;
                        }
                    }
                    if ($b !== null) {
                        $a = $firstQuantity / $countmonth;  // 평균값
                        //$a값 소수점인 경우 2자리로 포맷
                        $a = floor($a) != $a ? number_format($a, 2) : $a;

                        for ($k = $checkmonth; $k <= $b; $k++) {
                            $data[$k]['AvgQuantity'] = $a;
                        }
                        $countmonth = 1;
                        $b = null;
                    }
                } elseif ($checkmonth === null) {
                    $data[$i]['AvgQuantity'] = 0;
                }
            }


            log_message('debug', print_r($data, true));
            ut_procSuccess($data);
            exit();
        } catch (\Exception $e) {
            log_message('error', 'MonthlyList 조회 오류: ' . $e->getMessage());
            ut_ProcError('MonthlyList 조회 오류');
            exit();

        }


    }
}