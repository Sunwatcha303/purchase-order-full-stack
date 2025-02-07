<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <style>
        .button-container-order {
            text-align: center;
        }

        .btn-order {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 0.9rem;
            position: fixed;
            top: 20px;
            right: 20px;
        }

        .btn-order:hover {
            background-color: #0056b3;
        }

        .product-sales-dashboard-chart,
        .customer-transaction-chart {
            display: flex;
        }

        .product-sales-dashboard-decimal {
            display: flex;
            justify-content: space-evenly;
        }

        .product-sales-dashboard-decimal-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
    <!-- Back Button -->
    <div class='button-container-order'>
        <a href='Order_management.php'><button class='btn-order'>Order</button></a>
    </div>
    <div>
        <input type="date" id="fromDate" placeholder="From Date">
        <input type="date" id="toDate" placeholder="To Date">
        <button id="fetchData" onclick="handleGetReportPeriod()">Show</button>
    </div>
    <div>
        <button id="fetchAll" onclick="handleGetReportAll()">All</button>
    </div>

    <h2>Product sales Report</h2>
    <div class="product-sales-dashboard-container">
        <h3>Top 10 Product</h3>
        <div class="product-sales-dashboard-chart">
            <div id="product-sales-dashboard-bar"></div>
            <div id="product-sales-dashboard-pie"></div>
        </div>
        <h3>Total sales</h3>
        <div class="product-sales-dashboard-decimal">
            <div class="product-sales-dashboard-decimal-item">
                <label for="product-sales-total-quantity-sold">total quantity sold</label>
                <div id="product-sales-total-quantity-sold">

                </div>
            </div>
            <div class="product-sales-dashboard-decimal-item">
                <label for="product-sales-total-revenue">sales total revenue</label>
                <div id="product-sales-total-revenue">

                </div>
            </div>
        </div>
    </div>
    <table class="product-sales-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Total Quantity Sold</th>
                <th>Total Revenue</th>
                <th>Number of Transactions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be injected here -->
        </tbody>
    </table>

    <h2>Customer Transaction Summary</h2>

    <div class="customer-transaction-dashboard-container">
        <div class="customer-transaction-chart">
            <div id="customer-transaction-bar1"></div>
            <div id="customer-transaction-bar2"></div>
        </div>
    </div>
    <table class="customer-transaction-table">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Total Transactions</th>
                <th>Total Transaction Value</th>
                <th>Total VAT</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be injected here -->
        </tbody>
    </table>

    <h2>Status Summary Report</h2>
    <div id="status-transaction-dashboard"></div>
    <table class="status-transaction-table">
        <thead>
            <tr>
                <th>Status Name</th>
                <th>Total Transactions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be injected here -->
        </tbody>
    </table>

    <script>
        function get_transaction(from, to) {
            $.ajax({
                url: './service/get_transaction_period.php', // Correct URL
                method: 'POST',
                data: { from: from, to: to },
                success: function (response) {
                    // Clear the existing table rows and chart
                    $('.customer-transaction-table tbody').empty();
                    $('#customer-transaction-dashboard').empty(); // Assuming this is the chart container

                    if (response.data.length === 1 && response.data[0].message) {
                        // Handle the case where no results were found
                        $('.customer-transaction-table tbody').html('<tr><td colspan="4">' + response.data[0].message + '</td></tr>');
                    } else {
                        // Loop through the response and create the table rows
                        response.data.forEach(function (item) {
                            var row = '<tr>' +
                                '<td>' + item.Custname + '</td>' +
                                '<td>' + item.TotalTransactions + '</td>' +
                                '<td>' + item.TotalTransactionValue + '</td>' +
                                '<td>' + item.TotalVAT + '</td>' +
                                '</tr>';
                            $('.customer-transaction-table tbody').append(row);
                        });

                        // Create the chart image
                        var chartImage = '<img src="' + response.valueChart + '" alt="Dashboard Chart">';
                        $('#customer-transaction-bar1').html(chartImage); // Assuming this is the container for the chart
                        var chartImage = '<img src="' + response.transactionChart + '" alt="Dashboard Chart">';
                        $('#customer-transaction-bar2').html(chartImage); // Assuming this is the container for the chart
                    }
                },
                error: function () {
                    alert('Error fetching transactions!');
                }
            });
        }

        function get_product_salse(from, to) {
            $.ajax({
                url: './service/get_product_sales_period.php', // Use the correct URL
                method: 'POST',
                data: { from: from, to: to },
                success: function (response) {
                    // Clear the existing table rows and chart
                    $('.product-sales-table tbody').empty();
                    $('#product-sales-dashboard').empty(); // Assuming this is the chart container

                    if (response.data.length === 1 && response.data[0].message) {
                        // Handle the case where no results were found
                        $('.product-sales-table tbody').html('<tr><td colspan="4">' + response.data[0].message + '</td></tr>');
                    } else {
                        // Loop through the response and create the table rows
                        response.data.forEach(function (item) {
                            var row = '<tr>' +
                                '<td>' + item.ProductName + '</td>' +
                                '<td>' + item.TotalQuantitySold + '</td>' +
                                '<td>' + item.TotalRevenue + '</td>' +
                                '<td>' + item.NumberOfTransactions + '</td>' +
                                '</tr>';
                            $('.product-sales-table tbody').append(row);
                        });

                        $('#product-sales-total-quantity-sold').text(response.data.reduce((acc, item) => acc + parseInt(item.TotalQuantitySold), 0));
                        $('#product-sales-total-revenue').text(response.data.reduce((acc, item) => acc + parseFloat(item.TotalRevenue), 0).toFixed(2));

                        // Create the chart image
                        var barChartImage = '<img src="' + response.barChart + '" alt="Dashboard Chart">';
                        $('#product-sales-dashboard-bar').html(barChartImage); // Assuming this is the container for the chart
                        var pieChartImage = '<img src="' + response.pieChart + '" alt="Dashboard Chart">';
                        $('#product-sales-dashboard-pie').html(pieChartImage); // Assuming this is the container for the chart
                    }
                },
                error: function () {
                    alert('Error fetching transactions!');
                }
            });
        }

        function get_status_transaction(from, to) {
            $.ajax({
                url: './service/get_status_transaction_period.php', // Correct URL
                method: 'POST',
                data: { from: from, to: to },
                success: function (response) {
                    // Clear the existing table rows and chart
                    $('.status-transaction-table tbody').empty();
                    $('#status-transaction-dashboard').empty(); // Assuming this is the chart container

                    if (response.data.length === 1 && response.data[0].message) {
                        // Handle the case where no results were found
                        $('.status-transaction-table tbody').html('<tr><td colspan="2">' + response.data[0].message + '</td></tr>');
                    } else {
                        // Loop through the response and create the table rows
                        response.data.forEach(function (item) {
                            var row = '<tr>' +
                                '<td>' + item.StatusName + '</td>' +
                                '<td>' + item.TotalTransactions + '</td>' +
                                '</tr>';
                            $('.status-transaction-table tbody').append(row);
                        });

                        // Create the chart image
                        var chartImage = '<img src="' + response.chart + '" alt="Dashboard Chart">';
                        $('#status-transaction-dashboard').html(chartImage); // Assuming this is the container for the chart
                    }
                },
                error: function () {
                    alert('Error fetching transactions!');
                }
            });
        }

        function handleGetReportPeriod() {
            const from = $('#fromDate').val();
            const to = $('#toDate').val();

            if (!from || !to) {
                alert('Please select both "From" and "To" dates.');
                return;
            }
            get_transaction(from, to);
            get_product_salse(from, to);
            get_status_transaction(from, to);
        }
        function handleGetReportAll() {
            get_transaction(" ", " ");
            get_product_salse(" ", " ");
            get_status_transaction(" ", " ");
        }
    </script>
</body>

</html>