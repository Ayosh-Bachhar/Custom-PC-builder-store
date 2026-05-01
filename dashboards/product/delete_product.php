<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || ($_SESSION["Role"] != "Staff" && $_SESSION["Role"] != "Owner")) {
    header("Location: ../../index.php?role=login");
    exit();
}

if (!isset($_GET["product_id"])) {
    header("Location: manage_products.php");
    exit();
}

$productId = $_GET["product_id"];

/* Then delete product specs */
$deleteSpecs = "DELETE FROM product_spec WHERE Product_ID = '$productId'";
mysqli_query($conn, $deleteSpecs);

/* Then delete reviews */
$deleteReviews = "DELETE FROM review WHERE Product_ID = '$productId'";
mysqli_query($conn, $deleteReviews);

/* Finally delete product */
$deleteProduct = "DELETE FROM product WHERE Product_ID = '$productId'";

if (mysqli_query($conn, $deleteProduct)) {
    header("Location: manage_products.php");
    exit();
} else {
    echo "Product delete failed: " . mysqli_error($conn);
}
?>