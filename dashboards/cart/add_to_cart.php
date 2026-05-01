<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerUserId = $_SESSION["User_ID"];
    $productId = $_POST["product_id"];
    $quantity = $_POST["quantity"];

    /* Get product price */
    $productSql = "SELECT * FROM product WHERE Product_ID = '$productId'";
    $productResult = mysqli_query($conn, $productSql);

    if (mysqli_num_rows($productResult) == 0) {
        echo "Product not found.";
        exit();
    }

    $product = mysqli_fetch_assoc($productResult);
    $unitPrice = $product["Price"];

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

    /* Find next Cart_Item_ID for this cart */
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

    /* Insert item into cart */
    $insertItem = "INSERT INTO cart_item
                   (Cart_ID, Cart_Item_ID, Quantity, Unit_Price, Product_ID)
                   VALUES
                   ('$cartId', '$cartItemId', '$quantity', '$unitPrice', '$productId')";

    if (mysqli_query($conn, $insertItem)) {
        header("Location: ../product/product_details.php?product_id=$productId");
        exit();
    } else {
        echo "Add to cart failed: " . mysqli_error($conn);
    }
}
?>