<?php

namespace App\Controllers\ADMIN;

use App\Controllers\BaseController;

class Logout extends BaseController
{

    public function logout(): string
    {

        session()->destroy(); // 세션 파괴

        return view('auth/login'); // 로그인 페이지로 리다이렉트
    }


}