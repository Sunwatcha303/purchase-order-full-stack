<?php
include '../repository/report.php';

// Get the 'from' and 'to' parameters from the request
$from = $_POST['from'];
$to = $_POST['to'];

// Fetch data from the repository
$result = Report_Repo::get_product_sale($from, $to);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["ProductName"] . "</td>
                <td>" . $row["TotalQuantitySold"] . "</td>
                <td>" . $row["TotalRevenue"] . "</td>
                <td>" . $row["NumberOfTransactions"] . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No results found</td></tr>";
}
?>