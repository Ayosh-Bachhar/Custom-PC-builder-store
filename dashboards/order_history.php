<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../index.php?role=login");
    exit();
}

$userId = $_SESSION["User_ID"];

$sql = "SELECT orders.Order_ID,
               orders.Order_Date,
               orders.Total_Price,
               orders.Order_Status,
               orders.Payment_Status,
               orders.DeliveryCharge,
               delivery_option.Option_Name
        FROM orders
        INNER JOIN delivery_option
        ON orders.Delivery_Option_ID = delivery_option.Delivery_Option_ID
        WHERE orders.Customer_User_ID = '$userId'
        ORDER BY orders.Order_Date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order History</title>
</head>
<body>

    <h1>Order History</h1>

    <?php
    if (mysqli_num_rows($result) > 0) {
    ?>

        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Total Price</th>
                <th>Delivery Type</th>
                <th>Delivery Charge</th>
                <th>Order Status</th>
                <th>Payment Status</th>
                <th>Action</th>
            </tr>

            <?php
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <tr>
                    <td><?php echo $row["Order_ID"]; ?></td>
                    <td><?php echo $row["Order_Date"]; ?></td>
                    <td><?php echo $row["Total_Price"]; ?> Tk</td>
                    <td><?php echo $row["Option_Name"]; ?></td>
                    <td><?php echo $row["DeliveryCharge"]; ?> Tk</td>
                    <td><?php echo $row["Order_Status"]; ?></td>
                    <td><?php echo $row["Payment_Status"]; ?></td>
                    <td>
                        <a href="order_details.php?order_id=<?php echo $row["Order_ID"]; ?>">
                            View Details
                        </a>
                    </td>
                </tr>
            <?php
            }
            ?>

        </table>

    <?php
    } else {
        echo "<p>No orders found.</p>";
    }
    ?>

    <br>

    <a href="customer_dashboard.php">Back to Dashboard</a>

</body>
</html>