<?php

use \App\Models\CustomerModel;

function check_session_login() {
    $session = session();

    $isLogin = $session->get("admin_logged_in");

    if($isLogin != TRUE) {
       echo "<script> alert('접근 권한이 없습니다. 다시 로그인하여 정상적인 방법으로 접근하세요.'); location.replace('".base_url()."login'); </script>";
       exit;
    }
}


/**
 * 세션키 생성
 *
 * @param int $custIdx
 * @return string
 *
 */

function maskeSessionkey(int $custIdx): string
{
    return substr(hash('sha256', date('Y-m-d h:m:s', time()).substr(microtime(false), 2,3).$custIdx), 0, 32);
}

/**
 *  고객 인덱스, 세션키로 세션키가 DB에 저장된 것과 맞는지 비교함
 * @param int $custIdx
 * @param string $sessionKey
 *
 */

function check_mobile_sessionKey(int $custIdx, string $sessionKey)
{
    log_message('debug','======>' .__FUNCTION__);

    $customerModel = new CustomerModel();
    $res = $customerModel->find($custIdx);

    //검색해서 session 이 없거나 , session 값이 비교한거랑 다르면 세션 만료
    if (!isset($res['SessionKey']) || $res['SessionKey'] != $sessionKey) {
        ut_ProcError('세션이 만료되었습니다.', 'ERROR_SESSION_KEY');
        log_message('error','/////// 세션만료: '.$custIdx);
        exit();
    }
}