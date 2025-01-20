<?php
include 'db_connect.php';

// ดึงรายการ Status ทั้งหมด
$sql_status = "SELECT IDStatus, StatusName FROM Status";
$result_status = mysqli_query($link, $sql_status);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 20%;
            background-color: #343a40;
            color: white;
            padding: 20px;
        }

        .sidebar h3 {
            color: #f8f9fa;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li a {
            color: #f8f9fa;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .sidebar ul li a.active {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
        }

        .sidebar ul li a:hover {
            background-color: #495057;
        }

        .content {
            flex: 1;
            padding: 20px;

        }

        table {
            margin-top: 20px;
        }

        #orderList {
            height: 100vh;
        }

        .btn btn-primary btn-sm {
            margin-right: 10px;
        }

        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h3>Status</h3>
        <ul id="statusList">
            <?php while ($row_status = mysqli_fetch_assoc($result_status)) { ?>
                <li>
                    <a href="#" onclick="filterOrders(<?php echo $row_status['IDStatus']; ?>)">
                        <?php echo $row_status['StatusName']; ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>

    <div class="content">
        <div id="orderList" class="table-responsive">

        </div>
    </div>

    <script>
        function filterOrders(statusId) {
            $('#statusList a').removeClass('active');

            $('#statusList a').filter(function () {
                return $(this).attr('onclick').includes(statusId);
            }).addClass('active');

            $.ajax({
                url: './service/get_orders_by_status.php',
                method: 'GET',
                data: { status: statusId },
                success: function (data) {
                    $('#orderList').html(data); // แสดงผลใน div#orderList
                },
                error: function () {
                    alert('Failed to fetch orders.');
                }
            });
        }

        function confirmUpdate(idTransaction, statusId) {
            let confirmText = '';

            if (statusId === '7') { // หากเป็นการยกเลิก
                confirmText = '✖✖ Cancel ✖✖';

            } else { // หากเป็นการยืนยัน
                confirmText = '✔✔ Submit ✔✔';
            }
            Swal.fire({
                title: 'Are you sure? ',
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateStatus(idTransaction, statusId);
                }
            });
        }

        function updateStatus(idTransaction, statusId) {
            const action = statusId; // Assign action from statusName
            $.ajax({
                url: './service/update_status.php',
                method: 'POST',
                data: { idTransaction, action },
                success: function (response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire('Success!', result.message, 'success').then(() => {
                            if (statusId != 7) {
                                filterOrders(statusId - 1)
                            } else {
                                filterOrders(statusId)
                            }
                        });

                    } else {
                        Swal.fire('Error!', result.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error!', 'Failed to update status.', 'error');
                }
            });
        }


        $(document).ready(function () {
            filterOrders(1); // 1 = Pending (สมมติว่า ID = 1)
        });
    </script>

</body>

</html>