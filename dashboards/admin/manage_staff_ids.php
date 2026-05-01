<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Owner") {
    header("Location: ../../index.php?role=login");
    exit();
}

$message = "";
$ownerUserId = $_SESSION["User_ID"];

/* Create new staff ID */
if (isset($_POST["create_staff_id"])) {
    $staffId = mysqli_real_escape_string($conn, $_POST["staff_id"]);
    $staffName = mysqli_real_escape_string($conn, $_POST["staff_name"]);

    $checkSql = "SELECT * FROM employee_staff WHERE Staff_ID = '$staffId'";
    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        $message = "This Staff ID already exists.";
    } else {
        $insertSql = "INSERT INTO employee_staff
                      (Staff_ID, Staff_Name, User_ID, Join_Date, Quit_Date, Is_Active, Owner_User_ID)
                      VALUES
                      ('$staffId', '$staffName', NULL, NULL, NULL, 'Yes', '$ownerUserId')";

        if (mysqli_query($conn, $insertSql)) {
            $message = "Staff ID created successfully.";
        } else {
            $message = "Staff ID creation failed: " . mysqli_error($conn);
        }
    }
}

/* Terminate staff ID */
if (isset($_POST["terminate_staff_id"])) {
    $staffId = $_POST["staff_id"];

    $updateSql = "UPDATE employee_staff
                  SET Is_Active = 'No',
                      Quit_Date = CURDATE()
                  WHERE Staff_ID = '$staffId'";

    if (mysqli_query($conn, $updateSql)) {
        $message = "Staff ID terminated successfully.";
    } else {
        $message = "Terminate failed: " . mysqli_error($conn);
    }
}

/* Activate staff ID again */
if (isset($_POST["activate_staff_id"])) {
    $staffId = $_POST["staff_id"];

    $updateSql = "UPDATE employee_staff
                  SET Is_Active = 'Yes',
                      Quit_Date = NULL
                  WHERE Staff_ID = '$staffId'";

    if (mysqli_query($conn, $updateSql)) {
        $message = "Staff ID activated successfully.";
    } else {
        $message = "Activate failed: " . mysqli_error($conn);
    }
}

/* Show all staff IDs */
$listSql = "SELECT Staff_ID,
                   Staff_Name,
                   User_ID,
                   Join_Date,
                   Quit_Date,
                   Is_Active
            FROM employee_staff
            ORDER BY Staff_ID ASC";

$listResult = mysqli_query($conn, $listSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Staff IDs</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Manage Staff IDs</h1>

    <?php
    if ($message != "") {
        echo "<p><strong>$message</strong></p>";
    }
    ?>

    <h2>Create New Staff ID</h2>

    <form method="POST" action="">
        <label>Staff ID:</label><br>
        <input type="text" name="staff_id" required>
        <br><br>

        <label>Staff Name:</label><br>
        <input type="text" name="staff_name" required>
        <br><br>

        <button type="submit" name="create_staff_id">Create Staff ID</button>
    </form>

    <hr>

    <h2>Staff ID List</h2>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Staff ID</th>
            <th>Staff Name</th>
            <th>Status</th>
            <th>Phone</th>
            <th>Join Date</th>
            <th>Quit Date</th>
            <th>Action</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($listResult)) {
        ?>
            <tr>
                <td><?php echo $row["Staff_ID"]; ?></td>

                <td>
                    <?php
                    if ($row["Staff_Name"] == NULL) {
                        echo "-";
                    } else {
                        echo $row["Staff_Name"];
                    }
                    ?>
                </td>

                <td>
                    <?php
                    if ($row["Is_Active"] == "Yes") {
                        echo "Active";
                    } else {
                        echo "InActive";
                    }
                    ?>
                </td>

                <td>
                    <?php
                    if ($row["User_ID"] == NULL) {
                        echo "-";
                    } else {
                        $userId = $row["User_ID"];

                        $phoneSql = "SELECT Phone FROM users WHERE User_ID = '$userId'";
                        $phoneResult = mysqli_query($conn, $phoneSql);

                        if (mysqli_num_rows($phoneResult) > 0) {
                            $phoneRow = mysqli_fetch_assoc($phoneResult);
                            echo $phoneRow["Phone"];
                        } else {
                            echo "-";
                        }
                    }
                    ?>
                </td>

                <td>
                    <?php
                    if ($row["Join_Date"] == NULL) {
                        echo "NULL";
                    } else {
                        echo $row["Join_Date"];
                    }
                    ?>
                </td>

                <td>
                    <?php
                    if ($row["Quit_Date"] == NULL) {
                        echo "NULL";
                    } else {
                        echo $row["Quit_Date"];
                    }
                    ?>
                </td>

                <td>
                    <?php
                    if ($row["Is_Active"] == "Yes") {
                    ?>
                        <form method="POST" action="">
                            <input type="hidden" name="staff_id" value="<?php echo $row["Staff_ID"]; ?>">
                            <button type="submit" name="terminate_staff_id">Terminate</button>
                        </form>
                    <?php
                    } else {
                    ?>
                        <form method="POST" action="">
                            <input type="hidden" name="staff_id" value="<?php echo $row["Staff_ID"]; ?>">
                            <button type="submit" name="activate_staff_id">Activate</button>
                        </form>
                    <?php
                    }
                    ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>

    <br>

    <a href="owner_dashboard.php">Back to Owner Dashboard</a>

</body>
</html>