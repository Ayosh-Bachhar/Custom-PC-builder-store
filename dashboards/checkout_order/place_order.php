<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: checkout.php");
    exit();
}

$customerUserId = $_SESSION["User_ID"];

$phone = mysqli_real_escape_string($conn, $_POST["phone"]);
$city = mysqli_real_escape_string($conn, $_POST["city"]);
$area = mysqli_real_escape_string($conn, $_POST["area"]);
$roadNo = mysqli_real_escape_string($conn, $_POST["road_no"]);
$houseNo = mysqli_real_escape_string($conn, $_POST["house_no"]);
$notes = mysqli_real_escape_string($conn, $_POST["notes"]);
$deliveryOptionId = $_POST["delivery_option_id"];

/* Get delivery charge */
$deliverySql = "SELECT * FROM delivery_option 
                WHERE Delivery_Option_ID = '$deliveryOptionId'";

$deliveryResult = mysqli_query($conn, $deliverySql);

if (mysqli_num_rows($deliveryResult) == 0) {
    echo "Invalid delivery option.";
    exit();
}

$delivery = mysqli_fetch_assoc($deliveryResult);
$deliveryCharge = $delivery["Charge"];

/* Get customer cart */
$cartSql = "SELECT * FROM cart 
            WHERE Customer_User_ID = '$customerUserId'";

$cartResult = mysqli_query($conn, $cartSql);

if (mysqli_num_rows($cartResult) == 0) {
    echo "Cart not found.";
    exit();
}

$cart = mysqli_fetch_assoc($cartResult);
$cartId = $cart["Cart_ID"];

/* Get cart items */
$itemSql = "SELECT * FROM cart_item 
            WHERE Cart_ID = '$cartId'";

$itemResult = mysqli_query($conn, $itemSql);

if (mysqli_num_rows($itemResult) == 0) {
    echo "Your cart is empty.";
    exit();
}

/* Calculate cart total */
$cartTotal = 0;

while ($item = mysqli_fetch_assoc($itemResult)) {
    $lineTotal = $item["Quantity"] * $item["Unit_Price"];
    $cartTotal = $cartTotal + $lineTotal;
}

$totalPrice = $cartTotal + $deliveryCharge;

/* Insert into orders table */
$insertOrder = "INSERT INTO orders
(
    Order_Status,
    Total_Price,
    Notes,
    Payment_Method,
    Payment_Status,
    DeliveryCharge,
    Phone,
    City,
    Area,
    Road_no,
    House_no,
    Customer_User_ID,
    Processed_By_User_ID,
    Delivery_Option_ID,
    Cart_ID
)
VALUES
(
    'Pending',
    '$totalPrice',
    '$notes',
    'Cash on Delivery',
    'Unpaid',
    '$deliveryCharge',
    '$phone',
    '$city',
    '$area',
    '$roadNo',
    '$houseNo',
    '$customerUserId',
    NULL,
    '$deliveryOptionId',
    '$cartId'
)";

if (!mysqli_query($conn, $insertOrder)) {
    echo "Order insert failed: " . mysqli_error($conn);
    exit();
}

$orderId = mysqli_insert_id($conn);

/* Read cart items again for order_item insertion */
$itemSql2 = "SELECT * FROM cart_item 
             WHERE Cart_ID = '$cartId'";

$itemResult2 = mysqli_query($conn, $itemSql2);

$orderItemId = 1;

while ($item = mysqli_fetch_assoc($itemResult2)) {
    $quantity = $item["Quantity"];
    $unitPrice = $item["Unit_Price"];
    $productId = $item["Product_ID"];
    $lineTotal = $quantity * $unitPrice;

    $insertOrderItem = "INSERT INTO order_item
    (
        Order_ID,
        Order_Item_ID,
        Quantity,
        Unit_Price,
        LineTotal,
        Product_ID
    )
    VALUES
    (
        '$orderId',
        '$orderItemId',
        '$quantity',
        '$unitPrice',
        '$lineTotal',
        '$productId'
    )";

    mysqli_query($conn, $insertOrderItem);

    /* Decrease product stock after order */
    $decreaseStock = "UPDATE product
                  SET Stock_Qty = Stock_Qty - '$quantity'
                  WHERE Product_ID = '$productId'
                  AND Stock_Qty >= '$quantity'";
                  
    mysqli_query($conn, $decreaseStock);

    $orderItemId = $orderItemId + 1;
}

/* Clear cart after order placed */
$clearCart = "DELETE FROM cart_item 
              WHERE Cart_ID = '$cartId'";

mysqli_query($conn, $clearCart);

/* Send customer to invoice */
header("Location: invoice.php?order_id=$orderId");
exit();
?>