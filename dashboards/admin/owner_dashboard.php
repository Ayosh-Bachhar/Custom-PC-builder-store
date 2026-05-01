<?php
session_start();

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Owner") {
    header("Location: ../../index.php?role=login");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Owner Dashboard</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Owner Dashboard</h1>

    <p>Welcome, <?php echo $_SESSION["Name"]; ?></p>

    <a href="../product/manage_products.php">Manage Products</a>
    <br><br>
    
    <a href="../checkout_order/manage_orders.php">Manage Orders</a>
    <br><br>
    
    <a href="manage_users.php">Manage Users</a>
    <br><br>
    
    <a href="moderate_reviews.php">Moderate Reviews</a>
    <br><br>

    <a href="manage_staff_ids.php">Manage Staff IDs</a>
    <br><br>

    <a href="../customer/profile.php">Manage Profile</a>
    <br><br>

    <a href="../../logout.php">Logout</a>

</body>
</html>