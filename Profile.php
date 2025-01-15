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

echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Customer Info</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            padding: 20px;
            margin: 0;
        }
        .container-profile {
            max-width: 800px;
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
        .card-profile table th, .card-profile table td {
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
                <tr><th>ID</th><td>" . htmlspecialchars($row[0]) . "</td></tr>
                <tr><th>Name</th><td>" . htmlspecialchars($row[1]) . "</td></tr>
                <tr><th>Sex</th><td>" . htmlspecialchars($row[2]) . "</td></tr>
                <tr><th>Address</th><td>" . htmlspecialchars($row[3]) . "</td></tr>
                <tr><th>Tel.</th><td>" . htmlspecialchars($row[4]) . "</td></tr>
            </table>
            <h3 style='margin-top: 20px;'>Receipts</h3>
            <ul class='receipt-links'>
";
$prepTrans = "SELECT IDtransaction, Timestamp FROM Transaction WHERE IDCust='$id'";
$msresult = mysqli_query($conn, $prepTrans);
while ($row = mysqli_fetch_row($msresult)) {
    echo "<li><a href='reciepts/" . htmlspecialchars($row[0]) . ".pdf'>" . htmlspecialchars($row[0]) . " - " . htmlspecialchars($row[1]) . "</a></li>";
}

echo "
            </ul>
        </div>

        <!-- Back Button -->
        <div class='button-container-profile'>
            <a href='Order.php'><button class='btn'>Back</button></a>
        </div>
    </div>
</body>
</html>
";

mysqli_close($conn);
?>