<?php
session_start();
$id = $_SESSION['id'];

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'demo';
$port = 3307;

$conn = mysqli_connect($host, $username, $password, $database, $port);

$msquery = "SELECT * FROM Customer WHERE IDCust = '$id'";
$msresult = mysqli_query($conn, $msquery);
$row = mysqli_fetch_row($msresult);
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Customer Info</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <!-- SweetAlert2 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            padding: 20px;
            margin: 0;
        }

        .container-profile {
            max-width: 100%;
            margin: auto;
            padding: 20px;
        }

        .card-profile {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 20px;
        }

        .card-profile h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #555;
        }

        .card-profile table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .card-profile table th,
        .card-profile table td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .card-profile table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .button-container-profile {
            text-align: center;
        }

        .btn {
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
            left: 20px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .receipt-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .receipt-links li {
            margin-bottom: 10px;
        }

        .receipt-links a {
            color: #007BFF;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .receipt-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class='container-profile'>
        <!-- Combined Customer Information and Receipts card-profile -->
        <div class='card-profile'>
            <h2>Customer Information & Receipts</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <td><?php echo htmlspecialchars($row[0]) ?></td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td><?php echo htmlspecialchars($row[1]) ?></td>
                </tr>
                <tr>
                    <th>Sex</th>
                    <td><?php echo htmlspecialchars($row[2]) ?></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td><?php echo htmlspecialchars($row[3]) ?></td>
                </tr>
                <tr>
                    <th>Tel.</th>
                    <td><?php echo htmlspecialchars($row[4]) ?></td>
                </tr>
            </table>
            <h3 style='margin-top: 20px;'>Receipts</h3>
            <table class='receipt-table'>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Timestamp</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Receipt</th>
                        <th>PO</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>

        <!-- Back Button -->
        <div class='button-container-profile'>
            <a href='Product.php'><button class='btn'>Back</button></a>
        </div>
    </div>

    <script>
        function confirmCancel(idTransaction) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    handleCancelOrder(idTransaction);
                }
            });
        }
        function handleCancelOrder(idTransaction) {
            $.ajax({
                url: './service/cancel_order.php',
                method: 'POST',
                data: { idTransaction: idTransaction },
                success: function (response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire('Success!', 'Status updated successfully.', 'success').then(() => {
                            // filterOrders(statusId-1)
                            window.location.reload();
                        });

                    } else {
                        Swal.fire('Error!', result.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error!', 'Failed to cancel order.', 'error');
                }
            });
        }

        function handleCompleteOrder(idTransaction) {
            $.ajax({
                url: './service/complete_order.php',
                method: 'POST',
                data: { idTransaction: idTransaction },
                success: function (response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire('Success!', 'Status updated successfully.', 'success').then(() => {
                            // filterOrders(statusId-1)
                            window.location.reload();
                        });

                    } else {
                        Swal.fire('Error!', result.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error!', 'Failed to cancel order.', 'error');
                }
            });
        }

        function fetchAllOrders(customerId) {
            $.ajax({
                url: './service/get_all_order_by_id.php',
                method: 'POST',
                data: { id: customerId },
                success: function (response) {
                    $('.receipt-table tbody').html(response);
                },
                error: function () {
                    alert('Error fetching orders!');
                }
            });
        }

        fetchAllOrders(<?php echo $id ?>);
    </script>
</body>

</html>
<?php

mysqli_close($conn);
?>