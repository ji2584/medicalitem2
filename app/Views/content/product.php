<?= $this->extend('layout/default') ?>

<?= $this->section('content-header') ?>
<div class="content-header">
    <div class="container-fluid"></div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            </thead>
                            <tbody>
                            <tr>
                                <th class="text-center" style="width: 15%;">Product 엑셀 파일 업로드</th>
                                <td class>

                                    <label for="initExcel" class="btn btn-outline-info mb-0">Excel Upload</label>
                                    <input type="file" id="initExcel" accept=".xlsx" style="display: none;">
                                </td>
                                <td class>

                                    <label for="dupliExcel" class="btn btn-primary">Excel Upload</label>
                                    <input type="file" id="dupliExcel" accept=".xlsx" style="display: none;">
                                </td>
                                <td class>

                                    <label for="batchExcel" class="btn btn-outline-warning">Excel Upload</label>
                                    <input type="file" id="batchExcel" accept=".xlsx" style="display: none;">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header  "> <!-- d-flex justify-content-between align-items-center -->
                        <h3 class="card-title">소모품</h3>
                        <button id="deleteSelected" class="btn btn-danger float-right">
                            <i class="fas fa-trash-alt"></i> 삭제
                        </button>
                        <button id="InsertData" class="btn btn-primary float-right mx-2">
                            <i class="fas fa-plus-circle"></i> 삽입
                        </button>

                    </div>
                    <div class="card-body">
                        <input type="text" id="SearchTable" placeholder ="검색어 입력 (상품코드,상품명,제조회사)" class="form-control">
                        <table id="ProductTable" class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th class="text-center">선택</th>
                                <th class="text-center">상품코드</th>
                                <th class="text-center">구분</th>
                                <th class="text-center">상품명</th>
                                <th class="text-center">규격</th>
                                <th class="text-center">단위</th>
                                <th class="text-center">제조회사</th>
                                <th class="text-center">환산약가</th>
                                <th class="text-center">환산수</th>
                                <th class="text-center">표준코드</th>
                                <th class="text-center">전문구분</th>
                                <th class="text-center">보험구분</th>
                                <th class="text-center">특정품목</th>
                                <th class="text-center">계산서발행</th>
                                <th class="text-center">거래여부</th>
                                <th class="text-center">공급내역제출</th>
                                <th class="text-center">제형구분</th>

                                <th class="text-center">영문명</th>
                                <th class="text-center">비고</th>
                                <th class="text-center">판매처명</th>
                                <th class="text-center">보험약가</th>
                                <th class="text-center">기준회전일</th>
                                <th class="text-center">보험코드</th>
                                <th class="text-center">품목구분</th>
                                <th class="text-center">매입단가</th>
                                <th class="text-center">마약정보</th>
                                <th class="text-center">분류명</th>
                                <th class="text-center">주성분명</th>
                                <th class="text-center">분류번호</th>
                                <th class="text-center">주성분코드</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Modal Structure -->
    <div class="modal fade" id="productModal" tabindex="-1"  role="dialog" aria-labelledby="productModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">상품 정보 조회 및 수정</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <!-- MedicineType 선택 (삽입 모드에서만 표시) -->
                        <div id="medicineTypeWrapper" class="form-group row" style="display: none;">
                            <label for="medicineTypeSelect" class="col-sm-2 col-form-label">구분</label>
                            <div class="col-sm-10">
                                <select id="medicineTypeSelect" class="form-control">
                                    <option value="소모품">소모품</option>
                                    <option value="약품">약품</option>
                                </select>
                            </div>
                        </div>

                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td class="text-center">상품코드</td>
                                <td><input type="text" id="productCode" class="form-control" readonly></td>
                                <td class="text-center">상품명</td>
                                <td ><input type="text" id="productName" class="form-control"></td>

                            </tr>
                            <tr>
                                <td class="text-center">규격</td>
                                <td ><input type="text" id="specification"  class="form-control"></td>
                                <td class="text-center">단위</td>
                                <td><input type="text" id="unit"  class="form-control"></td>

                            </tr>
                            <tr>
                                <td class="text-center">제조회사</td>
                                <td><input type="text" id="company"  class="form-control"></td>
                                <td class="text-center">환산약가</td>
                                <td><input type="text" id="convertedPrice"  class="form-control"></td>

                            </tr>
                            <tr>
                                <td class="text-center">환산수량</td>
                                <td><input type="text" id="convertedQuantity"  class="form-control"></td>
                                <td class="text-center">표준코드</td>
                                <td><input type="text" id="standardCode"  class="form-control"></td>

                            </tr>
                            <tr>
                                <td class="text-center">전문구분</td>
                                <td><input type="text" id="specialtyDivision"  class="form-control">
                                <td class="text-center">보험구분</td>
                                <td><input type="text" id="insuranceClassification"  class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">특정품목</td>
                                <td><input type="text" id="specialItem"  class="form-control">
                                <td class="text-center">계산서발행</td>
                                <td><input type="text" id="invoiceIssue"  class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">거래여부</td>
                                <td><input type="text" id="transactionStatus"  class="form-control">
                                <td class="text-center">공급내역제출</td>
                                <td><input type="text" id="supplySubmission"  class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">제형구분</td>
                                <td><input type="text" id="dosageForm"  class="form-control">
                                <td class="text-center">약품구분</td>
                                <td><input type="text" id="medicineType"  class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">영문명</td>
                                <td><input type="text" id="englishName"  class="form-control">
                                <td class="text-center">비고</td>
                                <td><input type="text" id="remarks"  class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">판매처명</td>
                                <td><input type="text" id="sellerName"  class="form-control">
                                <td class="text-center">보험약가</td>
                                <td><input type="text" id="insurancePrice"  class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">기준회전일</td>
                                <td><input type="text" id="rotationDays"  class="form-control">
                                <td class="text-center">보험코드</td>
                                <td><input type="text" id="insuranceCode"  class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">품목구분</td>
                                <td><input type="text" id="itemClassification"  class="form-control">
                                <td class="text-center">매입단가</td>
                                <td><input type="text" id="purchasePrice"  class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">마약정보</td>
                                <td><input type="text" id="drugInfo"  class="form-control">
                                </td>
                            </tr>

                            <tr class="medicine-specific" style="display: none;">
                                <td class="text-center bg-light" colspan="4"><strong>약품 전용 정보</strong></td>
                            </tr>
                            <tr class="medicine-specific" style="display: none;">
                                <td class="text-center">주성분명</td>
                                <td><input type="text" id="ingredient_Name" class="form-control"></td>
                                <td class="text-center">분류명</td>
                                <td><input type="text" id="category_Name" class="form-control"></td>
                            </tr>
                            <tr class="medicine-specific" style="display: none;">
                                <td class="text-center">주성분코드</td>
                                <td><input type="text" id="ingredient_Code" class="form-control"></td>
                                <td class="text-center">분류번호</td>
                                <td><input type="text" id="category_Number" class="form-control"></td>
                            </tr>



                            <!-- Add additional fields as needed following the structure above -->
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="modalClose">취소</button>
                    <button type="button" class="btn btn-primary" id="saveProduct">저장</button>
                </div>
            </div>
        </div>
    </div>






</section>
<?= $this->endSection() ?>

<?= $this->section('script'); ?>
<script>
    // 업로드
    $('#initExcel').change(function() {
        if (confirm('파일을 업로드 하시겠습니까?')) {
            let fileInput = this.files[0];
            console.log(fileInput);
            if (!fileInput) {
                alert('파일을 선택하세요.');
                return;
            }


            let formData = new FormData();
            formData.append('uploadfile', fileInput);
            console.log("FormData 생성 완료:" , formData);

            callAjax(
                'product/saveExcel',
                formData,
                (resData) => {
                    if(resData.statusCode === 'statOK') {
                        alert('파일 업로드가 완료되었습니다.');
                        $('#initExcel').val('');
                        tblReload();
                    } else{
                        alert('파일 업로드에 실패하였습니다.');
                        console.log(resData);
                    }
                });
        } else {
            alert('파일 업로드가 취소되었습니다.');
            $('#initExcel').val(); // 입력 초기화
        }
    });





    //DataTables
    let tblSearch = null;

    function ProductList() {
        if (tblSearch == null) {
            tblSearch = $('#ProductTable').DataTable ({
                serverSide: true,
                processing: true,
                responsive: true,


                ajax: {
                    'url'          : 'product/List',
                    'type'         : 'POST',
                    'datatype'     : 'application/json',
                    data: function (d) {
                        console.log(d);
                        d.searchValue = $('#SearchTable').val(); //검색 입력필드 전송값
                        d.length = 10;
                    },
                    'dataSrc'      : function (resData) {
                        console.log(resData);
                        console.log(resData.jsonValue);

                        switch (resData.statusCode) {
                            case  'dataOK':
                                resData.recordsTotal = resData.jsonValue.recordsTotal;
                                resData.recordsFiltered = resData.jsonValue.recordsFiltered;
                                return resData.jsonValue.data;

                            case  'ERROR':
                                let errMsg = resData.statusValue;
                                alert(errMsg);
                                break;

                            default:
                                console.log('product/list Ajax Default Error');
                                console.log(resData);
                                break;
                        }
                    }

                },


                "columns": [
                    { "data": null,
                      "render": function(data,type,row) {
                          return `<input type="checkbox" class="row-check" value="${row.ProductCode}">`; //선택 체크박스
                      },
                    },
                    { "data": "ProductCode"},
                    { "data": "MedicineType",
                      "render": function(data,type,row) {
                        return data === '약품' ? '약' : (data === '소모품' ? '소모' : data);
                    }

                     },

                    { "data": "ProductName",
                      "render": function(data,type,row){
                          return `<a href="#" class="product-link"  data-toggle="modal" data-target="#productModal">${data}</a>`
                      }},
                    { "data": "Specification"},
                    { "data": "Unit" },
                    { "data": "Company"},
                    { "data": "ConvertedPrice",
                      "render": function(data) {
                        return data ? Number(data).toLocaleString() : '-';
                      }},
                    { "data": "ConvertedQuantity",
                      "render": function(data) {
                        return data ? Number(data).toLocaleString() : '-';
                      }},
                    { "data": "StandardCode" },
                    { "data": "SpecialtyDivision" },
                    { "data": "InsuranceClassification" },
                    { "data": "SpecificItem" },
                    { "data": "InvoiceIssue" },
                    { "data": "TransactionStatus" },
                    { "data": "SupplySubmission" },
                    { "data": "DosageForm" },

                    { "data": "EnglishName"},
                    { "data": "Remarks" },
                    { "data": "SellerName" },
                    { "data": "InsurancePrice" ,
                      "render": function(data) {
                        return data ? Number(data).toLocaleString() : '-';
                      }},
                    { "data": "RotationDays" },
                    { "data": "InsuranceCode" },
                    { "data": "ItemClassification" },
                    { "data": "PurchasePrice" },
                    { "data": "DrugInfo" },
                    { "data": "Category_Name"},
                    { "data": "Ingredient_Name"},
                    { "data": "Category_Number"},
                    { "data": "Ingredient_Code"}
                ],

                paging      : true,
                lengthMenu  : [10, 20, 50, 75, 100],
                pageLength  : 10,
                pagingType  : 'full_numbers',
                lengthChange: true,
                searching   : false,
                ordering    : false,
                info        : false,
                autoWidth   : false,

                "language": {
                    "emptyTable": "데이터가 없거나 관리자의 권한이 없습니다.",
                    "processing": "데이터를 불러오는 중...",
                    "search": "검색:",
                    "lengthMenu": "_MENU_ 항목씩 보기",
                    "info": "총 _TOTAL_ 개의 항목 중 _START_ - _END_ 항목",
                    "infoEmpty": "데이터가 없습니다.",
                    "zeroRecords": "일치하는 항목이 없습니다.",
                    "paginate": {
                        "first": "처음",
                        "last": "마지막",
                        "next": "다음",
                        "previous": "이전"
                    }
                }
            });




        }
    }

    ProductList();

    let isSaveMode =  false; // 삽입 수정 구분 플래그 (true: 삽입모드 false: 수정모드)

    //삽입 버튼 클릭시 모달 창 설정 변경
    $('#InsertData').click(function () {
        isSaveMode = true; // 삽입모드 설정
        $('#productModalLabel').text('상품 정보 삽입'); // 모달 제목 설정
        $('#productForm')[0].reset(); // 폼 초기화
        $('#medicineTypeWrapper').show(); // 구분 선택 옵션 표시
        $('#medicineTypeSelect').val('소모품'); // 기본값을 소모품으로 설정
        $('.medicine-specific').hide(); // 약품 전용 필드 숨기기
        $('#productCode').prop('readonly',false); // 삽입 할때 readonly 해제
        $('#productModal').modal('show');


    })

    // MedicineType 선택에 따른 약품 전용 정보 표시 조정

    $('#medicineTypeSelect').change(function () {
        if($(this).val() === '약품') {
            $('.medicine-specific').show(); //약품 전용 정보 표시
        } else {
            $('.medicine-specific').hide() // 약품 전용 정보 숨기기
        }
    })



        //체크박스 선택 삭제
    $('#deleteSelected').click(function () {
        //선택된 체크박스의 ProductCode 수집
        let selectedProductCodes = [];

        $('.row-check:checked').each(function () {
            selectedProductCodes.push($(this).val());
        });

        if (!confirm('선택한 항목을 삭제하시겠습니까?')) {
            return;
        }

        if (selectedProductCodes.length === 0) {
            alert('삭제할 항목을 선택하세요.');
            return;
        }

        // data 객체로 구성하여 전송
        let data = {
            ProductCodes: selectedProductCodes
        };

        callAjax(
            'product/delete',
            JSON.stringify(data),
            (resData) => {
                switch (resData.statusCode) {
                    case 'statOK':
                        alert('데이터가 삭제되었습니다.');
                        tblReload(true);
                        break

                    case 'ERROR':
                        let errMsg = resData.statusValue;
                        alert(errMsg);
                        break;

                    default:
                        console.log('product/delete Ajax Default');
                        console.log(resData);
                        break;

                }
            }

        );




    })

    // 테이들 리로드 함수
    function tblReload(status) {
        if (tblSearch !== null ) {
            tblSearch.ajax.reload(null,status);//status = true -> 페이지 리셋 데이터 다시 로드 , status = false -> 페이지 유지 한채로 new 데이터 가져옴
        }
    }

    $('#SearchTable').on('keyup', function() {
        tblSearch.ajax.reload();
    });



    //모달 스크립트

    // 숨김 이벤트 핸들러 등록
    $('#productModal').on('hide.bs.modal',closeModal);

    $('#productModal .close').on('click', function () {
        $('#productModal').modal('hide');
    });


    $('#modalClose').click(function () {
        $('#productModal').modal('hide');

    })

    function closeModal() {
        if (confirm('작성 중인 내용이 저장되지 않습니다. 정말 취소 하시겠습니까?')) {
            // tblReload();
        }

    }




    //datatable 에서 상품명 클릭시 데이터 불러오기
    $('#ProductTable').on('click','.product-link', function (){
        isSaveMode = false; // 수정 모드 설정
        $('#productModalLabel').text('상품 정보 수정') //모달 제목 수정
        let table = $('#ProductTable').DataTable();
        let row = $(this).closest('tr'); // 클릭된 버튼의 부모 tr 요소 찾기
        let productInfo = table.row(row).data(); //해당 tr의 DataTable 데이터 가져오기

        let ProductCode = productInfo['ProductCode'];               // 상품코드
        let ProductName = productInfo['ProductName'];               // 상품명
        let Specification = productInfo['Specification'];           // 규격
        let Unit = productInfo['Unit'];                              // 규격
        let Company = productInfo['Company'];                       // 제조회사
        let ConvertedPrice = productInfo['ConvertedPrice'];          // 환산약가
        let ConvertedQuantity = productInfo['ConvertedQuantity'];   // 환산수
        let StandardCode = productInfo['StandardCode'];             // 표준코드
        let SpecialtyDivision = productInfo['SpecialtyDivision'];               // 전문구분
        let InsuranceClassification = productInfo['InsuranceClassification'];   // 보험구분
        let SpecificItem = productInfo['SpecificItem'];                         // 특정품목
        let InvoiceIssue = productInfo['InvoiceIssue'];                          // 계산서발행
        let TransactionStatus = productInfo['TransactionStatus'];               // 거래여부
        let SupplySubmission = productInfo['SupplySubmission'];                 // 공급내역제출
        let DosageForm = productInfo['DosageForm'];                             // 제형구분
        let MedicineType = productInfo['MedicineType'];                         // 약품구분
        let EnglishName = productInfo['EnglishName'];                           // 영문명
        let Remarks = productInfo['Remarks'];                                   // 비고
        let SellerName = productInfo['SellerName'];                             // 판매처명
        let InsurancePrice = productInfo['InsurancePrice'];                     // 보험약가
        let RotationDays = productInfo['RotationDays'];                         // 기준회전일
        let InsuranceCode = productInfo['InsuranceCode'];                       // 보험코드
        let ItemClassification = productInfo['ItemClassification'];             // 품목구분
        let PurchasePrice = productInfo['PurchasePrice'];                       // 매입단가
        let DrugInfo = productInfo['DrugInfo'];                                  // 마약정보


        $('#productCode').val(ProductCode);
        $('#productName').val(ProductName);
        $('#specification').val(Specification);
        $('#unit').val(Unit);
        $('#company').val(Company);
        $('#convertedPrice').val(ConvertedPrice);
        $('#convertedQuantity').val(ConvertedQuantity);
        $('#standardCode').val(StandardCode);
        $('#specialtyDivision').val(SpecialtyDivision);
        $('#insuranceClassification').val(InsuranceClassification);
        $('#specificItem').val(SpecificItem);
        $('#invoiceIssue').val(InvoiceIssue);
        $('#transactionStatus').val(TransactionStatus);
        $('#supplySubmission').val(SupplySubmission);
        $('#dosageForm').val(DosageForm);
        $('#medicineType').val(MedicineType);
        $('#englishName').val(EnglishName);
        $('#remarks').val(Remarks);
        $('#sellerName').val(SellerName);
        $('#insurancePrice').val(InsurancePrice);
        $('#rotationDays').val(RotationDays);
        $('#insuranceCode').val(InsuranceCode);
        $('#itemClassification').val(ItemClassification);
        $('#purchasePrice').val(PurchasePrice);
        $('#drugInfo').val(DrugInfo);

        // MedicineType 체크하여 약품 전용 필드 표시
        if (productInfo['MedicineType'] === '약품') {
            $('.medicine-specific').show(); // 약품 전용 필드 그룹 표시
            // 약품 전용 필드 설정
            $('#ingredient_Name').val(productInfo['Ingredient_Name']);
            $('#category_Number').val(productInfo['Category_Number']);
            $('#ingredient_Code').val(productInfo['Ingredient_Code']);
            $('#category_Name').val(productInfo['Category_Name']);
        } else {
            $('.medicine-specific').hide(); // 소모품일 경우 약품 전용 필드 숨김
        }


    });



    //  모달에서 수정하고 저장버튼 클릭시
    $('#saveProduct').click(function () {

        let productCode = $('#productCode').val();
        if (!productCode) {  // 값이 비어있는지 확인
            alert("상품코드는 필수 입력 항목입니다.");  // 경고 메시지 표시
            return;  // 저장 중단
        }

        let productData = {
            ProductCode: $('#productCode').val() , //primary key
            ProductName: $('#productName').val() ,
            Specification: $('#specification').val() ,
            Unit: $('#unit').val() ,
            Company: $('#company').val() ,
            ConvertedPrice: $('#convertedPrice').val() ,
            ConvertedQuantity: $('#convertedQuantity').val(),
            StandardCode: $('#standardCode').val() ,
            SpecialtyDivision: $('#specialtyDivision').val() ,
            InsuranceClassification: $('#insuranceClassification').val() ,
            SpecificItem: $('#specialItem').val() ,
            InvoiceIssue: $('#invoiceIssue').val() ,
            TransactionStatus: $('#transactionStatus').val() ,
            SupplySubmission: $('#supplySubmission').val() ,
            DosageForm: $('#dosageForm').val() ,
            MedicineType: $('#medicineTypeSelect').val() ,  // 선택한 옵션값을 넣어줌
            EnglishName: $('#englishName').val() ,
            Remarks: $('#remarks').val() ,
            SellerName: $('#sellerName').val() ,
            InsurancePrice: $('#insurancePrice').val() ,
            RotationDays: $('#rotationDays').val() ,
            InsuranceCode: $('#insuranceCode').val() ,
            ItemClassification: $('#itemClassification').val() ,
            PurchasePrice: $('#purchasePrice').val() ,
            DrugInfo: $('#drugInfo').val() ,
            Category_Name: $('#category_Name').val() ,
            Ingredient_Name: $('#ingredient_Name').val() ,
            Category_Number: $('#category_Number').val() ,
            Ingredient_Code: $('#ingredient_Code').val()
        };




        console.log(productData);

        let ajaxEndpoint = isSaveMode ? 'product/insert' : 'product/save';
        let successMessage = isSaveMode ? '삽입되었습니다.' : '수정되었습니다.';

       callAjax(
           ajaxEndpoint,
           JSON.stringify(productData),

           (resData) => {
               switch (resData.statusCode) {
                   case 'statOK':
                       alert(successMessage);
                       tblReload(true);
                       $('#productModal').off('hide.bs.modal');  // 이벤트 핸들러 off
                       $('#productModal').modal('hide');
                       $('#productModal').on('hide.bs.modal', closeModal) // 이벤트 핸들러 on

                       break;

                   case 'ERROR':
                       let errMsg = resData.statusValue;
                       alert(errMsg);
                       break;

                   default:
                       console.log('product/save Ajax Default');
                       console.log(resData);
                       break;

               }
           }



           );



    });

// insertbatch방식 버튼
    $('#batchExcel').change(function () {

        if (confirm('파일을 업로드 하시겠습니까?')) {
            let fileInput = this.files[0];
            console.log(fileInput);
            if (!fileInput) {
                alert('파일을 선택하세요.');
                return;
            }
            let formData = new FormData();
            formData.append('uploadfile', fileInput);
            console.log("FormData 생성 완료:" , formData);

            callAjax(
                'product/batchExcel',
                formData,
                (resData) => {
                    if(resData.statusCode === 'statOK') {
                        alert('파일 업로드가 완료되었습니다.');
                        $('#batchExcel').val('');
                        tblReload();
                    } else{
                        alert('파일 업로드에 실패하였습니다.');
                        console.log(resData);
                    }
                });
        } else {
            alert('파일 업로드가 취소되었습니다.');
            $('#batchExcel').val(); // 입력 초기화
        }

    });

    //on duplicate key 쿼리 방식

    $('#dupliExcel').change(function () {

        if (confirm('파일을 업로드 하시겠습니까?')) {
            let fileInput = this.files[0];
            console.log(fileInput);
            if (!fileInput) {
                alert('파일을 선택하세요.');
                return;
            }
            let formData = new FormData();
            formData.append('uploadfile', fileInput);
            console.log("FormData 생성 완료:" , formData);

            callAjax(
                'product/dupliExcel',
                formData,
                (resData) => {
                    if(resData.statusCode === 'statOK') {
                        alert('파일 업로드가 완료되었습니다.');
                        $('#dupliExcel').val('');
                        tblReload();
                    } else{
                        alert('파일 업로드에 실패하였습니다.');
                        console.log(resData);
                    }
                });
        } else {
            alert('파일 업로드가 취소되었습니다.');
            $('#dupliExcel').val(); // 입력 초기화
        }

    });






</script>
<?= $this->endSection(); ?>
