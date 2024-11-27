<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Annual Sales Graph</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<h1>Annual Sales Graph</h1>
<canvas id="annualSalesChart" width="800" height="400"></canvas>

<script>
    // PHP에서 전달받은 데이터를 JavaScript 변수에 저장
    const salesData = <?php echo $salesData; ?>;

    // 월별 총 매출 데이터를 추출하여 차트용 데이터셋 생성
    const labels = salesData.map(data => data.SalesMonth); // ex: ["2023-01", "2023-02", ...]
    const totalSales = salesData.map(data => data.TotalSales);
    const totalQuantity = salesData.map(data => data.TotalQuantity);
    const averagePrice = salesData.map(data => data.AveragePrice);

    // Chart.js로 연간 그래프 생성
    const ctx = document.getElementById('annualSalesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Sales',
                    data: totalSales,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: false
                },
                {
                    label: 'Total Quantity',
                    data: totalQuantity,
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2,
                    fill: false
                },
                {
                    label: 'Average Price',
                    data: averagePrice,
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 2,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Month' } },
                y: { title: { display: true, text: 'Value' } }
            }
        }
    });
</script>
</body>
</html>
