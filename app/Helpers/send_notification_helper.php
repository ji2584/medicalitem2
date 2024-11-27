<?php

if (! function_exists('call_post_api')) {
    /**
     * 다른 웹 사이트 또는 서버와 통신합니다.
     *
     * CI4 에서 CURLRequest 라이브러리를 제공하고 있긴 합니다.
     * http://ci4doc.cikorea.net/libraries/curlrequest.html
     *
     * @param string $url 주소
     * @param array $headers HTTP 헤더
     * @param array $data 전송할 POST 데이터
     * @return bool|string 통신에 성공하면 결과 문자열, 실패하면 false
     */
    function call_post_api(string $url, array $headers, array $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // curl_exec 실행 결과를 출력하지 않고 반환
        curl_setopt($ch, CURLOPT_POST, true);    // true 면, post 요청
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        log_message('debug', print_r($response, true));
        log_message('debug', print_r(curl_getinfo($ch), true));

        if ($response !== false) {
            return $response;
        } else {
            log_message('error', '오류 #'.curl_errno($ch));
            log_message('error', 'cURL 오류입니다. '.curl_error($ch));
            return false;
        }
    }
}


if (! function_exists('send_alimtalk')) {
    /**
     * 비즈엠 API 를 통해 알림톡 전송을 요청합니다.
     *
     * @param string $phn 전송할 전화번호
     * @param int $type 알림톡 메시지 구분자
     * @param array $data 추가적으로 필요한 데이터 연관 배열, 예) ['CustIdx' => 777]
     * @return bool 전송 성공 여부
     */
    function send_alimtalk(string $phn, int $type = 0, array $data = []): bool
    {
        $header = ['Content-Type: application/json', 'userId: dkbnrt'];
        $url = 'https://alimtalk-api.bizmsg.kr/v2/sender/send';

        // 테스트
        $message = [];
        switch ($type) {

            case 0:

                // TODO 임시로 '키니스쿨' 프로필로 알림톡 발송

                if (empty($data)) {
                    log_message('error', '필요한 데이터가 없습니다!');
                    return false;
                }

                $message[0] = [
                    'message_type' => 'at',
                    'phn' => $phn,
                    'profile' => 'd29b75667a62cd491b83e490d9dd469a287d730a',
                    'reserveDt' => '00000000000000',
                    'msg' => "안녕하세요. {$data['GuardianName']} 님😊

국가대표 선수들의 운동 코칭 콘텐츠를제공하는 국대들 입니다.

{$data['CustName']} 님께서 국대들 앱에 회원가입을 하고자 합니다.
국대들 앱에서는 회원가입 단계에서 간편로그인을 통해
사용자의 개인정보를 수집하고 있습니다.
「개인정보 보호법」에 따르면, 만 14세 미만이 회원가입 하기 위해서는
법정대리인의 동의를 받아야 하고, 법정대리인이
동의하였는지 확인하여야 합니다.

아래와 같은 내용을 확인하신 뒤, 이상이 없으시다면 아래
[동의합니다.] 버튼을 눌러주세요!
[동의합니다.] 버튼을 누르시면 {$data['CustName']} 님의
회원가입 절차가 완료됩니다.

** 문의사항이 있으신 경우, 채널톡으로 문의하지 마시고,
pdkland@dkbnrt.com 으로 메일 주시면 답변드리도록 하겠습니다. **",
                    'tmplId' => 'guardian_agree_20240822',
                    'button1' => [
                        'name' => '서비스 이용약관 확인',
                        'type' => 'WL',
                        'url_mobile' => "https://" . $data['Domain'] . "/teamkookdae_terms/1.0/serviceAgree.html"

                    ],
                    'button2' => [
                        'name' => '개인정보수집 및 이용 확인',
                        'type' => 'WL',
                        'url_mobile' => "https://" . $data['Domain'] . "/teamkookdae_terms/1.0/privateCollectAgree.html"
                    ],
                    'button3' => [
                        'name' => '동의합니다.',
                        'type' => 'WL',
                        'url_mobile' => "https://" . $data['Domain'] . "/teamkookdae/agree-guardian-view/". $data['AgreeIdx']
                    ]
                ];
        }

        $result = call_post_api($url, $header, $message);

        if ($result !== false) {
            $result = json_decode($result);
            log_message('debug', print_r($result, true));

            $result = (is_array($result)) ? $result[0] : $result;

            if ($result->code !== 'fail') {
                return true;
            } else {
                log_message('error', print_r($result, true));
                return false;
            }

        } else {
            return false;
        }
    }
}