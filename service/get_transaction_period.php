<?php
include '../repository/report.php';
include '../repository/dashboard.php';
// Get the 'from' and 'to' parameters from the request
$from = $_POST['from'];
$to = $_POST['to'];

// Fetch data from the repository
$result = Report_Repo::get_customer_transactions($from, $to);

// Prepare an array to hold the data for the table and chart
$data = [];
$customerNames = [];
$totalTransactions = [];
$totalTransactionValues = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'Custname' => $row['Custname'],
            'TotalTransactions' => $row['TotalTransactions'],
            'TotalTransactionValue' => $row['TotalTransactionValue'],
            'TotalVAT' => $row['TotalVAT']
        ];
        $customerNames[] = $row['Custname'];
        $totalTransactions[] = $row['TotalTransactions'];
        $totalTransactionValues[] = $row['TotalTransactionValue'];
    }
} else {
    $data[] = ['message' => 'No results found'];
}

$valueBase64Image = Dashboard_Repo::createBarChart("Customer Values", $customerNames, $totalTransactionValues);
$transactionBase64Image = Dashboard_Repo::createBarChart("Customer Transactions", $customerNames, $totalTransactions);

// Return data as JSON along with the chart
header('Content-Type: application/json');
echo json_encode([
    'data' => $data,
    'valueChart' => 'data:image/png;base64,' . $valueBase64Image,
    'transactionChart' => 'data:image/png;base64,' . $transactionBase64Image

]);

?>