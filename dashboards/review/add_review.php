<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerUserId = $_SESSION["User_ID"];
    $productId = $_POST["product_id"];
    $rating = $_POST["rating"];
    $comment = mysqli_real_escape_string($conn, $_POST["comment"]);

    $insertReview = "INSERT INTO review
                     (Comment, Rating, Review_Status, Customer_User_ID, Product_ID, Moderated_By_User_ID)
                     VALUES
                     ('$comment', '$rating', 'Approved', '$customerUserId', '$productId', NULL)";

    if (mysqli_query($conn, $insertReview)) {
        header("Location: ../product/product_details.php?product_id=$productId");
        exit();
    } else {
        echo "Review insert failed: " . mysqli_error($conn);
    }
}
?>