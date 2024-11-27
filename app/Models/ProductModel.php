<?php

namespace App\Models;



use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'PRODUCT';
    protected $primaryKey = 'ProductCode';
    protected $allowedFields = [
        'ProductName',               // 상품명
        'Specification',              // 규격
        'Unit',                       // 단위
        'Company',                    // 제조회사
        'ConvertedPrice',             // 환산약가
        'ConvertedQuantity',          // 환산수량
        'StandardCode',               // 표준코드
        'SpecialtyDivision',          // 전문구분
        'InsuranceClassification',    // 보험구분
        'SpecificItem',               // 특정품목
        'InvoiceIssue',               // 계산서발행
        'TransactionStatus',          // 거래여부
        'SupplySubmission',           // 공급내역제출
        'DosageForm',                 // 제형구분
        'MedicineType',               // 약품구분
        'ProductCode',                // 상품코드
        'EnglishName',                // 영문명
        'Remarks',                    // 비고
        'SellerName',                 // 판매처명
        'InsurancePrice',             // 보험약가
        'RotationDays',               // 기준회전일
        'InsuranceCode',              // 보험코드
        'ItemClassification',         // 품목구분
        'PurchasePrice',              // 매입단가
        'DrugInfo',                   // 마약정보
        'Category_Name',              // 분류명
        'Ingredient_Name',             // 주성분명
        'Category_Number',            // 분류번호
        'Ingredient_Code',            // 주성분코드
    ];
    protected $useAutoIncrement = false;
    protected $returnType     = 'array';


    /*
    public function saveProducts(array $ArrProductData): void
    {
        $db = db_connect();

        // SQL 구문 생성 (컬럼명 백틱 사용)
        $sql = "INSERT INTO PRODUCT (`ProductCode`, `ProductName`, `Specification`, `Unit`, `Company`, `ConvertedPrice`, `ConvertedQuantity`,
        `StandardCode`, `SpecialtyDivision`, `InsuranceClassification`, `SpecificItem`, `InvoiceIssue`,
        `TransactionStatus`, `SupplySubmission`, `DosageForm`, `MedicineType`,
        `EnglishName`, `Remarks`, `SellerName`, `InsurancePrice`, `RotationDays`, `InsuranceCode`,
        `ItemClassification`, `PurchasePrice`, `DrugInfo`, `Category_Name`, `Ingredient_Name`, `Category_Number`, `Ingredient_Code`) VALUES ";

        // VALUES 구문 생성
        $values = [];
        foreach ($ArrProductData as $product) {
            $escapedValues =  array_values($product);
            $values[] = "(" . implode(",", $escapedValues) . ")";
        }
        $sql .= implode(",", $values);

        // ON DUPLICATE KEY UPDATE 구문 추가
        $sql .= " ON DUPLICATE KEY UPDATE
        `ProductName`=VALUES(`ProductName`),
        `Specification`=VALUES(`Specification`),
        `Unit`=VALUES(`Unit`),
        `Company`=VALUES(`Company`),
        `ConvertedPrice`=VALUES(`ConvertedPrice`),
        `ConvertedQuantity`=VALUES(`ConvertedQuantity`),
        `StandardCode`=VALUES(`StandardCode`),
        `SpecialtyDivision`=VALUES(`SpecialtyDivision`),
        `InsuranceClassification`=VALUES(`InsuranceClassification`),
        `SpecificItem`=VALUES(`SpecificItem`),
        `InvoiceIssue`=VALUES(`InvoiceIssue`),
        `TransactionStatus`=VALUES(`TransactionStatus`),
        `SupplySubmission`=VALUES(`SupplySubmission`),
        `DosageForm`=VALUES(`DosageForm`),
        `MedicineType`=VALUES(`MedicineType`),
        `EnglishName`=VALUES(`EnglishName`),
        `Remarks`=VALUES(`Remarks`),
        `SellerName`=VALUES(`SellerName`),
        `InsurancePrice`=VALUES(`InsurancePrice`),
        `RotationDays`=VALUES(`RotationDays`),
        `InsuranceCode`=VALUES(`InsuranceCode`),
        `ItemClassification`=VALUES(`ItemClassification`),
        `PurchasePrice`=VALUES(`PurchasePrice`),
        `DrugInfo`=VALUES(`DrugInfo`),
        `Category_Name`=VALUES(`Category_Name`),
        `Ingredient_Name`=VALUES(`Ingredient_Name`),
        `Category_Number`=VALUES(`Category_Number`),
        `Ingredient_Code`=VALUES(`Ingredient_Code`)";

        // 트랜잭션 시작 및 쿼리 실행
        try {
            $db->transBegin();
            $db->query($sql);
            $db->transCommit();
            ut_procSuccess();
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '쿼리 실행 오류: ' . $e->getMessage());
            ut_ProcError("데이터 저장 중 오류가 발생했습니다.");
        }
    }
*/

    public function saveProducts(array $data): void
    {

        // INSERT INTO 구문 생성
        $sql = "INSERT INTO PRODUCT (`ProductCode`, `ProductName`, `Specification`, `Unit`, `Company`, `ConvertedPrice`, `ConvertedQuantity`,
        `StandardCode`, `SpecialtyDivision`, `InsuranceClassification`, `SpecificItem`, `InvoiceIssue`,
        `TransactionStatus`, `SupplySubmission`, `DosageForm`, `MedicineType`, 
        `EnglishName`, `Remarks`, `SellerName`, `InsurancePrice`, `RotationDays`, `InsuranceCode`,
        `ItemClassification`, `PurchasePrice`, `DrugInfo`, `Category_Name`, `Ingredient_Name`, `Category_Number`, `Ingredient_Code`) 


    VALUES 
        (:ProductCode:, :ProductName:, :Specification:, :Unit:, :Company:, :ConvertedPrice:, :ConvertedQuantity:, 
        :StandardCode:, :SpecialtyDivision:, :InsuranceClassification:, :SpecificItem:, :InvoiceIssue:, 
        :TransactionStatus:, :SupplySubmission:, :DosageForm:, :MedicineType:, 
        :EnglishName:, :Remarks:, :SellerName:, :InsurancePrice:, :RotationDays:, :InsuranceCode:, 
        :ItemClassification:, :PurchasePrice:, :DrugInfo:, :Category_Name:, :Ingredient_Name:, :Category_Number:, :Ingredient_Code:)
        
        
    ON DUPLICATE KEY UPDATE 
        `ProductName`=VALUES(`ProductName`),
        `Specification`=VALUES(`Specification`),
        `Unit`=VALUES(`Unit`),
        `Company`=VALUES(`Company`),
        `ConvertedPrice`=VALUES(`ConvertedPrice`),
        `ConvertedQuantity`=VALUES(`ConvertedQuantity`),
        `StandardCode`=VALUES(`StandardCode`),
        `SpecialtyDivision`=VALUES(`SpecialtyDivision`),
        `InsuranceClassification`=VALUES(`InsuranceClassification`),
        `SpecificItem`=VALUES(`SpecificItem`),
        `InvoiceIssue`=VALUES(`InvoiceIssue`),
        `TransactionStatus`=VALUES(`TransactionStatus`),
        `SupplySubmission`=VALUES(`SupplySubmission`),
        `DosageForm`=VALUES(`DosageForm`),
        `MedicineType`=VALUES(`MedicineType`),
        `EnglishName`=VALUES(`EnglishName`),
        `Remarks`=VALUES(`Remarks`),
        `SellerName`=VALUES(`SellerName`),
        `InsurancePrice`=VALUES(`InsurancePrice`),
        `RotationDays`=VALUES(`RotationDays`),
        `InsuranceCode`=VALUES(`InsuranceCode`),
        `ItemClassification`=VALUES(`ItemClassification`),
        `PurchasePrice`=VALUES(`PurchasePrice`),
        `DrugInfo`=VALUES(`DrugInfo`),
        `Category_Name`=VALUES(`Category_Name`),
        `Ingredient_Name`=VALUES(`Ingredient_Name`),
        `Category_Number`=VALUES(`Category_Number`),
        `Ingredient_Code`=VALUES(`Ingredient_Code`)";



        $this->db->query($sql, $data);

    }



}