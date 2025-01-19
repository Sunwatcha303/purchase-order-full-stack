<?php
$link = mysqli_connect("localhost", "root", "", "demo", 3307);

// Check connection
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

?>