<?php
include '../repository/report.php';
include '../repository/dashboard.php';

$from = $_POST['from'];
$to = $_POST['to'];

$result = Report_Repo::get_product_sale($from, $to);

$data = [];
$productNames = [];
$totalRevenue = [];
$totalQuantities = [];

if ($result->num_rows > 10) {
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'ProductName' => $row['ProductName'],
            'TotalQuantitySold' => $row['TotalQuantitySold'],
            'TotalRevenue' => $row['TotalRevenue'],
            'NumberOfTransactions' => $row['NumberOfTransactions']
        ];
        if ($count++ < 10) {
            $productNames[] = $row['ProductName'];
            $totalRevenue[] = $row['TotalRevenue'];
            $totalQuantities[] = $row['TotalQuantitySold'];
        }
    }
} else {
    $data[] = ['message' => 'No results found'];
}

if (empty($productNames)) {
    header('Content-Type: application/json');
    echo json_encode(['data' => $data, 'chart' => null]);
    exit;
}

$barBase64Image = Dashboard_Repo::createBarChart("Top 10 Product Revenue", $productNames, $totalRevenue, );
$pieBase64Image = Dashboard_Repo::createPieChart("Top 10 Product Quantity Distribute", $productNames, $totalQuantities);

header('Content-Type: application/json');
echo json_encode([
    'data' => $data,
    'barChart' => 'data:image/png;base64,' . $barBase64Image,
    'pieChart' => 'data:image/png;base64,' . $pieBase64Image
]);

?>