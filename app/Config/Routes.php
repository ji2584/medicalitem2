<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Admin\Home::index'); // 홈


//Auth
$routes->get('login', 'ADMIN\Auth::index'); // 로그인 뷰
$routes->post('login', 'ADMIN\Auth::login'); //로그인
$routes->get('CreateAdmin', 'ADMIN\CreateAdmin::create'); // 관리자 계정 생성
$routes->get('logout', 'ADMIN\Logout::logout');

// Product && 의약품
$routes->group('product', static function ($routes) {
    $routes->get('/', 'ADMIN\Product::index');
    $routes->post('saveExcel', 'ADMIN\Product::saveExcel'); // 엑셀 업로드 저장
    $routes->post('List', 'ADMIN\Product::List');
    $routes->post('save', 'ADMIN\Product::save');
    $routes->post('delete','ADMIN\Product::delete'); //체크박스 삭제
    $routes->post('batchExcel','ADMIN\Product::batchExcel'); //insertbatch 방식
    $routes->post('dupliExcel','ADMIN\Product::dupliExcel'); //duplicate 방식
    $routes->post('insert','ADMIN\Product::Insert'); // 데이터 삽입
});

//Sales 매출현황
$routes->group('sales', static function ($routes) {
    $routes->get('/', 'ADMIN\Sales::index');
    $routes->post('uploadSale', 'ADMIN\Sales::uploadSale'); // 업로드
    $routes->post('List', 'ADMIN\Sales::SalesList'); //리스트 출력
    $routes->post('delete', 'ADMIN\Sales::SalesDelete'); //  삭제
    $routes->post('SalesUpdate', 'ADMIN\Sales::SalesUpdate'); //매출현황 수정
    $routes->post('SalesInsert', 'ADMIN\Sales::SalesInsert'); //매출현황 삽입

});

//통계
$routes->group('overview', static function ($routes) {
    $routes->get('/', 'ADMIN\Overview::index');
    $routes->post('YearList', 'ADMIN\Overview::YearList'); //연매출 요약
    $routes->post('MonthlyList', 'ADMIN\Overview::MonthlyList'); //월별 상세 내역
    $routes->post('AnnualGraph','ADMIN\Overview::getAnnualData'); //그래프
});

//병원통계
$routes->group('hospital',static function ($routes) {
    $routes->get('/', 'ADMIN\Hospital::index');
    $routes->post('SearchYearMonthHospital','ADMIN\Hospital::SearchYearMonthHospital'); // 연간통계
    $routes->post('MonthlyHospital','ADMIN\Hospital::MonthlyHospital'); //월 상세내역
});