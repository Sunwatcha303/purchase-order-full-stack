<?php
include '../repository/backOfficeRepository.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idTransaction = $_POST['idTransaction'];
    $idStatus = $_POST['action'];
    Backoffice_Repo::updateStatus($idTransaction, $idStatus);
    if($idStatus == 2){
        Backoffice_Repo::CreatePDF($idTransaction);
    }
    if($idStatus == 7){
        Backoffice_Repo::CancelOrder($idTransaction);
    }
}
?>
