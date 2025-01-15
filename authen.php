<?php
if (empty($_POST['id'])) {
    header('Location: index.php');
    exit;
}
session_start();
$id = $_POST['id'];
$_SESSION['id'] = $id;
$link = mysqli_connect("localhost", "root", "", "demo", 3307);
$query = "SELECT * FROM Customer WHERE IDCust = $id LIMIT 1";
$result = mysqli_query($link, $query);
if (mysqli_num_rows($result) > 0) {
    header('Location: Order.php');
    exit;
}
session_unset();
session_destroy();
header('Location: Insert_Form.html');
?>