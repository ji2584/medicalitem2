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
                        <h3 class="card-title">연간 매출 요약</h3>
                    </div>
                    <div class="card-body">
                        <table id="AnnualSalesSummaryTable" class="table table-bordered text-center">
                            <input type="text" id="SearchOverviewTable" placeholder ="검색어 입력 (상품명)" class="form-control">
                            <thead>
                            <tr>
                                <th class="text-center">상품명</th>
                                <th class="text-center">연간 총 수량</th>
                                <th class="text-center">평균 단가</th>
                                <th class="text-center">총 매출액</th>
                                <th class="text-center">연간 거래횟수(-포함?)</th>
                                <th class="text-center">그래프 보기</th>

                            </tr>
                            </thead>
                            <tbody>

                            <!-- 데이터는 AJAX로 불러옵니다 -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

         <!--   <!-- 그래프 모달 -->
            <div id="graphModal" class="modal" tabindex="-1" style="display:none;">
                <div class="modal-dialog" style="max-width: 70%; width: 70%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">연간 매출 그래프</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <canvas id="annualSalesChart" width="1500" height="800"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">월별 상세 내역</h3>

                    </div>
                    <div class="card-body">
                        <table id="MonthlySalesDetailTable" class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th class="text-center">매출 일자</th>
                                <th class="text-center"> 수량</th>
                                <th class="text-center">평균 수량</th>
                                <th class="text-center">평균 단가</th>
                                <th class="text-center">해당 월 매출</th>

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



    $('#AnnualSalesSummaryTable').on('click', '.view-graph', function() {
        let table = $('#AnnualSalesSummaryTable').DataTable();
        let row = $(this).closest('tr');
        let productName = table.row(row).data().ProductName;

        console.log("선택된 제품명:", productName);

        callAjax(
            'overview/AnnualGraph',
            JSON.stringify({ productName: productName }),
            function(response) {
                console.log("서버 응답 데이터:", response);

                if (response.statusCode === 'dataOK'){
                let salesData = response.jsonValue;

                // 데이터 준비
                let labels = salesData.map(data => data.SalesMonth); // X축 레이블 (월별)
                let totalQuantity = salesData.map(data => data.TotalQuantity); // 판매 수량 데이터
                let avgQuantity = salesData.map(data => data.AvgQuantity); // 평균 수량 데이터

                // 최대값 계산
                const maxTotalQuantity = Math.max(...totalQuantity);
                const maxAvgQuantity = Math.max(...avgQuantity);

                // 기존 차트 삭제
                if (window.annualSalesChart instanceof Chart) {
                    window.annualSalesChart.destroy();
                }

                // 차트 생성
                let ctx = document.getElementById('annualSalesChart').getContext('2d');
                window.annualSalesChart = new Chart(ctx, {
                    type: 'line', // 선 그래프
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: '판매 수량',
                                data: totalQuantity,
                                borderColor: 'blue', // 선 색상
                                borderWidth: 2, // 선 두께
                                fill: false,    // 채우기 없음
                                tension: 0.4,
                                yAxisID: 'y-axis-quantity' // 왼쪽 Y축에 매핑
                            },
                            {
                                label: '평균 수량',
                                data: avgQuantity,
                                borderColor: 'red',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.4,
                                yAxisID: 'y-axis-avgQuantity' // 오른쪽 Y축에 매핑
                            }
                        ]
                    },
                    options: {
                        responsive: true, // 반응형 여부
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: '년/월', // X축 제목
                                    font: {
                                        size: 20,
                                        weight: 'bold'
                                    }
                                },
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 45,
                                    minRotation: 0,
                                    font: {
                                        size: 20
                                    }
                                }
                            },
                            'y-axis-quantity': {
                                type: 'linear',
                                position: 'left', // 왼쪽 Y축
                                title: {
                                    display: true,
                                    text: '판매 수량 (개)',
                                    font: {
                                        size: 20,
                                        weight: 'bold'
                                    }
                                },
                                min: 0, // 최소값
                                max: Math.ceil(maxTotalQuantity * 1.2), // 최대값의 1.2배 설정
                                ticks: {
                                    stepSize: Math.ceil(maxTotalQuantity / 10),
                                    callback: value => `${value} 개`, // 단위 추가
                                    font: {
                                        size: 20
                                    }
                                }
                            },
                            'y-axis-avgQuantity': {
                                type: 'linear',
                                position: 'right', // 오른쪽 Y축
                                title: {
                                    display: true,
                                    text: '평균 수량 (개)',
                                    font: {
                                        size: 20,
                                        weight: 'bold'
                                    }
                                },
                                min: 0, // 최소값
                                max: Math.ceil(maxAvgQuantity * 1.2), // 최대값의 1.2배 설정
                                ticks: {
                                    stepSize: Math.ceil(maxAvgQuantity / 10),
                                    callback: value => `${value} 개`, // 단위 추가
                                    font: {
                                        size: 20
                                    }
                                },
                                grid: {
                                    drawOnChartArea: false // 오른쪽 Y축의 그리드 제거
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                enabled: true,
                                backgroundColor: '#000', // 툴팁 색상
                                titleFont: {
                                    size: 20,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 20
                                },
                                padding: 10, // 툴팁 내부 여백
                                callbacks: {
                                    label: function(context) {
                                        if (context.dataset.yAxisID === 'y-axis-quantity') {
                                            return `판매 수량: ${context.raw} 개`;
                                        } else if (context.dataset.yAxisID === 'y-axis-avgQuantity') {
                                            return `평균 수량: ${context.raw} 개`;
                                        }
                                    }
                                }
                            },
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 20,
                                        weight: 'bold'
                                    }
                                }
                            }
                        }
                    }
                });
                    } else {
                    console.error('서버 응답 상태 이상',response.statusCode);
                }

                // 모달 표시
                $('#graphModal').modal('show');
            },
        );
    });




    //DataTables
    let tblSearch = null;

    function YearOverviewList() {
        if (tblSearch == null) {
            tblSearch = $('#AnnualSalesSummaryTable').DataTable ({
                serverSide: true,
                processing: true,
                responsive: true,


                ajax: {
                    'url'          : 'overview/YearList',
                    'type'         : 'POST',
                    'datatype'     : 'application/json',
                    data: function (d) {
                        console.log(d);
                        d.searchValue = $('#SearchOverviewTable').val(); //검색 입력필드 전송값
                        d.length = 10;
                    },
                    'dataSrc'      : function (resData) {


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
                                console.log('overview/YearList Ajax Default Error');

                                break;
                        }
                    }

                },


                "columns": [

                    { "data": "ProductName",                                                                //상품명
                      "render": function(data,type,row) {
                        // 상품명을 클릭하여 월별 데이터를 볼 수 있도록 링크로 표시
                           return `<a href="#" class="Monthly-link" >${data}</a>`;
                      }},
                    { "data": 'TotalQuantity' },                                                            //연간 총 수량
                    { "data": "AveragePrice"  ,                                                              // 평균 단가
                      "render": function(data) {
                            return data ? Number(data).toLocaleString() : '-';
                      }
                      },
                    { "data": "TotalSales"   ,                                                               // 총 매출액
                      "render": function(data) {
                            return data ? Number(data).toLocaleString() : '-';
                      }},
                    { "data": "ResaleCount"},
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `<button type="button" class="view-graph" data-product="${row.ProductName}">View Graph</button>`;
                        }

                    }


                ],

                paging      : true,
               // lengthMenu  : [10, 20, 50, 75, 100],
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

    YearOverviewList();

    let monthlyTable = null;
    let selectedProductName = null;
    function MonthlyOverviewList() {
        if (monthlyTable == null) {
            monthlyTable = $('#MonthlySalesDetailTable').DataTable ({

                responsive: true,
                ajax: {
                    'url'          : 'overview/MonthlyList',
                    'type'         : 'POST',
                    'datatype'     : 'application/json',
                    data: function (d) {
                        console.log(d);
                        d.productName =selectedProductName;
                        console.log('11');
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
                                console.log('overview/Monthly Ajax Default Error');
                                console.log(resData);
                                break;
                        }
                    }

                },


                "columns": [

                    { "data": "SalesMonth" },                                     //월
                    { "data": 'TotalQuantity' },                                  //월 총수량
                    { "data": 'AvgQuantity' },                                     //월 평균수량
                    { "data": "AveragePrice"  ,                                   // 평균 단가
                        "render": function(data) {
                            return data ? Number(data).toLocaleString() : '-';
                        }
                    },
                    { "data": "TotalSales"   ,                                   // 총 사용량
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






    $('#AnnualSalesSummaryTable').on('click','.Monthly-link',function(e) {
        e.preventDefault(); //<a href='#'> 기본적으로 페이지 맨 위로 스크롤 방지
        selectedProductName = $(this).text(); // 클릭된 상품명 가져오기
        console.log('상품명',selectedProductName);
        MonthlyOverviewList();
        monthlyTable.ajax.reload();




            });






    // 연간 테이들 리로드 함수
    function tblReload(status) {
        if (tblSearch !== null ) {
            tblSearch.ajax.reload(null,status);//status = true -> 페이지 리셋 데이터 다시 로드 , status = false -> 페이지 유지 한채로 new 데이터 가져옴
        }
    }

    $('#SearchOverviewTable').on('keyup', function() {
        tblSearch.ajax.reload();
    });









</script>
<?= $this->endSection(); ?>
