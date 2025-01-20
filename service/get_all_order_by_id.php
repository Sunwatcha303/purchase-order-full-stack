<?php
include '../repository/order.php';

$msresult = Order_Repo::GetAllOrderByIDCust($_POST['id']);
while ($row = mysqli_fetch_row($msresult)) {
    echo "<tr>
    <td>" . htmlspecialchars($row[0]) . "</td>
    <td>" . htmlspecialchars($row[1]) . "</td>
    <td>" . $row[2] . "</td>
    <td>";
    if ($row[2] === "Shipping") {
        echo "<input class='btn-success btn-sm' type=button value=Complete onClick=handleCompleteOrder($row[0])></input>";
    }
    if ($row[2] !== "Completed" && $row[2] !== "Cancelled") {
        echo "<input class='btn-danger btn-sm' type=button value=Cancel onClick=confirmCancel($row[0])></input>";
    } else {
        echo "No action";
    }
    echo "</td>
    <td>";
    if ($row[2] === "Completed") {
        echo "<a href=receipts/$row[0].pdf>view receipt</a>";
    } else {
        echo "receipt unavailable";
    }
    echo "</td>
    <td>";
    echo "<a href=PO/$row[0].pdf>view PO</a>";
    echo "</td></tr>";
}
?>