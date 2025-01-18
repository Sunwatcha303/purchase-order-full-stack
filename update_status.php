<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idTransaction = $_POST['idTransaction'];
    $idStatus = $_POST['action'];

    // ดึง IDStatus ตามชื่อสถานะ
    // $sqlStatus = "SELECT IDStatus FROM Status WHERE StatusName = '$action'";
    // $resultStatus = mysqli_query($link, $sqlStatus);
    // $statusRow = mysqli_fetch_assoc($resultStatus);
    // $idStatus = $statusRow['IDStatus'];

    // อัปเดตสถานะใน Transaction
    $sqlUpdate = "UPDATE `Transaction` SET IDStatus = '$idStatus' WHERE IDtransaction = '$idTransaction'";
    mysqli_query($link, $sqlUpdate);

    header("Location: Backoffice.php");
}

mysqli_close($link);
?>
