<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || ($_SESSION["Role"] != "Staff" && $_SESSION["Role"] != "Owner")) {
    header("Location: ../../index.php?role=login");
    exit();
}

if (!isset($_GET["order_id"])) {
    header("Location: manage_orders.php");
    exit();
}

$orderId = $_GET["order_id"];

/* Delete order items first */
$deleteItems = "DELETE FROM order_item
                WHERE Order_ID = '$orderId'";

mysqli_query($conn, $deleteItems);

/* Delete order after deleting order items */
$deleteOrder = "DELETE FROM orders
                WHERE Order_ID = '$orderId'";

if (mysqli_query($conn, $deleteOrder)) {
    header("Location: manage_orders.php");
    exit();
} else {
    echo "Order delete failed: " . mysqli_error($conn);
}
?>