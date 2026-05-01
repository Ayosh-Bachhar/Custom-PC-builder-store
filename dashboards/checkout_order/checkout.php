<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

$customerUserId = $_SESSION["User_ID"];

/* Get customer info */
$userSql = "SELECT * FROM users WHERE User_ID = '$customerUserId'";
$userResult = mysqli_query($conn, $userSql);
$user = mysqli_fetch_assoc($userResult);

/* Get customer cart */
$cartSql = "SELECT * FROM cart WHERE Customer_User_ID = '$customerUserId'";
$cartResult = mysqli_query($conn, $cartSql);

if (mysqli_num_rows($cartResult) == 0) {
    echo "Your cart is empty.";
    exit();
}

$cart = mysqli_fetch_assoc($cartResult);
$cartId = $cart["Cart_ID"];

/* Get cart items */
$itemSql = "SELECT cart_item.Quantity,
                   cart_item.Unit_Price,
                   product.Name,
                   product.Brand,
                   product.Model
            FROM cart_item
            INNER JOIN product
            ON cart_item.Product_ID = product.Product_ID
            WHERE cart_item.Cart_ID = '$cartId'";

$itemResult = mysqli_query($conn, $itemSql);

if (mysqli_num_rows($itemResult) == 0) {
    echo "Your cart is empty.";
    exit();
}

/* Get delivery options */
$deliverySql = "SELECT * FROM delivery_option";
$deliveryResult = mysqli_query($conn, $deliverySql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Checkout</h1>

    <h2>Cart Summary</h2>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Product</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Line Total</th>
        </tr>

        <?php
        $cartTotal = 0;

        while ($item = mysqli_fetch_assoc($itemResult)) {
            $lineTotal = $item["Quantity"] * $item["Unit_Price"];
            $cartTotal = $cartTotal + $lineTotal;
        ?>
            <tr>
                <td><?php echo $item["Name"]; ?></td>
                <td><?php echo $item["Brand"]; ?></td>
                <td><?php echo $item["Model"]; ?></td>
                <td><?php echo $item["Quantity"]; ?></td>
                <td><?php echo $item["Unit_Price"]; ?> Tk</td>
                <td><?php echo $lineTotal; ?> Tk</td>
            </tr>
        <?php
        }
        ?>
    </table>

    <h3>Cart Total: <?php echo $cartTotal; ?> Tk</h3>

    <hr>

    <h2>Delivery Information</h2>

    <form method="POST" action="place_order.php">

        <label>Phone:</label><br>
        <input type="text" name="phone" value="<?php echo $user["Phone"]; ?>" required>
        <br><br>

        <label>City:</label><br>
        <input type="text" name="city" value="<?php echo $user["City"]; ?>" required>
        <br><br>

        <label>Area:</label><br>
        <input type="text" name="area" value="<?php echo $user["Area"]; ?>" required>
        <br><br>

        <label>Road No:</label><br>
        <input type="text" name="road_no" value="<?php echo $user["Road_no"]; ?>">
        <br><br>

        <label>House No:</label><br>
        <input type="text" name="house_no" value="<?php echo $user["House_no"]; ?>">
        <br><br>

        <label>Notes:</label><br>
        <textarea name="notes" rows="4" cols="40"></textarea>
        <br><br>

        <label>Delivery Option:</label><br>
        <select name="delivery_option_id" required>
            <option value="">Select Delivery Option</option>

            <?php
            while ($delivery = mysqli_fetch_assoc($deliveryResult)) {
            ?>
                <option value="<?php echo $delivery["Delivery_Option_ID"]; ?>">
                    <?php echo $delivery["Option_Name"]; ?> - <?php echo $delivery["Charge"]; ?> Tk
                </option>
            <?php
            }
            ?>
        </select>

        <br><br>

        <p><strong>Payment Method:</strong> Cash on Delivery</p>

        <button type="submit">Confirm Order</button>

    </form>

    <br>

    <a href="../cart/view_cart.php">Back to Cart</a>

</body>
</html>