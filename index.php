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
</head>

<body>
    <center>
        <form action="authen.php" method="POST">
            <h1>รหัสลูกค้า
                <input type="text" name="id"><br>
                <input type="submit" value="ยืนยัน">
                <input type="reset" value="ยกเลิก">
            </h1>
        </form>
        <a href="Insert_Form.html"><input type="button" value="เพิ่มลูกค้า"></input></a>
        <div>
            <?php
            $host = 'localhost';
            $username = 'root';
            $password = '';
            $database = 'demo';
            $port = 3307;

            $conn = mysqli_connect($host, $username, $password, $database, $port);

            $msquery = "SELECT * FROM Customer";
            $msresult = mysqli_query($conn, $msquery);

            $cntM = 0;
            $cntF = 0;
            // echo "<ol>";
            echo "<center>";
            echo "<table border='1' style='border-collapse: collapse'>
                    <thead>
                        <tr>
                            <th>IDCust</th>
                            <th>Custname</th>
                            <th>Sex</th>
                        </tr>
                    </thead>
                    ";
            echo "<tbody>";
            while ($row = mysqli_fetch_row($msresult)) {
                echo "<tr>" .
                    "<td>$row[0]</td>" .
                    "<td>$row[1]</td>" .
                    "<td>$row[2]</td>" .
                    "</tr>\n";
                if ($row[2] == "M") {
                    $cntM++;
                } else if ($row[2] == "F") {
                    $cntF++;
                }
            }
            echo "</tbody></table>";
            echo "รวม " . $cntM + $cntF . " คน<br>";
            echo "ชาย " . $cntM . " คน<br>";
            echo "หญิง " . $cntF . " คน<br>";
            
            echo "</center>";
            mysqli_close($conn);
            ?>
        </div>
    </center>
</body>

</html>