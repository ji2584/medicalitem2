<?php

namespace App\Controllers\ADMIN;

use App\Controllers\BaseController;

class CreateAdmin extends BaseController
{
    public function create()
    {
//1
        $mysqli = new \mysqli('110.10.147.8', 'medicalitem', 'utinfra9958', 'medicalitem',13306);

        if ($mysqli->connect_error) {
            return "Connection failed: " . $mysqli->connect_error; //오류메시지 반환
        }

        $adminid = 'medicalitem3';
        $password = 'test';

        $adminname = '일반관리자';
        $createdAt =  date('Y-m-d H:i:s');
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO ADMIN (AdminID, AdminPass,AdminName,CreatedAt) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssss", $adminid, $hashedPassword, $adminname, $createdAt);

            if ($stmt->execute()) {
                $stmt->close(); // 여기에서 자원 해제
                $mysqli->close(); // 데이터베이스 연결 해제
                return "관리자 계정이 성공적으로 생성되었습니다!";
            } else {
                $stmt->close(); // 여기에서 자원 해제
                $mysqli->close(); // 데이터베이스 연결 해제
                return "Error: " . $stmt->error;
            }
        } else {
            $mysqli->close(); // 데이터베이스 연결 해제
            return "Error preparing statement: " . $mysqli->error;
        }
    }
}
