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

    <h2>Customer Transaction Summary</h2>

    <table border="1" class="customer-transaction-table">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Total Transactions</th>
                <th>Total Transaction Value</th>
                <th>Total VAT</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be populated here dynamically -->
        </tbody>
    </table>

    <h2>Product sales Report</h2>
    <table border="1" class="product-sales-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Total Quantity Sold</th>
                <th>Total Revenue</th>
                <th>Number of Transactions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be populated here dynamically -->
        </tbody>
    </table>

    <h2>Status Summary Report</h2>
    <table border="1" class="status-transaction-table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Total Transactions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be populated here dynamically -->
        </tbody>
    </table>

    <script>
        function get_transaction(from, to) {
            $.ajax({
                url: './service/get_transaction_period.php',
                method: 'POST',
                data: { from: from, to: to },
                success: function (response) {
                    $('.customer-transaction-table tbody').html(response);
                },
                error: function () {
                    alert('Error fetching transactions!');
                }
            });
        }
        function get_product_salse(from, to) {
            $.ajax({
                url: './service/get_product_sales_period.php',
                method: 'POST',
                data: { from: from, to: to },
                success: function (response) {
                    $('.product-sales-table tbody').html(response);
                },
                error: function () {
                    alert('Error fetching transactions!');
                }
            });
        }
        function get_status_transaction(from, to) {
            $.ajax({
                url: './service/get_status_transaction_period.php',
                method: 'POST',
                data: { from: from, to: to },
                success: function (response) {
                    $('.status-transaction-table tbody').html(response);
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