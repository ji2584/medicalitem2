<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;
use PhpParser\Node\Expr\Cast\Double;

class HospitalModel extends Model
{
    protected $table = 'SALES';

    protected $primaryKey = 'SalesIdx';
    protected $allowedFields = ['Trans_Date','Sales_Date',
        'InstIdx','Functionality','Sales_ProductName',
        'Sales_Specification','Sales_Unit','Sales_Manufacturer',
        'Sales_UnitPrice',
        'Sales_Quantity','Sales_TotalAmount','Sales_InsurancePrice',
        'SalesInsuranceCode','Sales_Remarks'
    ];
    protected $useAutoIncrement = true;






    //년 월도 검색
    public function getYearMonthHospital ($searchValue = '')
    {
        // SQL 쿼리 작성
        $sql = "SELECT 
                        y.YearMonth,
                        SUM(s.Sales_Quantity * s.Sales_UnitPrice) AS TotalSales
                FROM  
                    YearMonthTable y
                LEFT JOIN 
                      SALES s
                ON 
                        DATE_FORMAT(s.Sales_Date, '%Y-%m') = y.YearMonth";
             

        //검색어가 있을 경우에만 where 조건 추가
        if(!empty($searchValue)) {
            $sql .= " WHERE y.YearMonth LIKE ?";
            $searchValue = '%' . $searchValue . '%' ;
        }


        $sql .= " GROUP BY y.YearMonth 
              ORDER BY y.YearMonth ASC";



        // 쿼리 실행 - ?에 바인딩 값 전달
        $result = $this->db->query($sql, $searchValue);

        log_message('debug', 'result//dasdfasdf'.print_r($result, true));
        return $result->getResultArray();



    }


    //월별 상세내역
    public function getMonthlyHospital($yearMonth)
    {

        $sql = "  WITH RecentSales AS (
                                         SELECT 
                                                s.Sales_ProductName,
                                                DATE_FORMAT(s.Sales_Date, '%Y-%m') AS RecentYearMonth,
                                                SUM(s.Sales_Quantity) AS RecentQuantity
                                         FROM 
                                                SALES s
                                         WHERE 
                                                DATE_FORMAT(s.Sales_Date, '%Y-%m') = (
                                                SELECT MAX(DATE_FORMAT(s2.Sales_Date, '%Y-%m'))
                                                FROM SALES s2
                                                WHERE s2.Sales_ProductName = s.Sales_ProductName
                                                AND DATE_FORMAT(s2.Sales_Date, '%Y-%m') < ? -- 클릭한 연월 이전
                                                 )
                                        GROUP BY 
                                            s.Sales_ProductName, DATE_FORMAT(s.Sales_Date, '%Y-%m')
                                                 ),
                                                                 
                NextSales AS (
                                        SELECT 
                                            s.Sales_ProductName,
                                            DATE_FORMAT(s.Sales_Date, '%Y-%m') AS NextYearMonth
                                        FROM 
                                            SALES s
                                        WHERE 
                                            DATE_FORMAT(s.Sales_Date, '%Y-%m') = (
                                                SELECT MIN(DATE_FORMAT(s2.Sales_Date, '%Y-%m'))
                                                FROM SALES s2
                                                WHERE s2.Sales_ProductName = s.Sales_ProductName
                                                AND DATE_FORMAT(s2.Sales_Date, '%Y-%m') > ? -- 클릭한 연월 이후
                                            )
                                            
                                        GROUP BY 
                                            s.Sales_ProductName, DATE_FORMAT(s.Sales_Date, '%Y-%m')
                                            ),
                                                                                    
                                            
                                            
                FirstSales AS (
                                        SELECT 
                                                s.Sales_ProductName,
                                                MIN(DATE_FORMAT(s.Sales_Date, '%Y-%m')) AS FirstYearMonth
                                        FROM 
                                                SALES s
                                        GROUP BY 
                                                s.Sales_ProductName
                                            )
                                            
                                            
                                        SELECT 
                                                p.Sales_ProductName,
                                                y.YearMonth,
                                                COALESCE(SUM(s.Sales_Quantity), 0) AS TotalQuantity,
                                               CASE 
                                                     WHEN SUM(s.Sales_Quantity) <> 0 
                                                     THEN SUM(s.Sales_UnitPrice * s.Sales_Quantity) / SUM(s.Sales_Quantity)
                                                     ELSE 0 
                                                     END AS AvgPrice,
                                                COALESCE(SUM(s.Sales_Quantity * s.Sales_UnitPrice), 0) AS TotalSales,
                                                r.RecentYearMonth,
                                                r.RecentQuantity,
                                                n.NextYearMonth,
                                                f.FirstYearMonth
                                        FROM 
                                                YearMonthTable y
                                        CROSS JOIN 
                                                (SELECT DISTINCT Sales_ProductName FROM SALES) p
                                        LEFT JOIN 
                                                SALES s
                                        ON 
                                                DATE_FORMAT(s.Sales_Date, '%Y-%m') = y.YearMonth
                                                AND s.Sales_ProductName = p.Sales_ProductName
                                        LEFT JOIN 
                                                RecentSales r
                                        ON 
                                                r.Sales_ProductName = p.Sales_ProductName
                                        LEFT JOIN 
                                                NextSales n
                                        ON 
                                                n.Sales_ProductName = p.Sales_ProductName
                                        LEFT JOIN 
                                                FirstSales f
                                        ON 
                                                f.Sales_ProductName = p.Sales_ProductName
                                        WHERE 
                                                f.FirstYearMonth <= ? -- 최초 매출 월이 클릭한 연월보다 이전인 경우만
                                                AND y.YearMonth = ? -- 클릭한 연월
                                        GROUP BY 
                                                y.YearMonth, p.Sales_ProductName, r.RecentYearMonth, r.RecentQuantity, n.NextYearMonth, f.FirstYearMonth
                                        ORDER BY 
                                                y.YearMonth ASC, p.Sales_ProductName ASC
                                            
                                            ";


        // 쿼리 실행 - ?에 바인딩 값 전달
                 $result = $this->db->query($sql, [$yearMonth,$yearMonth,$yearMonth,$yearMonth]);


                 return $result->getResultArray();



    }




    public function calculateAverageQuantities($data)
    {
        $maxYearMonth = '2024-12'; // 엑셀 데이터의 최대 월 설정

        foreach ($data as &$row) {
            $currentMonth = $row['YearMonth']; // 현재 클릭한 연월
            $recentMonth = $row['RecentYearMonth'] ?? $row['FirstYearMonth']; // 최근 매출 월
            $recentQuantity = $row['RecentQuantity']; // 최근 매출 수량 (클릭 월의 TotalQuantity와 독립적이어야 함)
            $nextMonth = $row['NextYearMonth'] ?? $maxYearMonth; // 다음 매출 월 (없으면 최대 연월)
            $currentQuantity = $row['TotalQuantity']; // 현재 월의 매출 수량



            // 2. 현재 월에 매출 수량이 있는 경우
            if ($currentQuantity != 0) {
                $interval = $this->calculateMonthDifference($currentMonth, $nextMonth,$maxYearMonth);

                // 바로 다음 달에 매출이 있는 경우, 수량 그대로 사용
                if ($interval === 1) {
                    $row['AvgQuantity'] = $currentQuantity;
                } else {
                    // 연속 매출이 아닌 경우, 평균 수량 계산
                    $row['AvgQuantity'] = round($currentQuantity / $interval, 2);
                }

            } else {
                // 3. 현재 월에 매출이 없는 경우
                $interval = $this->calculateMonthDifference($recentMonth, $nextMonth,$maxYearMonth);

                $row['AvgQuantity'] = round($recentQuantity / $interval, 2);
            }

        }

        return $data;
    }

// 월 차이를 계산하는 함수
    public function calculateMonthDifference($startMonth, $endMonth, $maxYearMonth): int
    {

        if (!$startMonth || !$endMonth) {
            log_message('error', "Null value passed to calculateMonthDifference: StartMonth=$startMonth, EndMonth=$endMonth");
            return 0; // 기본값으로 반환
        }

        // 두 날짜를 비교할 DateTime 객체로 변환
        $start = DateTime::createFromFormat('Y-m', $startMonth);
        $end = DateTime::createFromFormat('Y-m', $endMonth);

        if (!$start || !$end) {
            log_message('error', "Invalid date format: StartMonth=$startMonth, EndMonth=$endMonth");
            return 0; // 기본값으로 반환
        }

        $yearDiff = (int)$end->format('Y') - (int)$start->format('Y');
        $monthDiff = (int)$end->format('m') - (int)$start->format('m');

        // 종료 월이 최대 연월(maxYearMonth)인 경우, 종료 월 포함하여 계산
        if ($endMonth === $maxYearMonth) {
            return ($yearDiff * 12) + $monthDiff + 1; // 종료 월 포함
        }

        // 일반적인 경우, 종료 월 포함하지 않음
        return ($yearDiff * 12) + $monthDiff;


    }






}