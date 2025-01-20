<?php
include '../repository/backOfficeRepository.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idTransaction = $_POST['idTransaction'] ?? null;
    $idStatus = $_POST['action'] ?? null;

    if ($idTransaction && $idStatus) {
        Backoffice_Repo::updateStatus($idTransaction, $idStatus);
        $response = [
            'success' => true,
            'message' => 'Status updated successfully.' 

        ];

        switch ($idStatus) {
            case 2: 
                Backoffice_Repo::CreatePDF($idTransaction);
                break;
            case 7: 
                Backoffice_Repo::CancelOrder($idTransaction);
                break;
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Invalid input. Missing idTransaction or action.',
        ];
    }
    echo json_encode($response);
}

?>
