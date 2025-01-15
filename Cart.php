<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .cart {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item span {
            flex: 1;
            text-align: center;
        }

        .cart-item .name {
            text-align: left;
            flex: 3;
        }

        .summary {
            text-align: right;
            font-size: 18px;
            margin-top: 20px;
            color: #333;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
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

        .button.back {
            background-color: #6c757d;
        }

        .button.back:hover {
            background-color: #5a6268;
        }

        .button.order {
            background-color: #28a745;
        }

        .button.order:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Your Cart</h2>
        <div class="cart">
            <?php
            $totalPrice = 0; // สำหรับเก็บราคารวมทั้งหมด

            if (!empty($_SESSION["cart"])) {
                echo "<div class='cart-item' style='font-weight: bold;'>
                        <span class='name'>สินค้า</span>
                        <span>ราคาต่อชิ้น</span>
                        <span>จำนวน</span>
                        <span>ราคารวม</span>
                      </div>";

                foreach ($_SESSION["cart"] as $item) {
                    $itemTotal = $item["price"] * $item["qty"]; // คำนวณราคารวมต่อสินค้า
                    $totalPrice += $itemTotal; // เพิ่มเข้าในราคารวมทั้งหมด

                    echo "<div class='cart-item'>";
                    echo "<span class='name'>" . htmlspecialchars($item["name"]) . "</span>";
                    echo "<span>$" . number_format($item["price"], 2) . "</span>";
                    echo "<span>" . htmlspecialchars($item["qty"]) . "</span>";
                    echo "<span>$" . number_format($itemTotal, 2) . "</span>";
                    echo "</div>";
                }

                echo "<div class='summary'><strong>ราคารวมทั้งหมด:</strong> $" . number_format($totalPrice, 2) . "</div>";
            } else {
                echo "<p style='text-align: center;'>Your cart is empty.</p>";
            }
            ?>
        </div>

        <div class="buttons">
            <a href="Order.php">
                <button class="button back">กลับ</button>
            </a>
            <form action="ConfirmOrder.php" method="post" style="display: inline;">
                <?php 
                if(!($totalPrice == 0)) {
                    echo "<button type='submit' class='button order'>สั่งสินค้า</button>";

                }

                ?>
            </form>
        </div>
    </div>
</body>

</html>