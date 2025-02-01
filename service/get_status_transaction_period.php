<?php
include '../repository/report.php';

// Get the 'from' and 'to' parameters from the request
$from = $_POST['from'];
$to = $_POST['to'];

// Fetch data from the repository
$result = Report_Repo::get_transaction_status($from, $to);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["StatusName"] . "</td>
                <td>" . $row["TotalTransactions"] . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No results found</td></tr>";
}
?>