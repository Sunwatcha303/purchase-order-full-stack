<?php
require __DIR__ . '/../vendor/autoload.php';

include '../database.php';

use Mpdf\Mpdf;

class Order_Repo
{
    static public function GetAllOrderByIDCust($id)
    {
        $link = Database::connect();

        $prepTrans = "SELECT t.IDtransaction, t.Timestamp, s.StatusName FROM Transaction t INNER JOIN Status
        s ON t.IDStatus = s.IDStatus WHERE IDCust='$id'";
        $msresult = mysqli_query($link, $prepTrans);

        Database::close();

        return $msresult;
    }
    static function UpdateStatusOrder($idStatus, $idTransaction)
    {
        $link = Database::connect();

        $sqlUpdate = "UPDATE `Transaction` SET IDStatus = '$idStatus' WHERE IDtransaction = '$idTransaction'";
        mysqli_query($link, $sqlUpdate);

        Database::close();
    }

    static public function CompleteOrder($idStatus, $idTransaction)
    {
        $link = Database::connect();

        // Begin the database transaction
        mysqli_begin_transaction($link);

        try {
            // Update the transaction status
            $sqlUpdate = "UPDATE `Transaction` SET IDStatus = '$idStatus' WHERE IDtransaction = '$idTransaction'";
            $result = mysqli_query($link, $sqlUpdate);

            if (!$result) {
                throw new Exception("Error updating transaction status");
            }

            // Create the PDF for the transaction
            Order_Repo::CreatePDF($link, $idTransaction);

            // Commit the transaction if everything is successful
            mysqli_commit($link);
        } catch (Exception $e) {
            // Rollback in case of any error
            mysqli_rollback($link);
            echo "Error: " . $e->getMessage();
        } finally {
            // Close the database connection
            Database::close();
        }
    }

    static private function CreatePDF($link, $nextid)
    {
        try {
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

            // Calculate totals
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

            // Write HTML to the PDF
            $mpdf->WriteHTML($html);

            // Save the PDF to the 'reciepts' folder
            $mpdf->Output(__DIR__ . '/../reciepts/' . $nextid . '.pdf', 'F');
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