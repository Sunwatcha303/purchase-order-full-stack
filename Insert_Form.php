<?php
$conn = mysqli_connect("localhost", "root", "", "demo", 3307);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// รับค่าจากฟอร์ม
$a2 = $_POST['a2'];
$a3 = $_POST['a3'];
$a4 = $_POST['a4'];
$a5 = $_POST['a5'];

// SQL Query เพื่อแทรกข้อมูลลงในตาราง Customer
$query = "INSERT INTO Customer(Custname, Sex, Address, Tel) VALUES('$a2', '$a3', '$a4', '$a5')";

// เตรียมคำสั่ง SQL
$stmt = mysqli_prepare($conn, $query);

// ประมวลผลคำสั่ง SQL
if (mysqli_execute($stmt)) {
    // ดึง ID ที่ถูกแทรกเข้าไปในตาราง Customer
    $last_id = mysqli_insert_id($conn);
} else {
    // หากเกิดข้อผิดพลาด
    $last_id = false;
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิกสำเร็จ</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            color: #4e73df;
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }

        .btn {
            background-color: #4e73df;
            color: white;
            padding: 12px 20px;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #2e59d9;
        }

        .btn-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>สมัครสมาชิกสำเร็จ</h1>
    <?php if ($last_id): ?>
        <p>คุณได้รับ ID เป็น: <strong><?php echo $last_id; ?></strong></p>
    <?php else: ?>
        <p>เกิดข้อผิดพลาดในการสมัคร กรุณาลองใหม่</p>
    <?php endif; ?>
    
    <div class="btn-container">
        <a href="index.php" class="btn">กลับหน้าหลัก</a>
    </div>
</div>

</body>
</html>
