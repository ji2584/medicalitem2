<?php

/**
 * 로직 성공시 결과값 전송 함수
 *
 * @param null $resultValue
 */
function ut_procSuccess($resultValue = null)
{
    header('Content-Type: application/json');

    if ($resultValue == null && !is_array($resultValue)) {
        $successValue = ['statusCode' => 'statOK' ,'statusValue' => 'ok'];
    } else {
        $successValue = ['statusCode' => 'dataOK', 'jsonValue' => $resultValue];
    }

    echo json_encode($successValue,JSON_UNESCAPED_UNICODE);
}

/**
 * 로직 실패시 결과값 전송 함수
 *
 * @param $errorMsg : 실패 메시지
 * @param string $errCode : 실패 코드
 */
function ut_ProcError($errorMsg, string $errCode = 'ERROR')
{
    header('Content-Type: application/json');

    $errorValue = ['statusCode' => $errCode, 'statusValue' => $errorMsg];

    echo json_encode($errorValue, JSON_UNESCAPED_UNICODE);
}

