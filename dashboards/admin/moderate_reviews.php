<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || ($_SESSION["Role"] != "Staff" && $_SESSION["Role"] != "Owner")) {
    header("Location: ../../index.php?role=login");
    exit();
}

$message = "";
$moderatorUserId = $_SESSION["User_ID"];

/* Approve review */
if (isset($_POST["approve_review"])) {
    $reviewId = $_POST["review_id"];

    $updateSql = "UPDATE review
                  SET Review_Status = 'Approved',
                      Moderated_By_User_ID = '$moderatorUserId'
                  WHERE ReviewID = '$reviewId'";

    if (mysqli_query($conn, $updateSql)) {
        $message = "Review approved successfully.";
    } else {
        $message = "Approve failed: " . mysqli_error($conn);
    }
}

/* Disapprove review */
if (isset($_POST["disapprove_review"])) {
    $reviewId = $_POST["review_id"];

    $updateSql = "UPDATE review
                  SET Review_Status = 'Rejected',
                      Moderated_By_User_ID = '$moderatorUserId'
                  WHERE ReviewID = '$reviewId'";

    if (mysqli_query($conn, $updateSql)) {
        $message = "Review disapproved successfully.";
    } else {
        $message = "Disapprove failed: " . mysqli_error($conn);
    }
}

/* Get all reviews */
$reviewSql = "SELECT review.*,
                     users.Name AS CustomerName,
                     product.Name AS ProductName
              FROM review
              INNER JOIN users
              ON review.Customer_User_ID = users.User_ID
              INNER JOIN product
              ON review.Product_ID = product.Product_ID
              ORDER BY review.ReviewID DESC";

$reviewResult = mysqli_query($conn, $reviewSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Moderate Reviews</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Moderate Reviews</h1>

    <?php
    if ($message != "") {
        echo "<p><strong>$message</strong></p>";
    }
    ?>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Review ID</th>
            <th>Product</th>
            <th>Customer</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Review Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php
        while ($review = mysqli_fetch_assoc($reviewResult)) {
        ?>
            <tr>
                <td><?php echo $review["ReviewID"]; ?></td>
                <td><?php echo $review["ProductName"]; ?></td>
                <td><?php echo $review["CustomerName"]; ?></td>
                <td><?php echo $review["Rating"]; ?> / 5</td>
                <td><?php echo $review["Comment"]; ?></td>
                <td><?php echo $review["ReviewDate"]; ?></td>
                <td><?php echo $review["Review_Status"]; ?></td>

                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="review_id" value="<?php echo $review["ReviewID"]; ?>">

                        <button type="submit" name="approve_review">Approve</button>
                        <button type="submit" name="disapprove_review">Reject</button>
                    </form>
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
    <a href="owner_dashboard.php">Back to Owner Dashboard</a>
<?php
} else {
?>
    <a href="../staff/staff_dashboard.php">Back to Staff Dashboard</a>
<?php
}
?>

</body>
</html>