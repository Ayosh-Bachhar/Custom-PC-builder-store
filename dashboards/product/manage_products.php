<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || ($_SESSION["Role"] != "Staff" && $_SESSION["Role"] != "Owner")) {
    header("Location: ../../index.php?role=login");
    exit();
}

$productSql = "SELECT * FROM product ORDER BY Product_ID ASC";
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
    <br><br>

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
        while ($product = mysqli_fetch_assoc($productResult)) {
        ?>
            <tr>
                <td><?php echo $product["Product_ID"]; ?></td>
                <td><?php echo $product["Name"]; ?></td>
                <td><?php echo $product["Brand"]; ?></td>
                <td><?php echo $product["Model"]; ?></td>
                <td><?php echo $product["Price"]; ?> Tk</td>
                <td><?php echo $product["Stock_Qty"]; ?></td>

                <td>
                    <?php
                    if ($product["Stock_Qty"] > 0) {
                        echo "In Stock";
                    } else {
                        echo "Out of Stock";
                    }
                    ?>
                </td>

                <td><?php echo $product["Warranty"]; ?></td>
                <td><?php echo $product["Watt_Value"]; ?> W</td>
                <td><?php echo $product["Category_Name"]; ?></td>

                <td>
                    <a href="edit_product.php?product_id=<?php echo $product["Product_ID"]; ?>">Edit</a>
                    |
                    <a href="delete_product.php?product_id=<?php echo $product["Product_ID"]; ?>">Delete</a>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>

    <br>

<?php
if ($_SESSION["Role"] == "Owner") {
?>
    <a href="../admin/owner_dashboard.php">Back to Owner Dashboard</a>
<?php
} else {
?>
    <a href="../staff/staff_dashboard.php">Back to Staff Dashboard</a>
<?php
}
?>

</body>
</html>