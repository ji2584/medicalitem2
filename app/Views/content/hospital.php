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
                    <div class="card-header">
                        <h3 class="card-title">연간 통계</h3>
                    </div>
                    <div class="card-body">
                        <table id="SearchYearMonthHospitalTable" class="table table-bordered text-center">
                            <input type="text" id="SearchHospital" placeholder ="검색어 입력 (년도-월,ex 2024-01)" class="form-control">
                            <thead>
                            <tr>
                                <th class="text-center">년월</th>
                                <th class="text-center">총 매출액</th>

                            </tr>
                            </thead>
                            <tbody>
                            <!-- 데이터는 AJAX로 불러옵니다 -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                        </h3>
                        <span id = "selectedYearMonth"></span>월 상세내역

                    </div>
                    <div class="card-body">
                        <table id="MonthlyHospitalTable" class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th class="text-center">상품명</th>
                                <th class="text-center">수량</th>
                                <th class="text-center">평균수량</th>
                                <th class="text-center">평균 단가</th>
                                <th class="text-center">해당 상품 매출</th>

                            </tr>
                            </thead>
                            <tbody>
                            <!-- 상품명을 클릭하면 이 테이블이 업데이트 됩니다. -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

</section>
<?= $this->endSection() ?>

<?= $this->section('script'); ?>

<script>

    $(function () {
        HospitalList();
        $('#SearchHospital').on('keyup', function() {
            tblSearch.ajax.reload();

        });
    });


    //DataTables
    let tblSearch = null;

    function HospitalList() {
        if (tblSearch == null) {
            tblSearch = $('#SearchYearMonthHospitalTable').DataTable ({
               responsive: true,

                ajax: {
                    'url'          : 'hospital/SearchYearMonthHospital',
                    'type'         : 'POST',
                    'datatype'     : 'application/json',
                    data: function (d) {
                        console.log(d);
                        d.searchValue = $('#SearchHospital').val(); //검색 입력필드 전송값
                    },
                    'dataSrc'      : function (resData) {
                        console.log(resData);
                        switch (resData.statusCode) {
                            case  'dataOK':
                                return resData.jsonValue;
                            case  'ERROR':
                                let errMsg = resData.statusValue;
                                alert(errMsg);
                                break;

                            default:
                                console.log('hospital/HospitalList Ajax Default Error');

                                break;
                        }
                    }

                },


                "columns": [
                    { "data": "YearMonth",                                                                //상품명
                        "render": function(data,type,row) {
                            // 상품명을 클릭하여 월별 데이터를 볼 수 있도록 링크로 표시
                            return `<a href="#" class="hospital-link" >${data}</a>`;
                        }
                    },
                    { "data": "TotalSales"   ,                                                               // 총 매출액
                        "render": function(data) {
                            return data ? Number(data).toLocaleString() : '0';
                        }
                    }
                ],

                paging      : true,
                lengthMenu  : [10, 20, 50, 75, 100],
                pageLength  : 10,
                pagingType  : 'full_numbers',
                lengthChange: false,
                searching   : false,
                ordering    : false,
                info        : false,
                autoWidth   : true,

                "language": {
                    "processing": "데이터를 불러오는 중...",
                    "search": "검색:",
                    "lengthMenu": "_MENU_ 항목씩 보기",
                    "info": "총 _TOTAL_ 개의 항목 중 _START_ - _END_ 항목",
                    "infoEmpty": "데이터가 없습니다.",
                    "zeroRecords": "검색한 데이터가 없습니다.",
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

    $('#SearchYearMonthHospitalTable').on('click','.hospital-link',function(e) {
        e.preventDefault(); //<a href='#'> 기본적으로 페이지 맨 위로 스크롤 방지
        selectedYearMonth = $(this).text(); // 클릭된 상품명 가져오기
        console.log('상품명',selectedYearMonth);
        $('#selectedYearMonth').text(selectedYearMonth);// 월별 상세내역 년 월 추가하기
        MonthlyHospitalList();
        monthlyTable.ajax.reload();
    });

    let monthlyTable = null;
    let selectedYearMonth = null;
    function MonthlyHospitalList() {
        if (monthlyTable == null) {
            monthlyTable = $('#MonthlyHospitalTable').DataTable ({
                responsive: true,
                ajax: {
                    'url'          : 'hospital/MonthlyHospital',
                    'type'         : 'POST',
                    'datatype'     : 'application/json',
                    data: function (d) {
                        console.log(d);
                        d.yearMonth =selectedYearMonth;

                    },
                    'dataSrc'      : function (resData) {
                        console.log(resData);
                        console.log(resData.jsonValue);

                        switch (resData.statusCode) {
                            case  'dataOK':

                                return resData.jsonValue;

                            case  'ERROR':
                                let errMsg = resData.statusValue;
                                alert(errMsg);
                                break;

                            default:
                                console.log('hospital/MonthlyHospital Ajax Default Error');
                                console.log(resData);
                                break;
                        }
                    }

                },


                "columns": [

                    { "data": "Sales_ProductName" },                                 //상품명
                    { "data": 'TotalQuantity' },                                   //수량
                    { "data": "AvgQuantity"  },                                       //평균 수량

                    { "data": "AvgPrice"  ,                                   // 평균 단가
                        "render": function(data) {
                            return data ? Number(Math.round(data)).toLocaleString() : '-';
                        }
                    },
                    { "data": "TotalSales"   ,                                   // 총 매출
                        "render": function(data) {
                            return data ? Number(data).toLocaleString() : '-';
                        }},




                ],

                paging      : true,
                lengthMenu  : [10, 20, 50, 75, 100],
                pageLength  : 10,
                pagingType  : 'full_numbers',
                lengthChange: true,
                searching   : false,
                ordering    : true,
                info        : true,
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


    // 연간 테이들 리로드 함수
    function tblReload(status) {
        if (tblSearch !== null ) {
            tblSearch.ajax.reload(null,status);//status = true -> 페이지 리셋 데이터 다시 로드 , status = false -> 페이지 유지 한채로 new 데이터 가져옴
        }
    }



</script>
<?= $this->endSection(); ?>
