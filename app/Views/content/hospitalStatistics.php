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







    // 테이들 리로드 함수
    function tblReload(status) {
        if (tblSearch !== null ) {
            tblSearch.ajax.reload(null,status);//status = true -> 페이지 리셋 데이터 다시 로드 , status = false -> 페이지 유지 한채로 new 데이터 가져옴
        }
    }

    $('#SearchSalesTable').on('keyup', function() {
        tblSearch.ajax.reload();
    });





</script>
<?= $this->endSection(); ?>
