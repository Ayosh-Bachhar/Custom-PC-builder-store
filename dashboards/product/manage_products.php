<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || ($_SESSION["Role"] != "Staff" && $_SESSION["Role"] != "Owner")) {
    header("Location: ../../index.php?role=login");
    exit();
}

/* =========================
   FILTER VALUES
========================= */
$search = "";
$category = "";
$brand = "";
$stockStatus = "";
$minPrice = "";
$maxPrice = "";

if (isset($_GET["search"])) {
    $search = mysqli_real_escape_string($conn, trim($_GET["search"]));
}

if (isset($_GET["category"])) {
    $category = mysqli_real_escape_string($conn, trim($_GET["category"]));
}

if (isset($_GET["brand"])) {
    $brand = mysqli_real_escape_string($conn, trim($_GET["brand"]));
}

if (isset($_GET["stock_status"])) {
    $stockStatus = mysqli_real_escape_string($conn, trim($_GET["stock_status"]));
}

if (isset($_GET["min_price"])) {
    $minPrice = mysqli_real_escape_string($conn, trim($_GET["min_price"]));
}

if (isset($_GET["max_price"])) {
    $maxPrice = mysqli_real_escape_string($conn, trim($_GET["max_price"]));
}

/* =========================
   CATEGORY DROPDOWN DATA
========================= */
$categorySql = "SELECT DISTINCT Category_Name FROM product ORDER BY Category_Name ASC";
$categoryResult = mysqli_query($conn, $categorySql);

/* =========================
   BRAND DROPDOWN DATA
========================= */
$brandSql = "SELECT DISTINCT Brand FROM product WHERE 1";

if ($category != "") {
    $brandSql = $brandSql . " AND Category_Name = '$category'";
}

$brandSql = $brandSql . " ORDER BY Brand ASC";
$brandResult = mysqli_query($conn, $brandSql);

/* =========================
   PRODUCT FILTER QUERY
========================= */
$productSql = "SELECT * FROM product WHERE 1";

if ($search != "") {
    $productSql = $productSql . " AND (Name LIKE '%$search%' OR Brand LIKE '%$search%' OR Model LIKE '%$search%')";
}

if ($category != "") {
    $productSql = $productSql . " AND Category_Name = '$category'";
}

if ($brand != "") {
    $productSql = $productSql . " AND Brand = '$brand'";
}

if ($stockStatus == "In Stock") {
    $productSql = $productSql . " AND Stock_Qty > 0";
} elseif ($stockStatus == "Out of Stock") {
    $productSql = $productSql . " AND Stock_Qty <= 0";
}

if ($minPrice != "") {
    $productSql = $productSql . " AND Price >= '$minPrice'";
}

if ($maxPrice != "") {
    $productSql = $productSql . " AND Price <= '$maxPrice'";
}

$productSql = $productSql . " ORDER BY Product_ID ASC";
$productResult = mysqli_query($conn, $productSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Manage Products</h1>

    <a href="add_product.php">Add New Product</a>

    <hr>

    <h2>Filter Products</h2>

    <form method="GET" action="">
        <label>Search:</label><br>
        <input type="text" name="search" placeholder="Name, brand, model" value="<?php echo $search; ?>">

        <br><br>

        <label>Category:</label><br>
        <select name="category">
            <option value="">All Categories</option>

            <?php
            while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
            ?>
                <option value="<?php echo $categoryRow["Category_Name"]; ?>"
                    <?php
                    if ($category == $categoryRow["Category_Name"]) {
                        echo "selected";
                    }
                    ?>
                >
                    <?php echo $categoryRow["Category_Name"]; ?>
                </option>
            <?php
            }
            ?>
        </select>

        <br><br>

        <label>Brand:</label><br>
        <select name="brand">
            <option value="">All Brands</option>

            <?php
            while ($brandRow = mysqli_fetch_assoc($brandResult)) {
            ?>
                <option value="<?php echo $brandRow["Brand"]; ?>"
                    <?php
                    if ($brand == $brandRow["Brand"]) {
                        echo "selected";
                    }
                    ?>
                >
                    <?php echo $brandRow["Brand"]; ?>
                </option>
            <?php
            }
            ?>
        </select>

        <br><br>

        <label>Stock Status:</label><br>
        <select name="stock_status">
            <option value="">All Stock</option>

            <option value="In Stock"
                <?php
                if ($stockStatus == "In Stock") {
                    echo "selected";
                }
                ?>
            >
                In Stock
            </option>

            <option value="Out of Stock"
                <?php
                if ($stockStatus == "Out of Stock") {
                    echo "selected";
                }
                ?>
            >
                Out of Stock
            </option>
        </select>

        <br><br>

        <label>Minimum Price:</label><br>
        <input type="number" name="min_price" value="<?php echo $minPrice; ?>">

        <br><br>

        <label>Maximum Price:</label><br>
        <input type="number" name="max_price" value="<?php echo $maxPrice; ?>">

        <br><br>

        <button type="submit">Apply Filter</button>

        <a href="manage_products.php">
            <button type="button">Clear Filter</button>
        </a>
    </form>

    <hr>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Product ID</th>
            <th>Name</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Stock Status</th>
            <th>Warranty</th>
            <th>Watt</th>
            <th>Category</th>
            <th>Action</th>
        </tr>

        <?php
        if (mysqli_num_rows($productResult) > 0) {
            while ($product = mysqli_fetch_assoc($productResult)) {
                $currentStockStatus = "Out of Stock";

                if ($product["Stock_Qty"] > 0) {
                    $currentStockStatus = "In Stock";
                }
        ?>
                <tr>
                    <td><?php echo $product["Product_ID"]; ?></td>
                    <td><?php echo $product["Name"]; ?></td>
                    <td><?php echo $product["Brand"]; ?></td>
                    <td><?php echo $product["Model"]; ?></td>
                    <td><?php echo $product["Price"]; ?> Tk</td>
                    <td><?php echo $product["Stock_Qty"]; ?></td>
                    <td><?php echo $currentStockStatus; ?></td>
                    <td><?php echo $product["Warranty"]; ?></td>
                    <td><?php echo $product["Watt_Value"]; ?> W</td>
                    <td><?php echo $product["Category_Name"]; ?></td>
                    <td>
                        <a href="edit_product.php?product_id=<?php echo $product["Product_ID"]; ?>">Edit</a>
                        |
                        <a href="delete_product.php?product_id=<?php echo $product["Product_ID"]; ?>"
                           onclick="return confirm('Are you sure you want to delete this product?');">
                            Delete
                        </a>
                    </td>
                </tr>
        <?php
            }
        } else {
        ?>
            <tr>
                <td colspan="11">No products found.</td>
            </tr>
        <?php
        }
        ?>
    </table>

    <br>

    <?php
    if ($_SESSION["Role"] == "Owner") {
    ?>
        <a class="top-back-link" href="../admin/owner_dashboard.php">Back to Owner Dashboard</a>
    <?php
    } else {
    ?>
        <a class="top-back-link" href="../staff/staff_dashboard.php">Back to Staff Dashboard</a>
    <?php
    }
    ?>

</body>
</html>