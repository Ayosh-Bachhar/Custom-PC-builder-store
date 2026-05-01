<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cartId = $_POST["cart_id"];
    $cartItemId = $_POST["cart_item_id"];

    if (isset($_POST["increase_quantity"])) {
        $updateSql = "UPDATE cart_item
                      SET Quantity = Quantity + 1
                      WHERE Cart_ID = '$cartId'
                      AND Cart_Item_ID = '$cartItemId'";

        mysqli_query($conn, $updateSql);
    }

    if (isset($_POST["decrease_quantity"])) {
        $getSql = "SELECT Quantity 
                   FROM cart_item
                   WHERE Cart_ID = '$cartId'
                   AND Cart_Item_ID = '$cartItemId'";

        $getResult = mysqli_query($conn, $getSql);
        $item = mysqli_fetch_assoc($getResult);

        if ($item["Quantity"] > 1) {
            $updateSql = "UPDATE cart_item
                          SET Quantity = Quantity - 1
                          WHERE Cart_ID = '$cartId'
                          AND Cart_Item_ID = '$cartItemId'";

            mysqli_query($conn, $updateSql);
        }
    }

    if (isset($_POST["remove_item"])) {
        $deleteSql = "DELETE FROM cart_item
                      WHERE Cart_ID = '$cartId'
                      AND Cart_Item_ID = '$cartItemId'";

        mysqli_query($conn, $deleteSql);
    }
}

header("Location: view_cart.php");
exit();
?>