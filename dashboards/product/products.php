<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

$search = "";
$category = "";
$brand = "";
$minPrice = "";
$maxPrice = "";

$whereParts = array();

if (isset($_GET["search"]) && $_GET["search"] != "") {
    $search = mysqli_real_escape_string($conn, $_GET["search"]);

    $whereParts[] = "(Name LIKE '%$search%' 
                     OR Brand LIKE '%$search%' 
                     OR Model LIKE '%$search%')";
}

if (isset($_GET["category"]) && $_GET["category"] != "") {
    $category = mysqli_real_escape_string($conn, $_GET["category"]);
    $whereParts[] = "Category_Name = '$category'";
}

if (isset($_GET["brand"]) && $_GET["brand"] != "") {
    $brand = mysqli_real_escape_string($conn, $_GET["brand"]);
    $whereParts[] = "Brand = '$brand'";
}

if (isset($_GET["min_price"]) && $_GET["min_price"] != "") {
    $minPrice = mysqli_real_escape_string($conn, $_GET["min_price"]);
    $whereParts[] = "Price >= '$minPrice'";
}

if (isset($_GET["max_price"]) && $_GET["max_price"] != "") {
    $maxPrice = mysqli_real_escape_string($conn, $_GET["max_price"]);
    $whereParts[] = "Price <= '$maxPrice'";
}

$sql = "SELECT * FROM product";

if (count($whereParts) > 0) {
    $sql = $sql . " WHERE " . implode(" AND ", $whereParts);
}

$sql = $sql . " ORDER BY Name ASC";

$productResult = mysqli_query($conn, $sql);

/* Get brands for filter */
$brandSql = "SELECT DISTINCT Brand FROM product ORDER BY Brand ASC";
$brandResult = mysqli_query($conn, $brandSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Products</h1>

    <form method="GET" action="products.php">
        <label>Search:</label><br>
        <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Name, brand, model">
        <br><br>

        <?php
        if ($category != "") {
        ?>
            <input type="hidden" name="category" value="<?php echo $category; ?>">
            <p><strong>Category:</strong> <?php echo $category; ?></p>
        <?php
        }
        ?>

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

        <label>Minimum Price:</label><br>
        <input type="number" name="min_price" value="<?php echo $minPrice; ?>">

        <br><br>

        <label>Maximum Price:</label><br>
        <input type="number" name="max_price" value="<?php echo $maxPrice; ?>">

        <br><br>

        <button type="submit">Apply Filter</button>
    </form>

    <hr>

    <?php
    if (mysqli_num_rows($productResult) > 0) {
    ?>

        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Warranty</th>
                <th>Category</th>
                <th>Action</th>
            </tr>

            <?php
            while ($product = mysqli_fetch_assoc($productResult)) {
            ?>
                <tr>
                    <td><?php echo $product["Product_ID"]; ?></td>
                    <td><?php echo $product["Name"]; ?></td>
                    <td><?php echo $product["Brand"]; ?></td>
                    <td><?php echo $product["Model"]; ?></td>
                    <td><?php echo $product["Price"]; ?> Tk</td>
                    <td><?php echo $product["Stock_Qty"]; ?></td>
                    <td><?php echo $product["Warranty"]; ?></td>
                    <td><?php echo $product["Category_Name"]; ?></td>
                    <td>
                    <a href="product_details.php?product_id=<?php echo $product["Product_ID"]; ?>">
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
        echo "<p>No products found.</p>";
    }
    ?>

    <br>

    <a href="../customer/customer_home.php">Back to Customer Home</a>

</body>
</html>