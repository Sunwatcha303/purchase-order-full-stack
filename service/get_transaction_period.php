<?php
include '../repository/report.php';

// Get the 'from' and 'to' parameters from the request
$from = $_POST['from'];
$to = $_POST['to'];

// Fetch data from the repository
$result = Report_Repo::get_customer_transactions($from, $to);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['Custname']}</td>
                <td>{$row['TotalTransactions']}</td>
                <td>{$row['TotalTransactionValue']}</td>
                <td>{$row['TotalVAT']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No results found</td></tr>";
}
?>