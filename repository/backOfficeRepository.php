<?php
require __DIR__ . '/../vendor/autoload.php';

include '../database.php';

use Mpdf\Mpdf;

class Backoffice_Repo
{
    static public function GetAllOrderByStatus($status_id)
    {
        $link = Database::connect();

        $sql = "SELECT t.IDtransaction, t.Qty, t.Totalprice, c.Custname 
        FROM `Transaction` t
        JOIN `Customer` c ON t.IDCust = c.IDCust
        WHERE t.IDStatus = $status_id";

        $result = mysqli_query($link, $sql);
        Database::close();
        return $result;
    }

    static public function updateStatus($idTransaction, $idStatus)
    {
        $sqlUpdate = "UPDATE `Transaction` SET IDStatus = '$idStatus' WHERE IDtransaction = '$idTransaction'";
        $link = Database::connect();

        mysqli_query($link, $sqlUpdate);
        Database::close();
    }

    static public function GetTransactionDetails($transaction_id)
    {
        $link = Database::connect();

        $sql = "SELECT 
                td.IDProduct, 
                p.ProductName, 
                td.Qty, 
                p.PricePerUnit, 
                (td.Qty * p.PricePerUnit) AS TotalPrice 
            FROM 
                TransactionDetail td
            INNER JOIN 
                Product p 
            ON 
                td.IDProduct = p.IDProduct
            WHERE 
                td.IDtransaction = '$transaction_id'";

        $result = mysqli_query($link, $sql);

        if (!$result) {
            throw new Exception("Error fetching transaction details: " . mysqli_error($link));
        }

        Database::close();
        return $result;
    }


    static public function CreatePDF($nextid)
    {
        try {
            $link = Database::connect();
            // Create an instance of mPDF with custom configuration
            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];
            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new \Mpdf\Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    __DIR__ . '/../font', // Directory for custom fonts
                ]),
                'fontdata' => $fontData + [
                    'th_sarabun' => [
                        'R' => 'Sarabun-Regular.ttf', // Font for Thai language
                    ],
                ],
                'default_font' => 'th_sarabun',
                'encoding' => 'UTF-8',
                'lang' => 'th', // Language setting for Thai
            ]);

            // Fetch transaction and customer details
            $trans_query = "SELECT t.IDtransaction, t.Qty, t.Totalprice, t.Vat, t.Timestamp, c.IDCust, c.Custname, c.Address, c.Tel 
                        FROM Transaction t 
                        INNER JOIN Customer c ON c.IDCust = t.IDCust 
                        WHERE IDtransaction = '$nextid'";

            // Fetch product details for the transaction
            $detail_query = "SELECT t.IDProduct, p.ProductName, t.Qty, p.PricePerUnit, (Qty * p.PricePerUnit) as Sum
                         FROM TransactionDetail t 
                         INNER JOIN Product p ON p.IDProduct = t.IDProduct 
                         WHERE t.IDtransaction = '$nextid'";

            $trans_result = mysqli_query($link, $trans_query);
            $detail_result = mysqli_query($link, $detail_query);
            Database::close();
            if (!$trans_result || !$detail_result) {
                throw new Exception("Error fetching transaction or product details");
            }

            $transaction_data = mysqli_fetch_assoc($trans_result);
            $customer_data = $transaction_data; // Assuming customer info is part of the same row as the transaction

            // Start creating the HTML content for the purchase order
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
                <h1>ใบเบิกสินค้า </h1>
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

            // Calculate totals
            $vat = $transaction_data['Vat'];
            $total = $transaction_data["Totalprice"];
            $subtotal = $total - $vat;

            $html .= "
                </tbody>
            </table>";

            // Write HTML to the PDF
            $mpdf->WriteHTML($html);

            // Save the PDF to the 'reciepts' folder
            $mpdf->Output(__DIR__ . '/../pickup/' . $nextid . '.pdf', 'F');
            // Uncomment this to display the PDF directly in the browser
            // $mpdf->Output();

        } catch (\Mpdf\MpdfException $e) {
            // Catch any mPDF-related exceptions
            throw new Exception("mPDF error: " . $e->getMessage());
        } catch (Exception $e) {
            // Catch any other exceptions
            throw new Exception("Error: " . $e->getMessage());
        }
    }

}
?>