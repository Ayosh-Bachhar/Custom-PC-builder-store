<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || ($_SESSION["Role"] != "Staff" && $_SESSION["Role"] != "Owner")) {
    header("Location: ../../index.php?role=login");
    exit();
}

$message = "";
$currentUserId = $_SESSION["User_ID"];
$currentRole = $_SESSION["Role"];

/* Block user */
if (isset($_POST["block_user"])) {
    $userId = $_POST["user_id"];

    if ($userId == $currentUserId) {
        $message = "You cannot block your own account.";
    } else {
        $updateSql = "UPDATE users
                      SET Account_Status = 'Blocked'
                      WHERE User_ID = '$userId'";

        if (mysqli_query($conn, $updateSql)) {
            $message = "User blocked successfully.";
        } else {
            $message = "Block failed: " . mysqli_error($conn);
        }
    }
}

/* Unblock user */
if (isset($_POST["unblock_user"])) {
    $userId = $_POST["user_id"];

    $updateSql = "UPDATE users
                  SET Account_Status = 'Active'
                  WHERE User_ID = '$userId'";

    if (mysqli_query($conn, $updateSql)) {
        $message = "User unblocked successfully.";
    } else {
        $message = "Unblock failed: " . mysqli_error($conn);
    }
}

/* Get users */
if ($currentRole == "Owner") {
    $userSql = "SELECT * FROM users ORDER BY User_ID ASC";
} else {
    $userSql = "SELECT * FROM users 
                WHERE Role = 'Customer'
                ORDER BY User_ID ASC";
}

$userResult = mysqli_query($conn, $userSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Manage Users</h1>

    <?php
    if ($message != "") {
        echo "<p><strong>$message</strong></p>";
    }
    ?>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>City</th>
            <th>Area</th>
            <th>Road No</th>
            <th>House No</th>
            <th>Role</th>
            <th>Account Status</th>
            <th>Action</th>
        </tr>

        <?php
        while ($user = mysqli_fetch_assoc($userResult)) {
        ?>
            <tr>
                <td><?php echo $user["User_ID"]; ?></td>
                <td><?php echo $user["Name"]; ?></td>
                <td><?php echo $user["Phone"]; ?></td>

                <td>
                    <?php
                    if ($user["City"] == NULL) {
                        echo "-";
                    } else {
                        echo $user["City"];
                    }
                    ?>
                </td>

                <td>
                    <?php
                    if ($user["Area"] == NULL) {
                        echo "-";
                    } else {
                        echo $user["Area"];
                    }
                    ?>
                </td>

                <td>
                    <?php
                    if ($user["Road_no"] == NULL) {
                        echo "-";
                    } else {
                        echo $user["Road_no"];
                    }
                    ?>
                </td>

                <td>
                    <?php
                    if ($user["House_no"] == NULL) {
                        echo "-";
                    } else {
                        echo $user["House_no"];
                    }
                    ?>
                </td>

                <td><?php echo $user["Role"]; ?></td>
                <td><?php echo $user["Account_Status"]; ?></td>

                <td>
                    <?php
                    if ($user["User_ID"] == $currentUserId) {
                        echo "Current User";
                    } else {
                        if ($user["Account_Status"] == "Active") {
                    ?>
                            <form method="POST" action="">
                                <input type="hidden" name="user_id" value="<?php echo $user["User_ID"]; ?>">
                                <button type="submit" name="block_user">Block</button>
                            </form>
                    <?php
                        } else {
                    ?>
                            <form method="POST" action="">
                                <input type="hidden" name="user_id" value="<?php echo $user["User_ID"]; ?>">
                                <button type="submit" name="unblock_user">Unblock</button>
                            </form>
                    <?php
                        }
                    }
                    ?>
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