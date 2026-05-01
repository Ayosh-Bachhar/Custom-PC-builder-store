<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

$categorySql = "SELECT * FROM category ORDER BY Category_Name ASC";
$categoryResult = mysqli_query($conn, $categorySql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Home</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Customer Home Page</h1>

    <p>Welcome, <?php echo $_SESSION["Name"]; ?></p>

    <h2>Search Products</h2>

    <form method="GET" action="../product/products.php">
        <input type="text" name="search" placeholder="Search by name, brand, or model" required>
        <button type="submit">Search</button>
    </form>

    <hr>

    <h2>Home Shortcuts</h2>
    
    <a href="../pc_builder/pc_build.php">
        <button>PC Build</button>
    </a>

    <hr>

    <h2>Component Categories</h2>

    <?php
    while ($category = mysqli_fetch_assoc($categoryResult)) {
    ?>
        <a href="../product/products.php?category=<?php echo urlencode($category['Category_Name']); ?>">
            <button><?php echo $category["Category_Name"]; ?></button>
        </a>
        <br><br>
    <?php
    }
    ?>

    <br>

    <a href="customer_dashboard.php">Back to Dashboard</a>

</body>
</html>