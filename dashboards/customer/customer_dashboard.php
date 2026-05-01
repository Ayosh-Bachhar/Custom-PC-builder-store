<?php
session_start();

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Customer Dashboard</h1>

    <p>Welcome, <?php echo $_SESSION["Name"]; ?></p>

    <a href="customer_home.php">Browse Products</a>
    <br><br>

    <a href="../cart/view_cart.php">View Cart</a>
    <br><br>

    <a href="profile.php">Manage Profile</a>
    <br><br>

    <a href="../checkout_order/order_history.php">Order History</a>
    <br><br>

    <a href="../pc_builder/pc_build.php">PC Builder</a>
    <br><br>

    <a href="../../logout.php">Logout</a>

</body>
</html>