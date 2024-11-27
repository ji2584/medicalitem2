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
                                <th class="text-center" style="width: 15%;">Sales 엑셀 파일 업로드</th>
                                <td class>

                                    <label for="salesInitExcel" class="btn btn-outline-info mb-0">Excel Upload</label>
                                    <input type="file" id="salesInitExcel" accept=".xlsx" style="display: none;">
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
                        <h3 class="card-title">매출현황</h3>
                        <button id="deleteSales" class="btn btn-danger float-right">
                            <i class="fas fa-trash-alt"></i> 삭제
                        </button>
                        <button id="insertSales" class="btn btn-primary float-right mx-2">
                            <i class="fas fa-plus-circle"></i> 삽입
                        </button>

                    </div>
                    <div class="card-body">
                        <input type="text" id="SearchSalesTable" placeholder ="검색어 입력 (상품명,제조회사)" class="form-control">
                        <table id="SalesTable" class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th class="text-center">구분</th>

                                <th class="text-center">SalesIdx</th>
                                <th class="text-center">수불일자</th>
                                <th class="text-center">매출일자</th>
                                <th class="text-center">거래처</th>
                                <th class="text-center">기능</th>
                                <th class="text-center">상품명</th>
                                <th class="text-center">규격</th>
                                <th class="text-center">단위</th>
                                <th class="text-center">제조회사</th>
                                <th class="text-center">단가</th>
                                <th class="text-center">수량</th>
                                <th class="text-center">금액</th>
                                <th class="text-center">보험약가</th>
                                <th class="text-center">보험코드</th>
                                <th class="text-center">적요</th>

                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Modal Structure -->
    <div class="modal fade" id="SalesModal" tabindex="-1"  role="dialog" aria-labelledby="salesModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesModalLabel">매출현황 조회 및 수정</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="salesForm">

                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <input type ="hidden" id="salesIdx">
                                <td class="text-center">수불일자</td>
                                <td ><input type="text" id="salesTransDate" class="form-control"></td>

                            </tr>
                            <tr>
                                <td class="text-center">매출일자</td>
                                <td ><input type="text" id="salesSalesDate"  class="form-control"></td>
                                <td class="text-center">거래처</td>
                                <td><input type="text" id="salesInstName"  class="form-control"></td>

                            </tr>
                            <tr>
                                <td class="text-center">기능</td>
                                <td><input type="text" id="salesFunctionality"  class="form-control"></td>
                                <td class="text-center">상품명</td>
                                <td><input type="text" id="salesProductName"  class="form-control"></td>

                            </tr>
                            <tr>
                                <td class="text-center">규격</td>
                                <td><input type="text" id="salesSpecification"  class="form-control"></td>
                                <td class="text-center">단위</td>
                                <td><input type="text" id="salesUnit"  class="form-control"></td>

                            </tr>
                            <tr>
                                <td class="text-center">제조회사</td>
                                <td><input type="text" id="salesManufacturer"  class="form-control">
                                <td class="text-center">단가</td>
                                <td><input type="text" id="salesUnitPrice"  class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">수량</td>
                                <td><input type="text" id="salesQuantity"  class="form-control">
                                <td class="text-center">금액</td>
                                <td><input type="text" id="salesTotalAmount"  class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">보험약가</td>
                                <td><input type="text" id="salesInsurancePrice"  class="form-control">
                                <td class="text-center">보험코드</td>
                                <td><input type="text" id="salesInsuranceCode"  class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">적요</td>
                                <td><input type="text" id="salesRemarks"  class="form-control">
                                </td>
                            </tr>

                            <!-- Add additional fields as needed following the structure above -->
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="modalClose">취소</button>
                    <button type="button" class="btn btn-primary" id="saveSales">저장</button>
                </div>
            </div>
        </div>
    </div>






</section>
<?= $this->endSection() ?>

<?= $this->section('script'); ?>
<script>
    // 업로드
    $('#salesInitExcel').change(function() {
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
                'sales/uploadSale',
                formData,
                (resData) => {
                    if(resData.statusCode === 'statOK') {
                        alert('파일 업로드가 완료되었습니다.');
                        $('#salesInitExcel').val('');
                        tblReload();
                    } else{
                        alert('파일 업로드에 실패하였습니다.');
                        console.log(resData);
                    }
                });
        } else {
            alert('파일 업로드가 취소되었습니다.');
            $('#salesInitExcel').val(); // 입력 초기화
        }
    });





    //DataTables
    let tblSearch = null;

    function SalesList() {
        if (tblSearch == null) {
            tblSearch = $('#SalesTable').DataTable ({
                serverSide: true,
                processing: true,
                responsive: true,


                ajax: {
                    'url'          : 'sales/List',
                    'type'         : 'POST',
                    'datatype'     : 'application/json',
                    data: function (d) {
                        console.log(d);
                        d.searchValue = $('#SearchSalesTable').val(); //검색 입력필드 전송값
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
                                console.log('sales/list Ajax Default Error');
                                console.log(resData);
                                break;
                        }
                    }

                },


                "columns": [
                    { "data": null,
                        "render": function(data,type,row) {
                            return `<input type="checkbox" class="row-check" value="${row.SalesIdx}">`; //선택 체크박스
                        },
                    },
                    { "data": "SalesIdx","visible":false},
                    { "data": "Trans_Date"},
                    { "data": "Sales_Date"},
                    { "data": "InstName"},
                    { "data": "Functionality"},
                    { "data": "Sales_ProductName",
                        "render": function(data,type,row){
                            return `<a href="#" class="sales-link"  data-toggle="modal" data-target="#SalesModal">${data}</a>`
                        }},
                    { "data": "Sales_Specification"},
                    { "data": "Sales_Unit" },
                    { "data": "Sales_Manufacturer"},
                    { "data": "Sales_UnitPrice",
                        "render": function(data) {
                            return data ? Number(data).toLocaleString() : '-';
                        }},
                    { "data": "Sales_Quantity",
                        "render": function(data) {
                            return data ? Number(data).toLocaleString() : '-';
                        }},
                    { "data": "Sales_TotalAmount" ,
                        "render": function(data) {
                            return data ? Number(data).toLocaleString() : '-';
                        }},
                    { "data": "Sales_InsurancePrice" ,
                        "render": function(data) {
                            return data ? Number(data).toLocaleString() : '-';
                        }},
                    { "data": "Sales_InsuranceCode" },
                    { "data": "Sales_Remarks" }

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

    SalesList();





    //체크박스 선택 삭제
    $('#deleteSales').click(function () {
        //선택된 체크박스의 ProductCode 수집
        let selectedSalesIdx = [];

        $('.row-check:checked').each(function () {
            selectedSalesIdx.push($(this).val());
        });

        if (!confirm('선택한 항목을 삭제하시겠습니까?')) {
            return;
        }

        if (selectedSalesIdx.length === 0) {
            alert('삭제할 항목을 선택하세요.');
            return;
        }

        // data 객체로 구성하여 전송
        let data = {
            SalesIdx: selectedSalesIdx
        };

        callAjax(
            'sales/delete',
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

    $('#SearchSalesTable').on('keyup', function() {
        tblSearch.ajax.reload();
    });



    //모달 스크립트

    // 숨김 이벤트 핸들러 등록
    $('#SalesModal').on('hide.bs.modal',closeModal);

    $('#SalesModal .close').on('click', function () {
        $('#SalesModal').modal('hide');
    });


    $('#modalClose').click(function () {
        $('#SalesModal').modal('hide');

    })

    function closeModal() {
        if (confirm('작성 중인 내용이 저장되지 않습니다. 정말 취소 하시겠습니까?')) {
            // tblReload();
        }

    }




    //datatable 에서 상품명 클릭시 데이터 불러오기
    $('#SalesTable').on('click','.sales-link', function (){

        $('#salesModalLabel').text('매출현황 정보 수정') //모달 제목 수정
        let table = $('#SalesTable').DataTable();
        let row = $(this).closest('tr'); // 클릭된 버튼의 부모 tr 요소 찾기
        let salesInfo = table.row(row).data(); //해당 tr의 DataTable 데이터 가져오기

        $('#salesIdx').val(salesInfo['SalesIdx']); // 고유 id
        $('#salesSelection').val(salesInfo['Selection']) ;                      //선택
        $('#salesTransDate').val(salesInfo['Trans_Date']);                      // 수불일자
        $('#salesSalesDate').val(salesInfo['Sales_Date']);                      // 매출일자
        $('#salesInstName').val(salesInfo['InstName']);                         // 거레처
        $('#salesFunctionality').val(salesInfo['Functionality']);                // 기능
        $('#salesProductName').val(salesInfo['Sales_ProductName']);              // 상품명
        $('#salesSpecification').val(salesInfo['Sales_Specification']);          // 규격
        $('#salesUnit').val(salesInfo['Sales_Unit']);                            // 단위
        $('#salesManufacturer').val(salesInfo['Sales_Manufacturer']);            // 제조회사
        $('#salesUnitPrice').val(salesInfo['Sales_UnitPrice']);                  //  단가
        $('#salesQuantity').val(salesInfo['Sales_Quantity']);                    // 수량
        $('#salesTotalAmount').val(salesInfo['Sales_TotalAmount']);              // 금액
        $('#salesInsurancePrice').val(salesInfo['Sales_InsurancePrice']);        // 보험약가
        $('#salesInsuranceCode').val(salesInfo['Sales_InsuranceCode']);          // 보험코드
        $('#salesRemarks').val(salesInfo['Sales_Remarks']);                     //적요



    });



    //  모달에서 수정하고 저장버튼 클릭시
    $('#saveSales').click(function () {

        let ajaxEndpoint =$('#salesIdx').val() ? 'sales/SalesUpdate' : 'sales/SalesInsert';
        let successMessage = $('#salesIdx').val() ? '수정되었습니다.' : '삽입되었습니다.';


        let salesData = {
            SalesIdx: $('#salesIdx').val() , //primary key
            Selection : $('#salesSelection').val() ,
            Trans_Date: $('#salesTransDate').val() ,
            Sales_Date: $('#salesSalesDate').val() ,
            InstName: $('#salesInstName').val() ,
            Functionality: $('#salesFunctionality').val() ,
            Sales_ProductName: $('#salesProductName').val(),
            Sales_Specification: $('#salesSpecification').val() ,
            Sales_Unit: $('#salesUnit').val() ,
            Sales_Manufacturer: $('#salesManufacturer').val() ,
            Sales_UnitPrice: $('#salesUnitPrice').val() ,
            Sales_Quantity: $('#salesQuantity').val() ,
            Sales_TotalAmount: $('#salesTotalAmount').val() ,
            Sales_InsurancePrice: $('#salesInsurancePrice').val() ,
            Sales_InsuranceCode: $('#salesInsuranceCode').val() ,
            Sales_Remarks: $('#salesRemarks').val()

        };

        console.log(salesData);

        callAjax(
            ajaxEndpoint,
            JSON.stringify(salesData),

            (resData) => {
                switch (resData.statusCode) {
                    case 'statOK':

                        alert(successMessage);
                        tblReload(true);
                        $('#SalesModal').off('hide.bs.modal');  // 이벤트 핸들러 off
                        $('#SalesModal').modal('hide');
                        $('#SalesModal').on('hide.bs.modal', closeModal) // 이벤트 핸들러 on

                        break;

                    case 'ERROR':
                        let errMsg = resData.statusValue;
                        alert(errMsg);
                        break;

                    default:
                        console.log('sales/SalesUpdate Ajax Default');
                        console.log(resData);
                        break;

                }
            }



        );



    });


    // 매출 현황 삽입
    $('#insertSales').click(function () {
        $('#salesModalLabel').text('매출현황 정보 추가') //모달 제목 변경
        $('#SalesModal').modal('show');
        $('#salesForm')[0].reset(); //모달 초기화 $('#salesForm').trigger('rest')
        $('#salesIdx').val('');// 빈값이면 삽입모드로 설정
    });




</script>
<?= $this->endSection(); ?>
