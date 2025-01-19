<?php
include 'db_connect.php';

$status_id = isset($_GET['status']) ? intval($_GET['status']) : 0;


switch ($status_id) {
    case 1:
        $status_name = "Pending";
        break;
    case 2:
        $status_name = "Approved";
        break;
    case 3:
        $status_name = "Shipping";
        break;
    case 4:
        $status_name = "Packing";
        break;
    case 5:
        $status_name = "Completed";
        break;
    case 6:
        $status_name = "Rejected";
        break;
    case 7:
        $status_name = "Cancelled";
        break;
    default:
        $status_name = "Unknown";
        break;
}

$sql = "SELECT t.IDtransaction, t.Qty, t.Totalprice, c.Custname 
        FROM `Transaction` t
        JOIN `Customer` c ON t.IDCust = c.IDCust
        WHERE t.IDStatus = $status_id";

$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<h1>$status_name</h1>
    <table class='table table-bordered table-striped'>
            <thead class='table-dark'>
                <tr>
                    <th>Transaction ID</th>
                    <th>Customer Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>";
    if ($status_id < 5) {
        echo "<th>Actions</th>";
    }
    echo "      </tr>
            </thead>
            <tbody>";

    while ($row = mysqli_fetch_assoc($result)) {
        $nextid = $status_id + 1;
        echo "<tr>
                <td>{$row['IDtransaction']}</td>
                <td>{$row['Custname']}</td>
                <td>{$row['Qty']}</td>
                <td>\${$row['Totalprice']}</td>
                ";
        if ($status_id < 5) {
            echo "
                <td>
                <button class='btn btn-success btn-sm' onclick=\"confirmUpdate({$row['IDtransaction']}, '$nextid')\">Submit</button>
                <button class='btn btn-danger btn-sm' onclick=\"confirmUpdate({$row['IDtransaction']}, '6')\">Cancel</button>
                </td>
            </tr>";
        }
    }

} else {
    echo "<p class='text-danger'>No orders found.</p>";
}
?>