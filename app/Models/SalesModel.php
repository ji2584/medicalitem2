<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
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

    protected $returnType     = 'array';





    //연간 매출 요약
    public function getAnnualSalesSummary($searchValue = '', $length = null, $start = null)
    {


     // SQL 쿼리 작성
        $sql = "
        SELECT 
        Sales_ProductName AS ProductName,
        SUM(Sales_Quantity) AS TotalQuantity,
          COALESCE(
            CASE 
                WHEN SUM(Sales_Quantity) > 0 THEN FLOOR(SUM(Sales_UnitPrice * Sales_Quantity) / SUM(Sales_Quantity))
                ELSE 0
            END, 0
        )  AS AveragePrice,
        SUM(Sales_TotalAmount) AS TotalSales,
        COUNT(Sales_ProductName) - 1 AS ResaleCount
        FROM
        SALES
        ";

     //검색어가 있을 경우에만 where 조건 추가
        if(!empty($searchValue)) {
            $sql .= "WHERE Sales_ProductName LIKE ?";
            $searchValue = '%' . $searchValue . '%';
        }


     $sql .= "GROUP BY Sales_ProductName
              ORDER BY Sales_ProductName ASC
              LIMIT ?, ?  
                ";

        $test = !empty($searchValue) ? [$searchValue, $start, $length] : [$start, $length];


        // 쿼리 실행 - ?에 바인딩 값 전달
        $result = $this->db->query($sql, $test);

        return $result->getResultArray();



    }

    /**
     * 전체 레코드 수 (검색 조건 적용 전)
     */
    public function getTotalRecordsCount()
    {

        return $this->db->table('SALES')->countAll();
    }

    /**
     * 필터링된 총 레코드 수 (검색어 필터 적용)
     */
    public function getFilteredRecordsCount($searchValue = '')
    {
        $builder = $this->select('Sales_ProductName');

        if (!empty($searchValue)) {
            $builder->like('Sales_ProductName', $searchValue);
        }

        $builder->groupBy('Sales_ProductName');


        return $builder->countAllResults(false);
    }


     //월별 상세 내역
    public function getMonthlyDataByProduct($productName = '')
    {
        log_message('debug', 'getMonthlyDataByProduct called with productName: ' . $productName);

             // SQL 쿼리 작성
        $sql = "
        SELECT 
            YearMonthTable.YearMonth AS SalesMonth, 
            COALESCE(SUM(S.Sales_Quantity), 0) AS TotalQuantity, 
             COALESCE(
            CASE 
                WHEN SUM(S.Sales_Quantity) > 0 THEN FLOOR(SUM(S.Sales_TotalAmount) / SUM(S.Sales_Quantity))
                ELSE 0
            END, 0
        ) AS AveragePrice,  
            COALESCE(SUM(S.Sales_TotalAmount), 0) AS TotalSales
        FROM 
            YearMonthTable
        LEFT JOIN 
            SALES AS S 
            ON DATE_FORMAT(S.Sales_Date, '%Y-%m') = YearMonthTable.YearMonth 
            AND S.Sales_ProductName = ?
        GROUP BY 
            YearMonthTable.YearMonth
        ORDER BY 
            YearMonthTable.YearMonth ASC
       
    ";

        $result = $this->db->query($sql, [$productName]);

        $data = $result->getResultArray();



              return $data;



    }





}