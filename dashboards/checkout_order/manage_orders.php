<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || ($_SESSION["Role"] != "Staff" && $_SESSION["Role"] != "Owner")) {
    header("Location: ../../index.php?role=login");
    exit();
}

$message = "";

/* Update order status and payment status */
if (isset($_POST["update_status"])) {
    $orderId = $_POST["order_id"];
    $orderStatus = $_POST["order_status"];
    $paymentStatus = $_POST["payment_status"];
    $processedByUserId = $_SESSION["User_ID"];

    $updateSql = "UPDATE orders
                  SET Order_Status = '$orderStatus',
                      Payment_Status = '$paymentStatus',
                      Processed_By_User_ID = '$processedByUserId'
                  WHERE Order_ID = '$orderId'";

    if (mysqli_query($conn, $updateSql)) {
        $message = "Order updated successfully.";
    } else {
        $message = "Order update failed: " . mysqli_error($conn);
    }
}

/* Get all orders */
$orderSql = "SELECT orders.*,
                    users.Name AS CustomerName,
                    delivery_option.Option_Name
             FROM orders
             INNER JOIN users
             ON orders.Customer_User_ID = users.User_ID
             INNER JOIN delivery_option
             ON orders.Delivery_Option_ID = delivery_option.Delivery_Option_ID
             ORDER BY orders.Order_ID DESC";

$orderResult = mysqli_query($conn, $orderSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Manage Orders</h1>

    <?php
    if ($message != "") {
        echo "<p><strong>$message</strong></p>";
    }
    ?>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>Total Price</th>
            <th>Delivery</th>
            <th>Delivery Charge</th>
            <th>Payment Method</th>
            <th>Payment Status</th>
            <th>Order Status</th>
            <th>Update</th>
            <th>Details</th>
            <th>Delete</th>
        </tr>

        <?php
        while ($order = mysqli_fetch_assoc($orderResult)) {
        ?>
            <tr>
                <td><?php echo $order["Order_ID"]; ?></td>
                <td><?php echo $order["CustomerName"]; ?></td>
                <td><?php echo $order["Phone"]; ?></td>
                <td><?php echo $order["Total_Price"]; ?> Tk</td>
                <td><?php echo $order["Option_Name"]; ?></td>
                <td><?php echo $order["DeliveryCharge"]; ?> Tk</td>
                <td><?php echo $order["Payment_Method"]; ?></td>
                <td><?php echo $order["Payment_Status"]; ?></td>
                <td><?php echo $order["Order_Status"]; ?></td>

                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="order_id" value="<?php echo $order["Order_ID"]; ?>">

                        <label>Order:</label><br>
                        <select name="order_status">
                            <option value="Pending" <?php if ($order["Order_Status"] == "Pending") { echo "selected"; } ?>>
                                Pending
                            </option>

                            <option value="Processing" <?php if ($order["Order_Status"] == "Processing") { echo "selected"; } ?>>
                                Processing
                            </option>

                            <option value="Delivered" <?php if ($order["Order_Status"] == "Delivered") { echo "selected"; } ?>>
                                Delivered
                            </option>
                        </select>

                        <br><br>

                        <label>Payment:</label><br>
                        <select name="payment_status">
                            <option value="Unpaid" <?php if ($order["Payment_Status"] == "Unpaid") { echo "selected"; } ?>>
                                Unpaid
                            </option>

                            <option value="Paid" <?php if ($order["Payment_Status"] == "Paid") { echo "selected"; } ?>>
                                Paid
                            </option>
                        </select>

                        <br><br>

                        <button type="submit" name="update_status">Update</button>
                    </form>
                </td>

                <td>
                <a href="staff_order_details.php?order_id=<?php echo $order["Order_ID"]; ?>">View Details</a>
                </td>

                <td>
                    
                <a href="delete_order.php?order_id=<?php echo $order["Order_ID"]; ?>"
                onclick="return confirm('Are you sure you want to delete this order history?');">
                Delete
                </a>

                </td>
            </tr>
        <?php
        }
        ?>
    </table>

    <br>

<?php
if ($_SESSION["Role"] == "Owner") {
?>
    <a href="../admin/owner_dashboard.php">Back to Owner Dashboard</a>
<?php
} else {
?>
    <a href="../staff/staff_dashboard.php">Back to Staff Dashboard</a>
<?php
}
?>

</body>
</html>