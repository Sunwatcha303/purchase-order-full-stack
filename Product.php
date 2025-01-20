<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .cart-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: rgb(0, 119, 255);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logout-button {
            position: fixed;
            top: 20px;
            left: 120px;
            background-color: rgb(0, 119, 255);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .cart-button:hover {
            background-color: #218838;
        }

        .cart-button span {
            margin-left: 10px;
            font-weight: bold;
        }

        .card {
            width: 200px;
            height: 250px;

            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            margin: 20px;
            text-align: center;
            font-family: arial;
            justify-content: space-between
        }

        .card h1 {
            max-height: 48px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            font-size: 20px;
            margin: 10px 0;
        }


        .price {
            color: grey;
            font-size: 22px;
        }

        .card button {
            border: none;
            outline: 0;
            padding: 12px;
            color: white;
            background-color: rgb(0, 119, 255);
            text-align: center;
            cursor: pointer;
            width: 100%;
            font-size: 18px;
        }

        .card button:hover {
            opacity: 0.7;
        }

        .container-product {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        .card-form {
            margin: 0px;
        }
    </style>
</head>

<body>
    <?php
    $link = mysqli_connect("localhost", "root", "", "demo", 3307);
    $query = "SELECT IDProduct, ProductName, PricePerUnit, ReserveQty FROM product";
    $result = mysqli_query($link, $query);

    session_start();
    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["product_id"])) {
        $product_id = $_POST["product_id"];
        $product_name = $_POST["product_name"];
        $product_price = $_POST["product_price"];
        $product_reserve_qty = $_POST["product_reserve_qty"];

        if (isset($_SESSION["cart"][$product_id])) {
            if ($_SESSION["cart"][$product_id]["qty"] < $product_reserve_qty) {
                $_SESSION["cart"][$product_id]["qty"] += 1;
            }
        } else {
            $_SESSION["cart"][$product_id] = [
                "id" => $product_id,
                "name" => $product_name,
                "price" => $product_price,
                "reserve_qty" => $product_reserve_qty,
                "qty" => 1
            ];
        }
    }

    ?>
    <div>

        <a href="Cart.php">
            <button class="cart-button">
                🛒 View Cart <span id="cart-count"><?php echo count($_SESSION["cart"]) ?></span>
            </button>
        </a>
        <a href="Profile.php">
            <button class="profile-button">Profile</span></button>
        </a>
        <a href="index.php">
            <button class="logout-button">Logout</span></button>
        </a>

        <h2 style="text-align:center">Product Card</h2>
    </div>
    <div class="container-product">
        <?php
        while ($row = mysqli_fetch_row($result)) {
            echo "
            <div class=card>
                <h1>$row[1]</h1>
                <p class=price>$row[2] ฿</p>
                <p class=qty>คลัง: $row[3]</p>
                <form class=card-form method=post>
                    <input type=hidden name=product_id value=$row[0]>
                    <input type=hidden name=product_name value=$row[1]>
                    <input type=hidden name=product_price value=$row[2]>
                    <input type=hidden name=product_reserve_qty value=$row[3]>";
                    if($row[3] > 0){
                        echo "<button type=submit>Add to Cart</button>";
                    }
            echo " 
                </form>
            </div>";
        }
        ?>
    </div>
</body>

</html>