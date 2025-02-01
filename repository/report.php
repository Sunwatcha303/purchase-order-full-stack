<?php
include '../database.php';

class Report_Repo
{
    static public function get_customer_transactions($from, $to)
    {
        $link = Database::connect();

        $sql = "SELECT 
                c.Custname,
                COUNT(t.IDtransaction) AS TotalTransactions,
                SUM(t.Totalprice) AS TotalTransactionValue,
                SUM(t.Vat) AS TotalVAT
            FROM Customer c
            JOIN Transaction t ON c.IDCust = t.IDCust ";

        if ($from !== " " && $to !== " ") {
            $sql .= " WHERE Timestamp BETWEEN ? AND ?";
        }

        $sql .= " GROUP BY c.IDCust
            ORDER BY TotalTransactions DESC";

        $stmt = mysqli_prepare($link, $sql);
        if (!$stmt) {
            Database::close();
            throw new Exception("Failed to prepare SQL statement: " . mysqli_error($link));
        }
        if ($from !== " " && $to !== " ") {
            mysqli_stmt_bind_param($stmt, "ss", $from, $to);
        }
        if (!mysqli_stmt_execute($stmt)) {
            Database::close();
            throw new Exception("Failed to execute SQL statement: " . mysqli_stmt_error($stmt));
        }

        $result = mysqli_stmt_get_result($stmt);

        Database::close();
        return $result;
    }

    static public function get_product_sale($from, $to)
    {
        $link = Database::connect();

        $sql = "SELECT 
                    p.ProductName,
                    SUM(td.Qty) AS TotalQuantitySold,
                    SUM(td.Qty * p.PricePerUnit) AS TotalRevenue,
                    COUNT(DISTINCT t.IDtransaction) AS NumberOfTransactions
                FROM Product p
                JOIN TransactionDetail td ON p.IDProduct = td.IDProduct
                JOIN Transaction t ON td.IDtransaction = t.IDtransaction";


        if ($from !== " " && $to !== " ") {
            $sql .= " WHERE Timestamp BETWEEN ? AND ?";
        }

        $sql .= " GROUP BY p.IDProduct
                ORDER BY TotalRevenue DESC";

        $stmt = mysqli_prepare($link, $sql);
        if (!$stmt) {
            Database::close();
            throw new Exception("Failed to prepare SQL statement: " . mysqli_error($link));
        }
        if ($from !== " " && $to !== " ") {
            mysqli_stmt_bind_param($stmt, "ss", $from, $to);
        }
        if (!mysqli_stmt_execute($stmt)) {
            Database::close();
            throw new Exception("Failed to execute SQL statement: " . mysqli_stmt_error($stmt));
        }

        $result = mysqli_stmt_get_result($stmt);

        Database::close();
        return $result;
    }

    static public function get_transaction_status($from, $to)
    {
        $link = Database::connect();

        $sql = "SELECT 
            s.StatusName,
            COUNT(t.IDtransaction) AS TotalTransactions
        FROM Status s
        LEFT JOIN Transaction t ON s.IDStatus = t.IDStatus";

        if ($from !== " " && $to !== " ") {
            $sql .= " WHERE Timestamp BETWEEN ? AND ?";
        }

        $sql .= " GROUP BY s.IDStatus
        ORDER BY TotalTransactions DESC";

        $stmt = mysqli_prepare($link, $sql);
        if (!$stmt) {
            Database::close();
            throw new Exception("Failed to prepare SQL statement: " . mysqli_error($link));
        }
        if ($from !== " " && $to !== " ") {
            mysqli_stmt_bind_param($stmt, "ss", $from, $to);
        }
        if (!mysqli_stmt_execute($stmt)) {
            Database::close();
            throw new Exception("Failed to execute SQL statement: " . mysqli_stmt_error($stmt));
        }

        $result = mysqli_stmt_get_result($stmt);

        Database::close();
        return $result;
    }
}
?>