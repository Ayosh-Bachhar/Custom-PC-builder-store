<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["User_ID"])) {
    header("Location: ../index.php?role=login");
    exit();
}

$userId = $_SESSION["User_ID"];
$message = "";

/* Get current user data */
$getUser = "SELECT * FROM users WHERE User_ID = '$userId'";
$result = mysqli_query($conn, $getUser);
$user = mysqli_fetch_assoc($result);

/* Update profile */
if (isset($_POST["update_profile"])) {
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $phone = mysqli_real_escape_string($conn, $_POST["phone"]);
    $city = mysqli_real_escape_string($conn, $_POST["city"]);
    $area = mysqli_real_escape_string($conn, $_POST["area"]);
    $roadNo = mysqli_real_escape_string($conn, $_POST["road_no"]);
    $houseNo = mysqli_real_escape_string($conn, $_POST["house_no"]);

    $checkPhone = "SELECT * FROM users 
                   WHERE Phone = '$phone' 
                   AND User_ID != '$userId'";
    $phoneResult = mysqli_query($conn, $checkPhone);

    if (mysqli_num_rows($phoneResult) > 0) {
        $message = "This phone number is already used by another account.";
    } else {
        $updateUser = "UPDATE users
                       SET Name = '$name',
                           Phone = '$phone',
                           City = '$city',
                           Area = '$area',
                           Road_no = '$roadNo',
                           House_no = '$houseNo'
                       WHERE User_ID = '$userId'";

        if (mysqli_query($conn, $updateUser)) {
            $_SESSION["Name"] = $name;
            $message = "Profile updated successfully.";

            $getUser = "SELECT * FROM users WHERE User_ID = '$userId'";
            $result = mysqli_query($conn, $getUser);
            $user = mysqli_fetch_assoc($result);
        } else {
            $message = "Profile update failed: " . mysqli_error($conn);
        }
    }
}

/* Update password */
if (isset($_POST["update_password"])) {
    $oldPassword = $_POST["old_password"];
    $newPassword = $_POST["new_password"];

    if (password_verify($oldPassword, $user["Password"])) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $updatePassword = "UPDATE users
                           SET Password = '$hashedPassword'
                           WHERE User_ID = '$userId'";

        if (mysqli_query($conn, $updatePassword)) {
            $message = "Password updated successfully.";
        } else {
            $message = "Password update failed: " . mysqli_error($conn);
        }
    } else {
        $message = "Old password is incorrect.";
    }
}

/* Back dashboard link based on role */
$dashboardLink = "customer_dashboard.php";

if ($_SESSION["Role"] == "Staff") {
    $dashboardLink = "staff_dashboard.php";
} elseif ($_SESSION["Role"] == "Owner") {
    $dashboardLink = "owner_dashboard.php";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile Management</title>
</head>
<body>

    <h1>Profile Management</h1>

    <p>
        Logged in as: 
        <strong><?php echo $_SESSION["Role"]; ?></strong>
    </p>

    <p style="color: green;">
        <?php echo $message; ?>
    </p>

    <h2>Update Profile</h2>

    <form method="POST" action="">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?php echo $user['Name']; ?>" required><br><br>

        <label>Phone:</label><br>
        <input type="text" name="phone" value="<?php echo $user['Phone']; ?>" required><br><br>

        <label>City:</label><br>
        <input type="text" name="city" value="<?php echo $user['City']; ?>"><br><br>

        <label>Area:</label><br>
        <input type="text" name="area" value="<?php echo $user['Area']; ?>"><br><br>

        <label>Road No:</label><br>
        <input type="text" name="road_no" value="<?php echo $user['Road_no']; ?>"><br><br>

        <label>House No:</label><br>
        <input type="text" name="house_no" value="<?php echo $user['House_no']; ?>"><br><br>

        <button type="submit" name="update_profile">Update Profile</button>
    </form>

    <hr>

    <h2>Change Password</h2>

    <form method="POST" action="">
        <label>Old Password:</label><br>
        <input type="password" name="old_password" required><br><br>

        <label>New Password:</label><br>
        <input type="password" name="new_password" required minlength="8"><br><br>

        <button type="submit" name="update_password">Update Password</button>
    </form>

    <br>

    <a href="<?php echo $dashboardLink; ?>">Back to Dashboard</a>

</body>
</html>