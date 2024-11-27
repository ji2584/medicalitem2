<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (! function_exists('extract_youtube_vid')) {

    /**
     * YouTube Video URL 에서 vid 추출
     *
     * https://youtube.com/shorts/t9wEt3vbl_g
     * https://youtu.be/T-VYK4N1Lvw
     *
     * @param string $url YouTube Video URL
     * @return string
     */
    function extract_youtube_vid(string $url): ?string
    {
        $parsedUrl = parse_url($url);

        // $parsedUrl
        // [scheme] => https
        // [host] => youtube.com
        // [path] => /shorts/t9wEt3vbl_g
        //
        // [scheme] => https
        // [host] => youtu.be
        // [path] => /T-VYK4N1Lvw

        // Check if the host contains 'youtube.com' or 'youtu.be'
        if (isset($parsedUrl['host']) && (strpos($parsedUrl['host'], 'youtube.com') !== false || strpos($parsedUrl['host'], 'youtu.be') !== false)) {

            // If it's a YouTube Shorts URL
            if (strpos($parsedUrl['path'], '/shorts/') !== false) {
                $segments = explode('/', trim($parsedUrl['path'], '/'));
                return $segments[count($segments) - 1];
            }

            // If it's a regular YouTube URL (e.g., youtu.be/T-VYK4N1Lvw)
            if (strpos($parsedUrl['host'], 'youtu.be') !== false) {
                return ltrim($parsedUrl['path'], '/');
            }
        }

        return false; // If the URL is not valid
    }
}

if (! function_exists('round_up')) {

    /**
     * 소수점 $precision 에서 올림하여 $precision - 1 자리까지 표시
     *
     * @param $number
     * @param int $precision
     * @return float
     */
    function round_up($number, int $precision = 2): float
    {
        $fig = (int) str_pad('1', $precision, '0');
        return (ceil($number * $fig) / $fig);
    }
}

if (! function_exists('millis_to_datetime')) {

    /**
     * 밀리초를 Y-m-d H:i:s 
     * 기본 timezone 사용
     *
     * @param string $millis
     * @return string
     */
    function millis_to_datetime(string $millis): string
    {
        $seconds = $millis / 1000;
        return date('Y-m-d H:i:s', $seconds);
    }
}

if (! function_exists('sec_to_hms')) {

    /**
     * 초를 입력 받아
     * 시간, 분, 초로 분할하여 반환.
     * 0인 단위는 미표시
     *
     * @param int $paramSeconds
     * @return string
     */
    function sec_to_hms(int $paramSeconds): string
    {
        $hours = (int) ($paramSeconds / 3600);
        $minutes = (int) (($paramSeconds % 3600) / 60);
        $seconds = $paramSeconds % 60;

        return ($hours > 0 ? $hours . '시간 ' : '') .
            ($minutes > 0 ? $minutes . '분 ' : '') .
            ($seconds > 0 ? $seconds . '초' : '');
    }
}



if (! function_exists('convert_utc_to_timezone')) {

    /**
     * UTC 날짜와 시간 문자열을 지정된 타임존 날자 시간으로 변경
     *
     * @param string $datetime
     * @param string $timezone
     * @return string
     * @throws Exception
     */
    function convert_utc_to_timezone(string $datetime, string $timezone = 'Asia/Seoul'): string
    {
        $datetime = new \DateTime($datetime);  // DateTime 객체를 생성 (입력 문자열에 포함된 시간대를 자동으로 사용)
        $datetime->setTimezone(new \DateTimeZone($timezone));  // KST 시간대로 변환
        return $datetime->format('Y-m-d H:i:s');
    }
}

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

    if (! function_exists('decode_jws')) {

        /**
         * iOS App Store Server Notifications V2
         * 응답 JWS 를 Decode 합니다.
         *
         * @param string $jws
         * @return stdClass
         */
        function decode_jws(string $jws): stdClass
        {
            // JWT(S) HEADER (헤더).PAYLOAD (내용).SIGNATURE (서명)

            list($headersB64, $payloadB64, $sig) = explode('.', $jws);

            // 헤더에 alg, x5c 데이터 확인
            $headers = json_decode(base64_decode($headersB64), true);

            // log_message('debug', print_r($headers, true));

            $keyMaterial = <<<EOD
-----BEGIN CERTIFICATE-----
{$headers['x5c'][0]}
-----END CERTIFICATE-----
EOD;

            $key = new Key($keyMaterial, $headers['alg']);

            return JWT::decode($jws, $key);
        }
    }
}

