<?php
include '../repository/report.php';
include '../repository/dashboard.php';

// Get the 'from' and 'to' parameters from the request
$from = $_POST['from'];
$to = $_POST['to'];

// Fetch data from the repository
$result = Report_Repo::get_transaction_status($from, $to);

// Prepare an array to hold the data for the table and chart
$data = [];
$statusNames = [];
$totalTransactions = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'StatusName' => $row['StatusName'],
            'TotalTransactions' => $row['TotalTransactions']
        ];
        $statusNames[] = $row['StatusName'];
        $totalTransactions[] = $row['TotalTransactions'];
    }
} else {
    $data[] = ['message' => 'No results found'];
}

$base64Image = Dashboard_Repo::createBarChart("Transaction Status", $statusNames, $totalTransactions);

// Return data as JSON along with the chart
header('Content-Type: application/json');
echo json_encode([
    'data' => $data,
    'chart' => 'data:image/png;base64,' . $base64Image
]);

?>