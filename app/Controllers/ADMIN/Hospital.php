<?php

namespace App\Controllers\ADMIN;

use App\Controllers\BaseController;
use App\Models\HospitalModel;


class Hospital extends BaseController
{


    public function index(): string
    {
        helper(['check_session']);
        $session = session();
        check_session_login();

        $data['title'] = '병원 통계';
        $data['session'] = $session->get("username");

        return view('content/hospital', $data);
    }


    public function SearchYearMonthHospital()
    {

        helper(['check_session', 'ut_result']);
        check_session_login();
        $session = session();
        $adminType = $session->get('AdminType');

        try {
            $reqData = $this->request->getPost();
            $searchValue = $reqData['searchValue'];
            log_message('debug', '////////////////////' . print_r($reqData, true));


            // AdminType이 'A'일 경우 빈 데이터 반환
            if ($adminType === 'A') {
                return $this->response->setJSON([
                    'data' => []
                ]);
            }


            $hospitalModel = new HospitalModel();

            if ($searchValue) {
                $resData = $hospitalModel->getyearMonthHospital($searchValue);
            } else {
                $resData = [];
            }


            log_message('debug', print_r($resData, true));
            ut_procSuccess($resData);
            exit();


        } catch (\Exception $e) {
            log_message('error', 'MonthlyList 조회 오류: ' . $e->getMessage());
            ut_ProcError('SearchYearMonthHospitalList 조회 오류');
            exit();
        }


    }

    //월 상세 내역
    public function MonthlyHospital()
    {
        helper(['check_session', 'ut_result']);
        check_session_login();

        try {
            $reqData = $this->request->getPost();
            $yearMonth = $reqData['yearMonth'];
            log_message('debug', '////////////////////' . print_r($yearMonth, true));

            $hospitalModel = new HospitalModel();

            //월 별 상세 데이터 가져오기
            $resData=$hospitalModel->getMonthlyHospital($yearMonth);

            //평균 수량 계산
            $processData = $hospitalModel->calculateAverageQuantities($resData);

            ut_procSuccess($processData);
            log_message('debug','----------------------'.print_r($processData, true));



            //log_message('debug', '////////////////////' . print_r($firstSalesMonths, true));
            exit();


        } catch (\Exception $e) {
            ut_ProcError('MonthlyHospital 조회 오류');
            exit();
        }


    }





}