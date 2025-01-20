<?php
include '../repository/backOfficeRepository.php';

$status_id = isset($_GET['status']) ? intval($_GET['status']) : 0;

switch ($status_id) {
    case 1:
        $status_name = "Pending &#128340;"; // 🕰️
        break;
    case 2:
        $status_name = "Approved &#x2705;"; // ✅
        break;
    case 3:
        $status_name = "Packing &#128230;"; // 📦
        break;
    case 4:
        $status_name = "Shipping &#128666;"; // 🚚
        break;
    case 5:
        $status_name = "Completed &#128230 &#x2705;"; // ✅
        break;
    case 6:
        $status_name = "Rejected &#10060;"; // ❌
        break;
    case 7:
        $status_name = "Cancelled &#128683;"; // 🛑
        break;
    default:
        $status_name = "Unknown &#x003F;"; // ❓
        break;
}



$result = Backoffice_Repo::GetAllOrderByStatus($status_id);

if (mysqli_num_rows($result) > 0) {
    echo "<h1 class='text-center mb-4'>$status_name</h1>
    <table class='table table-bordered table-striped'>
        <thead class='table-dark'>
            <tr>
                <th>Transaction ID</th>
                <th>Customer Name</th>
                <th>Order Details</th>
                <th>Total Quantity</th>
                <th>Total Price</th>";
    if ($status_id < 4) {
        echo "<th>Actions</th>";
    }
    echo "  </tr>
        </thead>
        <tbody>"; 

    while ($row = mysqli_fetch_assoc($result)) {
        $transaction_id = $row['IDtransaction'];
        $details = Backoffice_Repo::GetTransactionDetails($transaction_id);

        // Prepare product details as a dropdown list
        $product_details = "<div class='dropdown'>
            <button class='btn btn-info btn-sm dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
                View Order Details
            </button>
            <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>";
        
        while ($detail = mysqli_fetch_assoc($details)) {
            $product_details .= "
            <li><strong>{$detail['ProductName']}</strong> - Quantity: {$detail['Qty']}</li>";
        }

        $product_details .= "</ul></div>"; // Close dropdown menu div

        $nextid = $status_id + 1;
        echo "<tr>
            <td>{$row['IDtransaction']}</td>
            <td>{$row['Custname']}</td>
            <td>$product_details</td>
            <td>{$row['Qty']}</td>
            <td>\${$row['Totalprice']}</td>";
        if ($status_id < 4) {
            echo "<td>
                <button class='btn btn-success btn-sm' onclick=\"confirmUpdate($transaction_id, '$nextid')\">Submit</button>
                <button class='btn btn-danger btn-sm' onclick=\"confirmUpdate($transaction_id, '7')\">Cancel</button>";
                if ($status_id == 2) {
                echo "<button class='btn  btn-sm' onclick=\"window.location.href='./pickup/$transaction_id.pdf'\">Download PDF</button>";
            }
            echo "</td>";
        }
        echo "</tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p class='text-danger text-center'>No orders found.</p>";
}
?>
<?  