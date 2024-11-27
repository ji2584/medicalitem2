<?php

namespace App\Filters;


use App\Models\MobileSessionModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\AES256Cipher;
use Config\TkookdaeConfig;
use Exception;

class MobileAuthFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {

        // 테스트
        $data = [
            'Today'      => date('Y-m-d'),
            'RequestURL' => $request->getUri()->getPath(),
            'AppVer'     => '1.0.0',
            'Platform'   => 'IOS'
        ];

        try {

            helper('ut_result');

            $aes256cipher = new AES256Cipher();

            $temp = $aes256cipher->encode('MOBILE_AUTH', json_encode($data));

            log_message('debug', 'Test Auth: ' . $temp);
            log_message('debug', 'RequestURL: '. $request->getUri()->getPath());

            // Header 에서 'Auth' 값 추출
            $auth = $request->getHeaderLine('Auth');

            log_message('debug', 'Current Auth: ' . $auth);

            // 복호화
            $dataStr = $aes256cipher->decode('MOBILE_AUTH', $auth);

            $authData = json_decode($dataStr, true);

            // 검증
            // 앱에서 받은 오늘 날짜와 서버 오늘 날짜가 동일한지
            // 유효한 RequestURL 인지 - 복호화 데이터의 RequestURL, request 객체의 path 가 일치하는지
            // 검증 통과 시, DB 저장
            if (date('Y-m-d') !== $authData['Today'] || $request->getUri()->getPath() !== $authData['RequestURL']) {
                $errormsg = $request->getUri()->getPath(). " 와 " .$authData['RequestURL']. " 이 일치하지 않음 ";

                log_message('error', $errormsg);

                ut_ProcError('error', '데이터 유효성 검사에 실패하였습니다.');
                exit();
            }

            $mobileSessionModel = new MobileSessionModel();

            $mobileSessionModel->insert(
                [
                    'CustIdx' => $authData['CustIdx'] ?? null,
                    'RequestURL' => $authData['RequestURL'],
                    'IpAddress' => $request->getIPAddress(),
                    'AppVer' => $authData['AppVer'],
                    'Platform' => $authData['Platform']
                ]
            );

            // 1. 서비스점검체크, 2. 앱업데이트체크
            $conf = new TkookdaeConfig();
            $serverStatus = $conf->IS_SERVER_CHECK;
            $neededVer    = $conf->APP_NEEDED_VER[$authData['Platform']];

            // 서비스 점검 체크 (서버상태값 변수 - TEST_IP 는 넘겨줌)
            if ($serverStatus) {
                $testIPs = $conf->TEST_IP;

                if (!in_array($request->getIPAddress(), $testIPs)) {

                    $msg = $conf->SERVICE_UPDATE_MSG;
                    ut_ProcError(json_encode($msg, JSON_UNESCAPED_UNICODE), 'SERVICE_MAINTENANCE');
                    exit();

                } else {

                    log_message('error', '현재 서버 점검중 - 사용가능 관계자 IP라 통과됨');
                }

            } elseif (isHigherVer($neededVer, $authData['AppVer'])) {

                ut_ProcError('앱 업데이트가 필요합니다.', 'ERROR_NEEDED_UPDATE');
                exit();
            }

            // Filter Pass
            $request->AppVer   = $authData['AppVer'];
            $request->Platform = $authData['Platform'];

        } catch (Exception $e) {

            log_message('error', print_r($e, true));
            ut_ProcError('죄송합니다. 문제가 발생했습니다.');
            exit();

        }

    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {

    }
}