<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || ($_SESSION["Role"] != "Staff" && $_SESSION["Role"] != "Owner")) {
    header("Location: ../../index.php?role=login");
    exit();
}

$message = "";

if (!isset($_GET["product_id"])) {
    header("Location: manage_products.php");
    exit();
}

$productId = $_GET["product_id"];

/* Get product */
$productSql = "SELECT * FROM product WHERE Product_ID = '$productId'";
$productResult = mysqli_query($conn, $productSql);

if (mysqli_num_rows($productResult) == 0) {
    echo "Product not found.";
    exit();
}

$product = mysqli_fetch_assoc($productResult);

/* Get categories */
$categorySql = "SELECT * FROM category ORDER BY Category_Name ASC";
$categoryResult = mysqli_query($conn, $categorySql);

/* Update product */
if (isset($_POST["update_product"])) {
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $brand = mysqli_real_escape_string($conn, $_POST["brand"]);
    $model = mysqli_real_escape_string($conn, $_POST["model"]);
    $price = $_POST["price"];
    $stockQty = $_POST["stock_qty"];
    $warranty = mysqli_real_escape_string($conn, $_POST["warranty"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $wattValue = $_POST["watt_value"];
    $categoryName = mysqli_real_escape_string($conn, $_POST["category_name"]);

    $updateSql = "UPDATE product
                  SET Name = '$name',
                      Brand = '$brand',
                      Model = '$model',
                      Price = '$price',
                      Stock_Qty = '$stockQty',
                      Warranty = '$warranty',
                      Description = '$description',
                      Watt_Value = '$wattValue',
                      Category_Name = '$categoryName'
                  WHERE Product_ID = '$productId'";

    if (mysqli_query($conn, $updateSql)) {
        header("Location: manage_products.php");
        exit();
    } else {
        $message = "Product update failed: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Edit Product</h1>

    <?php
    if ($message != "") {
        echo "<p><strong>$message</strong></p>";
    }
    ?>

    <form method="POST" action="">

        <label>Name:</label><br>
        <input type="text" name="name" value="<?php echo $product["Name"]; ?>" required>
        <br><br>

        <label>Brand:</label><br>
        <input type="text" name="brand" value="<?php echo $product["Brand"]; ?>" required>
        <br><br>

        <label>Model:</label><br>
        <input type="text" name="model" value="<?php echo $product["Model"]; ?>" required>
        <br><br>

        <label>Price:</label><br>
        <input type="number" name="price" value="<?php echo $product["Price"]; ?>" required>
        <br><br>

        <label>Stock Quantity:</label><br>
        <input type="number" name="stock_qty" value="<?php echo $product["Stock_Qty"]; ?>" required>
        <br><br>

        <label>Warranty:</label><br>
        <input type="text" name="warranty" value="<?php echo $product["Warranty"]; ?>">
        <br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="4" cols="40"><?php echo $product["Description"]; ?></textarea>
        <br><br>

        <label>Watt Value:</label><br>
        <input type="number" name="watt_value" value="<?php echo $product["Watt_Value"]; ?>" required>
        <br><br>

        <label>Category:</label><br>
        <select name="category_name" required>
            <?php
            while ($category = mysqli_fetch_assoc($categoryResult)) {
            ?>
                <option value="<?php echo $category["Category_Name"]; ?>"
                    <?php
                    if ($product["Category_Name"] == $category["Category_Name"]) {
                        echo "selected";
                    }
                    ?>
                >
                    <?php echo $category["Category_Name"]; ?>
                </option>
            <?php
            }
            ?>
        </select>

        <br><br>

        <button type="submit" name="update_product">Update Product</button>

    </form>

    <br>

    <a href="manage_products.php">Back to Manage Products</a>

</body>
</html>