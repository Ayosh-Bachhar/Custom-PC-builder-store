<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || ($_SESSION["Role"] != "Staff" && $_SESSION["Role"] != "Owner")) {
    header("Location: ../../index.php?role=login");
    exit();
}

$message = "";

/* Get categories */
$categorySql = "SELECT * FROM category ORDER BY Category_Name ASC";
$categoryResult = mysqli_query($conn, $categorySql);

/* Add product */
if (isset($_POST["add_product"])) {
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $brand = mysqli_real_escape_string($conn, $_POST["brand"]);
    $model = mysqli_real_escape_string($conn, $_POST["model"]);
    $price = $_POST["price"];
    $stockQty = $_POST["stock_qty"];
    $warranty = mysqli_real_escape_string($conn, $_POST["warranty"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $wattValue = $_POST["watt_value"];
    $categoryName = mysqli_real_escape_string($conn, $_POST["category_name"]);

    $insertSql = "INSERT INTO product
                  (Name, Brand, Model, Price, Stock_Qty, Warranty, Description, Watt_Value, Category_Name)
                  VALUES
                  ('$name', '$brand', '$model', '$price', '$stockQty', '$warranty', '$description', '$wattValue', '$categoryName')";

    if (mysqli_query($conn, $insertSql)) {
        $message = "Product added successfully.";
    } else {
        $message = "Product add failed: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Add Product</h1>

    <?php
    if ($message != "") {
        echo "<p><strong>$message</strong></p>";
    }
    ?>

    <form method="POST" action="">

        <label>Name:</label><br>
        <input type="text" name="name" required>
        <br><br>

        <label>Brand:</label><br>
        <input type="text" name="brand" required>
        <br><br>

        <label>Model:</label><br>
        <input type="text" name="model" required>
        <br><br>

        <label>Price:</label><br>
        <input type="number" name="price" required>
        <br><br>

        <label>Stock Quantity:</label><br>
        <input type="number" name="stock_qty" required>
        <br><br>

        <label>Warranty:</label><br>
        <input type="text" name="warranty">
        <br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="4" cols="40"></textarea>
        <br><br>

        <label>Watt Value:</label><br>
        <input type="number" name="watt_value" value="0" required>
        <br><br>

        <label>Category:</label><br>
        <select name="category_name" required>
            <option value="">Select Category</option>

            <?php
            while ($category = mysqli_fetch_assoc($categoryResult)) {
            ?>
                <option value="<?php echo $category["Category_Name"]; ?>">
                    <?php echo $category["Category_Name"]; ?>
                </option>
            <?php
            }
            ?>
        </select>

        <br><br>

        <button type="submit" name="add_product">Add Product</button>

    </form>

    <br>

    <a href="manage_products.php">Back to Manage Products</a>

</body>
</html>