<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Order</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            text-align: center;
        }

        h2 {
            color: #333;
        }

        p {
            margin: 20px 0;
            font-size: 18px;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            color: white;
        }

        .button.cancel {
            background-color: #dc3545;
        }

        .button.cancel:hover {
            background-color: #c82333;
        }

        .button.confirm {
            background-color: #28a745;
        }

        .button.confirm:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>ยืนยันการสั่งซื้อ</h2>
        <p>คุณต้องการสั่งซื้อสินค้าหรือไม่?</p>
        <div class="buttons">
            <a href="Cart.php">
                <button class="button cancel">ยกเลิก</button>
            </a>
            <button type="submit" class="button confirm" onclick="handleConfirm()">ตกลง</button>
        </div>
    </div>
    <script>
        function handleConfirm() {
            $.ajax({
                url: './service/place_order.php',
                method: 'get',
                success: function (response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            Swal.fire('Success!', result.message, 'success').then(() => {
                                window.location.href = 'Product.php';
                            });
                        } else {
                            Swal.fire('Error!', result.message, 'error');
                        }
                    } catch (e) {
                        Swal.fire('Error!', 'Invalid server response.', 'error');
                        console.error('Parsing error:', e, response);
                    }
                },
                error: function () {
                    Swal.fire('Error!', result.message, 'error');
                }
            });
        }
    </script>
</body>

</html>