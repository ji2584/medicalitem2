<?php

namespace App\Controllers\ADMIN;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class Auth extends BaseController
{

    // 로그인 뷰
    public function index(): string
    {
        return view('auth/login');
    }

    //로그인
    public function login()
    {
        helper('ut_result') ;

          try {
                $reqData = $this->request->getJSON(true);

                $validation = \Config\Services::validation();
                $validation->setRules([
                    'adminId' => ['label' => '관리자 아이디', 'rules' => 'required'],
                    'adminPwd' => ['label' => '관리자 비밀번호', 'rules' => 'required']

                ]);

                if (!$validation->run($reqData)) {
                    $errMsg = $validation->getErrors();
                    ut_ProcError('로그인 정보를 확인하세요');
                    log_message('error', 'SERVER LOGIN VALIDATION ERROR');
                    log_message('error', print_r($errMsg, true));
                    exit();
                }

                $adminId= $reqData['adminId'];
                $adminPwd = $reqData['adminPwd'];

                $adminModel = new AdminModel();
                $firstUser = $adminModel->where('AdminID',$adminId)->first();

                // 쿼리 실행값 없음
                if ($firstUser == null) {
                    ut_ProcError('계정이 존재하지 않습니다.');
                    exit();
                } elseif (!password_verify($adminPwd, $firstUser['AdminPass'])) {
                    ut_ProcError('비밀번호를 다시한번 확인해 주세요');
                    exit();
                }

                $sess = [
                        'admin_logged_in' => true,
                        'username' => $firstUser['AdminName'],
                        'AdminType' => $firstUser['AdminType'] // S or A
                    ];

                session()->set($sess);

                ut_procSuccess();
                exit();
          } catch (\Exception $e) {
              ut_ProcError('문제가 발생햇습니다.');
              log_message('error', print_r($e, true));
              exit();
          }
    }
}