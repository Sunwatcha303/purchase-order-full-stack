<?php
include "../repository/order.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Order_Repo::UpdateStatusOrder(7, $_POST['idTransaction']);
    echo json_encode(['success' => true, 'message' => 'Order canceled successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>