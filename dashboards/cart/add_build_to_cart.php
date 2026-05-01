<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../pc_builder/pc_build.php");
    exit();
}

$customerUserId = $_SESSION["User_ID"];

if (!isset($_POST["product_ids"])) {
    header("Location: ../pc_builder/pc_build.php");
    exit();
}

$productIds = $_POST["product_ids"];

/* Check if customer already has a cart */
$cartSql = "SELECT * FROM cart WHERE Customer_User_ID = '$customerUserId'";
$cartResult = mysqli_query($conn, $cartSql);

if (mysqli_num_rows($cartResult) > 0) {
    $cart = mysqli_fetch_assoc($cartResult);
    $cartId = $cart["Cart_ID"];
} else {
    $createCart = "INSERT INTO cart (Customer_User_ID)
                   VALUES ('$customerUserId')";

    mysqli_query($conn, $createCart);
    $cartId = mysqli_insert_id($conn);
}

/* Find current max Cart_Item_ID */
$itemSql = "SELECT MAX(Cart_Item_ID) AS MaxItemId 
            FROM cart_item 
            WHERE Cart_ID = '$cartId'";

$itemResult = mysqli_query($conn, $itemSql);
$itemData = mysqli_fetch_assoc($itemResult);

if ($itemData["MaxItemId"] == NULL) {
    $cartItemId = 1;
} else {
    $cartItemId = $itemData["MaxItemId"] + 1;
}

/* Add every selected build product to cart */
foreach ($productIds as $productId) {
    $productSql = "SELECT * FROM product WHERE Product_ID = '$productId'";
    $productResult = mysqli_query($conn, $productSql);

    if (mysqli_num_rows($productResult) > 0) {
        $product = mysqli_fetch_assoc($productResult);
        $unitPrice = $product["Price"];

        $insertItem = "INSERT INTO cart_item
                       (Cart_ID, Cart_Item_ID, Quantity, Unit_Price, Product_ID)
                       VALUES
                       ('$cartId', '$cartItemId', 1, '$unitPrice', '$productId')";

        mysqli_query($conn, $insertItem);

        $cartItemId = $cartItemId + 1;
    }
}

header("Location: view_cart.php");
exit();
?>