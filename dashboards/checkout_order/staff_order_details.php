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

/* Get order info */
$orderSql = "SELECT orders.*,
                    users.Name AS CustomerName,
                    delivery_option.Option_Name
             FROM orders
             INNER JOIN users
             ON orders.Customer_User_ID = users.User_ID
             INNER JOIN delivery_option
             ON orders.Delivery_Option_ID = delivery_option.Delivery_Option_ID
             WHERE orders.Order_ID = '$orderId'";

$orderResult = mysqli_query($conn, $orderSql);

if (mysqli_num_rows($orderResult) == 0) {
    echo "Order not found.";
    exit();
}

$order = mysqli_fetch_assoc($orderResult);

/* Get order items */
$itemSql = "SELECT order_item.*,
                   product.Name,
                   product.Brand,
                   product.Model
            FROM order_item
            INNER JOIN product
            ON order_item.Product_ID = product.Product_ID
            WHERE order_item.Order_ID = '$orderId'
            ORDER BY order_item.Order_Item_ID ASC";

$itemResult = mysqli_query($conn, $itemSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Order Details</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Order Details</h1>

    <h2>Order Information</h2>

    <p><strong>Order ID:</strong> <?php echo $order["Order_ID"]; ?></p>
    <p><strong>Customer:</strong> <?php echo $order["CustomerName"]; ?></p>
    <p><strong>Phone:</strong> <?php echo $order["Phone"]; ?></p>
    <p><strong>Address:</strong>
        <?php echo $order["House_no"]; ?>,
        Road <?php echo $order["Road_no"]; ?>,
        <?php echo $order["Area"]; ?>,
        <?php echo $order["City"]; ?>
    </p>

    <p><strong>Delivery Type:</strong> <?php echo $order["Option_Name"]; ?></p>
    <p><strong>Delivery Charge:</strong> <?php echo $order["DeliveryCharge"]; ?> Tk</p>
    <p><strong>Payment Method:</strong> <?php echo $order["Payment_Method"]; ?></p>
    <p><strong>Payment Status:</strong> <?php echo $order["Payment_Status"]; ?></p>
    <p><strong>Order Status:</strong> <?php echo $order["Order_Status"]; ?></p>
    <p><strong>Notes:</strong> <?php echo $order["Notes"]; ?></p>

    <hr>

    <h2>Ordered Items</h2>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Item ID</th>
            <th>Product</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Line Total</th>
        </tr>

        <?php
        while ($item = mysqli_fetch_assoc($itemResult)) {
        ?>
            <tr>
                <td><?php echo $item["Order_Item_ID"]; ?></td>
                <td><?php echo $item["Name"]; ?></td>
                <td><?php echo $item["Brand"]; ?></td>
                <td><?php echo $item["Model"]; ?></td>
                <td><?php echo $item["Quantity"]; ?></td>
                <td><?php echo $item["Unit_Price"]; ?> Tk</td>
                <td><?php echo $item["LineTotal"]; ?> Tk</td>
            </tr>
        <?php
        }
        ?>
    </table>

    <br>

    <a href="manage_orders.php">Back to Manage Orders</a>

</body>
</html>