<?php 
    session_start();
    session_unset();
    session_destroy();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            color: #4e73df;
            font-size: 28px;
            margin-bottom: 30px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f9f9f9;
            box-sizing: border-box;
        }

        input[type="text"]:focus {
            border-color: #4e73df;
            outline: none;
            background-color: #f1f1f1;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #4e73df;
            color: white;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #2e59d9;
        }

        .register-link {
            display: block;
            margin-top: 20px;
            font-size: 14px;
            color: #4e73df;
            text-decoration: none;
        }

        .register-link:hover {
            color: #2e59d9;
        }

    </style>
</head>

<body>
    <div class="container">
        <form action="authen.php" method="POST">
            <h1>กรุณากรอกรหัสลูกค้า</h1>
            <input type="text" name="id" placeholder="รหัสลูกค้า" required><br>
            <input type="submit" value="ยืนยัน">
            <a href="Insert_Form.html" class="register-link">สมัครสมาชิก</a>
        </form>
    </div>
</body>

</html>
