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

if (isset($_GET["search"])) {
    $search = mysqli_real_escape_string($conn, $_GET["search"]);
}

if (isset($_GET["category"])) {
    $category = mysqli_real_escape_string($conn, $_GET["category"]);
}

if (isset($_GET["brand"])) {
    $brand = mysqli_real_escape_string($conn, $_GET["brand"]);
}

if (isset($_GET["min_price"])) {
    $minPrice = $_GET["min_price"];
}

if (isset($_GET["max_price"])) {
    $maxPrice = $_GET["max_price"];
}

/* Choose universal category image */
$categoryImage = "default.png";

if ($category == "CPU") {
    $categoryImage = "cpu.png";
} elseif ($category == "CPU Cooler") {
    $categoryImage = "cpu_cooler.png";
} elseif ($category == "Motherboard") {
    $categoryImage = "motherboard.png";
} elseif ($category == "RAM") {
    $categoryImage = "ram.png";
} elseif ($category == "Storage") {
    $categoryImage = "storage.png";
} elseif ($category == "Graphics Card") {
    $categoryImage = "graphics_card.png";
} elseif ($category == "Power Supply") {
    $categoryImage = "power_supply.png";
} elseif ($category == "Casing") {
    $categoryImage = "casing.png";
} elseif ($category == "Monitor") {
    $categoryImage = "monitor.png";
} elseif ($category == "Casing Fan") {
    $categoryImage = "casing_fan.png";
} elseif ($category == "Keyboard") {
    $categoryImage = "keyboard.png";
} elseif ($category == "Mouse") {
    $categoryImage = "mouse.png";
} elseif ($category == "Speaker & Home Theater") {
    $categoryImage = "speaker.png";
} elseif ($category == "Headphone") {
    $categoryImage = "headphone.png";
} elseif ($category == "WiFi Adapter/LAN Card") {
    $categoryImage = "wifi.png";
} elseif ($category == "Anti Virus") {
    $categoryImage = "anti_virus.png";
} elseif ($category == "UPS") {
    $categoryImage = "ups.png";
}

/* Product query */
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

if ($minPrice != "") {
    $productSql = $productSql . " AND Price >= '$minPrice'";
}

if ($maxPrice != "") {
    $productSql = $productSql . " AND Price <= '$maxPrice'";
}

$productSql = $productSql . " ORDER BY Name ASC";
$productResult = mysqli_query($conn, $productSql);

/* Brand filter query */
$brandSql = "SELECT DISTINCT Brand FROM product WHERE 1";

if ($category != "") {
    $brandSql = $brandSql . " AND Category_Name = '$category'";
}

$brandSql = $brandSql . " ORDER BY Brand ASC";
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

    <?php
    if ($category != "") {
    ?>
        <h2><?php echo $category; ?></h2>

        <img src="../../category_images/<?php echo $categoryImage; ?>" width="140" height="140" alt="Category Image">

        <br><br>
    <?php
    }
    ?>

    <form method="GET" action="">
        <label>Search:</label><br>
        <input type="text" name="search" placeholder="Name, brand, model" value="<?php echo $search; ?>">

        <?php
        if ($category != "") {
        ?>
            <input type="hidden" name="category" value="<?php echo $category; ?>">
        <?php
        }
        ?>

        <br><br>

        <?php
        if ($category != "") {
            echo "<p><strong>Category:</strong> " . $category . "</p>";
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
        if (mysqli_num_rows($productResult) > 0) {
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
        } else {
        ?>
            <tr>
                <td colspan="9">No products found.</td>
            </tr>
        <?php
        }
        ?>
    </table>

    <br>

    <a href="../customer/customer_home.php">Back to Customer Home</a>

</body>
</html>