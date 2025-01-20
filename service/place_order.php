<?php
include '../repository/order.php';
session_start();

// header('Content-Type: application/json');

$response = [];

if (isset($_SESSION['id'], $_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $userId = $_SESSION['id'];
    $cart = $_SESSION['cart'];

    if (Order_Repo::PlaceOrder($userId, $cart)) {
        $response = [
            'success' => true,
            'message' => 'Order placed successfully.',
        ];
        unset($_SESSION['cart']);
    } else {
        $response = [
            'success' => false,
            'message' => 'Failed to place order. Please try again.',
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid session or empty cart.',
    ];
}

echo json_encode($response);
?>