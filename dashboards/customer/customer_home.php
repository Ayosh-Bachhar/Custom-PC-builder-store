<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

/* Get all categories in required display sequence */
$categorySql = "SELECT * FROM category
                ORDER BY FIELD(Category_Name,
                    'CPU',
                    'CPU Cooler',
                    'Motherboard',
                    'RAM',
                    'Storage',
                    'Graphics Card',
                    'Power Supply',
                    'Casing',
                    'Monitor',
                    'Casing Fan',
                    'Keyboard',
                    'Mouse',
                    'Speaker & Home Theater',
                    'Headphone',
                    'WiFi Adapter/LAN Card',
                    'Anti Virus',
                    'UPS'
                )";

$categoryResult = mysqli_query($conn, $categorySql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Home</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body class="customer-home-page">

    <h1>Customer Home Page</h1>

    <p>Welcome, <?php echo $_SESSION["Name"]; ?></p>

    <section class="customer-search-section">
        <h2>Search Products</h2>

        <form method="GET" action="../product/products.php">
            <input type="text" name="search" placeholder="Search by name, brand, or model">
            <button type="submit">Search</button>
        </form>
    </section>

    <section class="customer-shortcut-section">
        <h2>Home Shortcuts</h2>

        <a class="shortcut-button" href="../pc_builder/pc_build.php">PC Build</a>
    </section>

    <section class="featured-category-section">
        <h2>Featured Category</h2>
        <p>Get Your Desired Product from Featured Category!</p>

        <div class="category-grid">
            <?php
            while ($category = mysqli_fetch_assoc($categoryResult)) {
            ?>
                <a class="category-card" href="../product/products.php?category=<?php echo urlencode($category["Category_Name"]); ?>">
                    <?php echo $category["Category_Name"]; ?>
                </a>
            <?php
            }
            ?>
        </div>
    </section>

    <a class="back-link" href="../customer/customer_dashboard.php">Back to Dashboard</a>

</body>
</html>