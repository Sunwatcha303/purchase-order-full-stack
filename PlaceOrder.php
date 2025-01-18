<?php
require_once __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

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
        $updProd[] = "UPDATE Product SET StockQty = StockQty - " . (int) $item['qty'] . " WHERE IDProduct = " . (int) $item['id'] . " AND StockQty >= " . (int) $item['qty'];
    }
    // Calculate VAT and total price
    $vat = $total * 0.07 / 1.07;
    // Batch execution using a transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert into Transaction
        $insertTrans = "INSERT INTO Transaction(IDtransaction, IDCust, Qty, Vat, Totalprice) 
                        VALUES($nextid, $idCust, $qty, $vat, $total)";
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

        try {
            // สร้าง instance ของ mPDF
            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];
            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];
            $mpdf = new \Mpdf\Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    __DIR__ . '/font', // โฟลเดอร์ที่เก็บฟอนต์
                ]),
                'fontdata' => $fontData + [
                    'th_sarabun' => [
                        'R' => 'Sarabun-Regular.ttf', // ฟอนต์สำหรับแสดงภาษาไทย
                    ],
                ],
                'default_font' => 'th_sarabun',
                'encoding' => 'UTF-8', // ตั้งค่ารหัสเป็น UTF-8
                'lang' => 'th', // ภาษาไทย
            ]);

            // Query transaction and customer details
            $trans_query = "SELECT t.IDtransaction, t.Qty, t.Totalprice, t.Vat, t.Timestamp, c.IDCust, c.Custname, c.Address, c.Tel 
                            FROM Transaction t 
                            INNER JOIN Customer c ON c.IDCust = t.IDCust 
                            WHERE IDtransaction = '$nextid'";

            // Query transaction details (products)
            $detail_query = "SELECT t.IDProduct, p.ProductName, t.Qty, p.PricePerUnit, Qty * p.PricePerUnit as Sum
                             FROM TransactionDetail t 
                             INNER JOIN Product p ON p.IDProduct = t.IDProduct 
                             WHERE t.IDtransaction = '$nextid'";

            $trans_result = mysqli_query($conn, $trans_query);
            $detail_result = mysqli_query($conn, $detail_query);

            $transaction_data = mysqli_fetch_assoc($trans_result);
            $customer_data = $transaction_data; // Assuming the customer info is in the same row as the transaction

            // Start creating the HTML for the PO
            $html = "
                <style>
                    body { font-family: 'th_sarabun'; }
                    h1, h3 { text-align: center; }
                    table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
                    th { border: 1px solid #000; padding: 8px; text-align: center; }
                    td { border-left: 1px solid #000; border-right: 1px solid #000; padding: 8px; text-align: center; }
                    .header, .footer { text-align: right; margin: 20px 0; }
                    .totals { text-align: right; margin-top: 10px; }
                </style>
                <div class='header'>
                    <h1>ใบสั่งซื้อ (Purchase Order)</h1>
                    <p>เลขที่ใบสั่งซื้อ: PO-{$transaction_data['IDtransaction']}</p>
                    <p>วันที่: {$transaction_data['Timestamp']}</p>
                </div>
                <div>
                    <strong>ลูกค้า:</strong> {$customer_data['Custname']} <br>
                    <strong>ที่อยู่:</strong> {$customer_data['Address']} <br>
                    <strong>เบอร์โทร:</strong> {$customer_data['Tel']}
                </div>
                <br>
                <h3>รายละเอียดสินค้า</h3>
                <table>
                    <thead>
                        <tr>
                            <th>รหัสสินค้า</th>
                            <th>ชื่อสินค้า</th>
                            <th>จำนวน</th>
                            <th>ราคาต่อหน่วย</th>
                            <th>รวม</th>
                        </tr>
                    </thead>
                    <tbody>";
            while ($detail_data = mysqli_fetch_assoc($detail_result)) {
                $html .= "
                    <tr>
                        <td>{$detail_data['IDProduct']}</td>
                        <td>{$detail_data['ProductName']}</td>
                        <td>{$detail_data['Qty']}</td>
                        <td>" . number_format($detail_data['PricePerUnit'], 2) . "</td>
                        <td>" . number_format($detail_data['Sum'], 2) . "</td>
                    </tr>";
            }
            $vat = $transaction_data['Vat'];
            $total = $transaction_data["Totalprice"];
            $subtotal = $total - $vat;

            $html .= "
                    </tbody>
                </table>
                <div class='totals'>
                    <p><strong>ราคาก่อนรวมภาษีมูลค่าเพิ่ม :</strong> " . number_format($subtotal, 2) . " บาท</p>
                    <p><strong>ภาษีมูลค่าเพิ่ม (VAT 7%) :</strong> " . number_format($vat, 2) . " บาท</p>
                    <p><strong>จำนวนเงินรวมทั้งหมด :</strong> " . number_format($total, 2) . " บาท</p>
                </div>";

            $mpdf->WriteHTML($html);

            // Save the PDF to the 'reciepts' folder
            $mpdf->Output(__DIR__ . '/reciepts/' . $nextid . '.pdf', 'F');
            // $mpdf->Output(); // Uncomment this to display the PDF directly in the browser
        } catch (\Mpdf\MpdfException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo $e->getMessage();
        }
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