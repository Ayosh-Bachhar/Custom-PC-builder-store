<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../index.php?role=login");
    exit();
}

$userId = $_SESSION["User_ID"];

if (!isset($_GET["order_id"])) {
    header("Location: order_history.php");
    exit();
}

$orderId = $_GET["order_id"];

/* Get order information */
$orderSql = "SELECT orders.Order_ID,
                    orders.Order_Date,
                    orders.Order_Status,
                    orders.Total_Price,
                    orders.Payment_Method,
                    orders.Payment_Status,
                    orders.DeliveryCharge,
                    orders.Phone,
                    orders.City,
                    orders.Area,
                    orders.Road_no,
                    orders.House_no,
                    users.Name,
                    delivery_option.Option_Name
             FROM orders
             INNER JOIN delivery_option
             ON orders.Delivery_Option_ID = delivery_option.Delivery_Option_ID
             INNER JOIN users
             ON orders.Customer_User_ID = users.User_ID
             WHERE orders.Order_ID = '$orderId'
             AND orders.Customer_User_ID = '$userId'";

$orderResult = mysqli_query($conn, $orderSql);

if (mysqli_num_rows($orderResult) == 0) {
    echo "Invoice not found or you are not allowed to view this invoice.";
    exit();
}

$order = mysqli_fetch_assoc($orderResult);

/* Get order items */
$itemSql = "SELECT order_item.Quantity,
                   order_item.Unit_Price,
                   order_item.LineTotal,
                   product.Name,
                   product.Brand,
                   product.Model
            FROM order_item
            INNER JOIN product
            ON order_item.Product_ID = product.Product_ID
            WHERE order_item.Order_ID = '$orderId'";

$itemResult = mysqli_query($conn, $itemSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
</head>
<body>

    <h1>Custom PC Builder Store</h1>
    <h2>Invoice</h2>

    <p><strong>Invoice for Order ID:</strong> <?php echo $order["Order_ID"]; ?></p>
    <p><strong>Order Date:</strong> <?php echo $order["Order_Date"]; ?></p>

    <hr>

    <h3>Customer Information</h3>

    <p><strong>Name:</strong> <?php echo $order["Name"]; ?></p>
    <p><strong>Phone:</strong> <?php echo $order["Phone"]; ?></p>
    <p>
        <strong>Address:</strong>
        <?php echo $order["House_no"]; ?>,
        Road <?php echo $order["Road_no"]; ?>,
        <?php echo $order["Area"]; ?>,
        <?php echo $order["City"]; ?>
    </p>

    <hr>

    <h3>Order Information</h3>

    <p><strong>Delivery Type:</strong> <?php echo $order["Option_Name"]; ?></p>
    <p><strong>Payment Method:</strong> <?php echo $order["Payment_Method"]; ?></p>
    <p><strong>Payment Status:</strong> <?php echo $order["Payment_Status"]; ?></p>
    <p><strong>Order Status:</strong> <?php echo $order["Order_Status"]; ?></p>

    <hr>

    <h3>Item List</h3>

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
        while ($item = mysqli_fetch_assoc($itemResult)) {
        ?>
            <tr>
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

    <h3>Payment Summary</h3>

    <p><strong>Product Total:</strong> <?php echo $order["Total_Price"] - $order["DeliveryCharge"]; ?> Tk</p>
    <p><strong>Delivery Charge:</strong> <?php echo $order["DeliveryCharge"]; ?> Tk</p>
    <p><strong>Grand Total:</strong> <?php echo $order["Total_Price"]; ?> Tk</p>

    <br>

    <br>

<button onclick="window.print()">Print invoice as PDF</button>

<br><br>

<a href="order_details.php?order_id=<?php echo $order["Order_ID"]; ?>">Back to Order Details</a>

</body>
</html>