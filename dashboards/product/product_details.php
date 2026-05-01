<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

if (!isset($_GET["product_id"])) {
    header("Location: ../customer/customer_home.php");
    exit();
}

$productId = $_GET["product_id"];

/* Get product data */
$productSql = "SELECT * FROM product WHERE Product_ID = '$productId'";
$productResult = mysqli_query($conn, $productSql);

if (mysqli_num_rows($productResult) == 0) {
    echo "Product not found.";
    exit();
}

$product = mysqli_fetch_assoc($productResult);

/* Get product specs */
$specSql = "SELECT * FROM product_spec WHERE Product_ID = '$productId'";
$specResult = mysqli_query($conn, $specSql);

/* Choose universal category image */
$categoryImage = "default.png";

if ($product["Category_Name"] == "CPU") {
    $categoryImage = "cpu.png";
} elseif ($product["Category_Name"] == "CPU Cooler") {
    $categoryImage = "cpu_cooler.png";
} elseif ($product["Category_Name"] == "Motherboard") {
    $categoryImage = "motherboard.png";
} elseif ($product["Category_Name"] == "RAM") {
    $categoryImage = "ram.png";
} elseif ($product["Category_Name"] == "Storage") {
    $categoryImage = "storage.png";
} elseif ($product["Category_Name"] == "Graphics Card") {
    $categoryImage = "graphics_card.png";
} elseif ($product["Category_Name"] == "Power Supply") {
    $categoryImage = "power_supply.png";
} elseif ($product["Category_Name"] == "Casing") {
    $categoryImage = "casing.png";
} elseif ($product["Category_Name"] == "Monitor") {
    $categoryImage = "monitor.png";
} elseif ($product["Category_Name"] == "Casing Fan") {
    $categoryImage = "casing_fan.png";
} elseif ($product["Category_Name"] == "Keyboard") {
    $categoryImage = "keyboard.png";
} elseif ($product["Category_Name"] == "Mouse") {
    $categoryImage = "mouse.png";
} elseif ($product["Category_Name"] == "Speaker & Home Theater") {
    $categoryImage = "speaker.png";
} elseif ($product["Category_Name"] == "Headphone") {
    $categoryImage = "headphone.png";
} elseif ($product["Category_Name"] == "WiFi Adapter/LAN Card") {
    $categoryImage = "wifi.png";
} elseif ($product["Category_Name"] == "Anti Virus") {
    $categoryImage = "anti_virus.png";
} elseif ($product["Category_Name"] == "UPS") {
    $categoryImage = "ups.png";
}

/* Get average rating */
$ratingSql = "SELECT AVG(Rating) AS AverageRating, COUNT(*) AS TotalReviews
              FROM review
              WHERE Product_ID = '$productId'
              AND Review_Status = 'Approved'";
$ratingResult = mysqli_query($conn, $ratingSql);
$ratingData = mysqli_fetch_assoc($ratingResult);

/* Get approved reviews */
$reviewSql = "SELECT review.*, users.Name
              FROM review
              INNER JOIN users
              ON review.Customer_User_ID = users.User_ID
              WHERE review.Product_ID = '$productId'
              AND review.Review_Status = 'Approved'
              ORDER BY review.ReviewDate DESC";
$reviewResult = mysqli_query($conn, $reviewSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Details</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Product Details</h1>

    <h2><?php echo $product["Name"]; ?></h2>

    <h2>Category Image</h2>

    <img src="../../category_images/<?php echo $categoryImage; ?>" width="180" height="180" alt="Category Image">

    <hr>

    <p><strong>Brand:</strong> <?php echo $product["Brand"]; ?></p>
    <p><strong>Model:</strong> <?php echo $product["Model"]; ?></p>
    <p><strong>Price:</strong> <?php echo $product["Price"]; ?> Tk</p>
    <p><strong>Stock:</strong> <?php echo $product["Stock_Qty"]; ?></p>
    <p><strong>Warranty:</strong> <?php echo $product["Warranty"]; ?></p>
    <p><strong>Category:</strong> <?php echo $product["Category_Name"]; ?></p>
    <p><strong>Watt Value:</strong> <?php echo $product["Watt_Value"]; ?> W</p>
    <p><strong>Description:</strong> <?php echo $product["Description"]; ?></p>

    <form method="POST" action="../cart/add_to_cart.php">
        <input type="hidden" name="product_id" value="<?php echo $product["Product_ID"]; ?>">

        <label>Quantity:</label>
        <input type="number" name="quantity" value="1" min="1" required>

        <button type="submit">Add to Cart</button>
    </form>

    <br>

    <a href="../cart/view_cart.php">View Cart</a>

    <hr>

    <h2>Specifications</h2>

    <?php
    if (mysqli_num_rows($specResult) > 0) {
    ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Spec Name</th>
                <th>Spec Value</th>
            </tr>

            <?php
            while ($spec = mysqli_fetch_assoc($specResult)) {
            ?>
                <tr>
                    <td><?php echo $spec["Specs"]; ?></td>
                    <td><?php echo $spec["Spec_Value"]; ?></td>
                </tr>
            <?php
            }
            ?>
        </table>
    <?php
    } else {
        echo "<p>No specifications added yet.</p>";
    }
    ?>

    <hr>

    <h2>Rating</h2>

    <?php
    if ($ratingData["TotalReviews"] > 0) {
        echo "<p>Average Rating: " . round($ratingData["AverageRating"], 1) . " / 5</p>";
        echo "<p>Total Reviews: " . $ratingData["TotalReviews"] . "</p>";
    } else {
        echo "<p>No rating yet.</p>";
    }
    ?>

    <hr>

    <h2>Add Your Review</h2>

    <form method="POST" action="../review/add_review.php">
        <input type="hidden" name="product_id" value="<?php echo $product["Product_ID"]; ?>">

        <label>Rating:</label><br>
        <select name="rating" required>
            <option value="">Select Rating</option>
            <option value="1">1 - Very Bad</option>
            <option value="2">2 - Bad</option>
            <option value="3">3 - Average</option>
            <option value="4">4 - Good</option>
            <option value="5">5 - Excellent</option>
        </select>

        <br><br>

        <label>Comment:</label><br>
        <textarea name="comment" rows="4" cols="40" required></textarea>

        <br><br>

        <button type="submit">Submit Review</button>
    </form>

    <hr>

    <h2>Reviews</h2>

    <?php
    if (mysqli_num_rows($reviewResult) > 0) {
        while ($review = mysqli_fetch_assoc($reviewResult)) {
    ?>
            <p><strong><?php echo $review["Name"]; ?></strong></p>
            <p>Rating: <?php echo $review["Rating"]; ?> / 5</p>
            <p><?php echo $review["Comment"]; ?></p>
            <hr>
    <?php
        }
    } else {
        echo "<p>No reviews yet.</p>";
    }
    ?>

    <br>

    <a href="products.php?category=<?php echo urlencode($product["Category_Name"]); ?>">Back to Products</a>

</body>
</html>