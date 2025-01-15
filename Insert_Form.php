<?php
$conn = mysqli_connect("localhost", "root", "", "demo", 3307);
$a1 = $_POST['a1'];
$a2 = $_POST['a2'];
$a3 = $_POST['a3'];
$a4 = $_POST['a4'];
$a5 = $_POST['a5'];
$query = "INSERT INTO Customer(IDCust,Custname,Sex,Address,Tel) VALUES('$a1','$a2','$a3','$a4','$a5')";
$stmt = mysqli_prepare($conn, $query);

if (!mysqli_execute($stmt)) {
    echo "error";
} else {
    echo "<a href=index.php><input type=button value=เสร็จสิ้น></input></a>";
}

mysqli_close($conn);
?>