<?php
session_start();
$idCust = $_SESSION['id'];
$cart = $_SESSION['cart'];

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'demo';
$port = 3307;

$conn = mysqli_connect($host, $username, $password, $database, $port);

// Fetch the latest transaction ID
$msquery = "SELECT IDtransaction FROM Transaction ORDER BY IDtransaction DESC LIMIT 1";
$msresult = mysqli_query($conn, $msquery);
$row = mysqli_fetch_row($msresult);

// Calculate the next IDtransaction
$nextid = $row ? $row[0] + 1 : 1;

// Prepare queries
$values = [];
$updProd = [];
$total = 0;
$qty = 0;
$msg = "";
$success = false;

try {
    foreach ($cart as $item) {
        $values[] = "($nextid, " . (int) $item['id'] . ", " . (int) $item['qty'] . ")";
        $total += $item["price"] * $item["qty"];
        $qty += $item["qty"];
        $updProd[] = "UPDATE Product SET ReserveQty = ReserveQty - " . (int) $item['qty'] . " WHERE IDProduct = " . (int) $item['id'] . " AND ReserveQty >= " . (int) $item['qty'];
    }
    // Calculate VAT and total price
    $vat = $total * 0.07 / 1.07;
    // Batch execution using a transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert into Transaction
        $insertTrans = "INSERT INTO Transaction(IDtransaction, IDCust, Qty, Vat, Totalprice, IDStatus) 
                        VALUES($nextid, $idCust, $qty, $vat, $total, 1)";
        if (!mysqli_query($conn, $insertTrans)) {
            throw new Exception("Error inserting transaction: " . mysqli_error($conn));
        }

        // Insert into TransactionDetail
        $preDetail = "INSERT INTO TransactionDetail(IDtransaction, IDProduct, Qty) VALUES " . implode(", ", $values);
        if (!mysqli_query($conn, $preDetail)) {
            throw new Exception("Error inserting transaction details: " . mysqli_error($conn));
        }

        // Update Product quantities
        foreach ($updProd as $updateQuery) {
            if (!mysqli_query($conn, $updateQuery)) {
                throw new Exception("Error updating product quantities: " . mysqli_error($conn));
            }
        }

        // Commit the transaction
        mysqli_commit($conn);
        $msg = "สั่งซื้อสำเร็จ";
        $success = true;
    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_rollback($conn);
        $msg = $e->getMessage();
    }
} catch (Exception $e) {
    $msg = $e->getMessage();
}


// Close the connection
mysqli_close($conn);

// Unset the cart
unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            text-align: center;
        }

        h2 {
            color: #333;
        }

        p {
            margin: 20px 0;
            font-size: 18px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            color: white;
        }

        .button.cancel {
            background-color: #dc3545;
        }

        .button.cancel:hover {
            background-color: #c82333;
        }

        .button.confirm {
            background-color: #28a745;
        }

        .button.confirm:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2><?php echo $msg ?></h2>
        <p>กลับไปยังหน้าหลัก</p>
        <div class="buttons">
            <a href="Order.php">
                <?php
                if ($success) {
                    echo '<button class="button confirm">ตกลง</button>';
                } else {
                    echo '<button class="button cancel">ตกลง</button>';
                }
                ?>
            </a>
        </div>
    </div>
</body>

</html>