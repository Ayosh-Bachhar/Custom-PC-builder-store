<?php
session_start();
include "config/db.php";

$message = "";

function cleanInput($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

/* =========================
   CUSTOMER REGISTRATION
========================= */
if (isset($_POST["customer_register"])) {
    $name = cleanInput($conn, $_POST["name"]);
    $phone = cleanInput($conn, $_POST["phone"]);
    $password = $_POST["password"];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $checkPhone = "SELECT * FROM users WHERE Phone = '$phone'";
    $result = mysqli_query($conn, $checkPhone);

    if (mysqli_num_rows($result) > 0) {
        $message = "This phone number is already registered.";
    } else {
        $insertUser = "INSERT INTO users (Name, Phone, Password, Account_Status, Role)
                       VALUES ('$name', '$phone', '$hashedPassword', 'Active', 'Customer')";

        if (mysqli_query($conn, $insertUser)) {
            $userId = mysqli_insert_id($conn);

            $insertCustomer = "INSERT INTO customer (User_ID)
                               VALUES ('$userId')";

            if (mysqli_query($conn, $insertCustomer)) {
                $message = "Customer registration successful.";
            } else {
                $message = "Customer insert failed: " . mysqli_error($conn);
            }
        } else {
            $message = "User insert failed: " . mysqli_error($conn);
        }
    }
}

/* =========================
   STAFF REGISTRATION
========================= */
if (isset($_POST["staff_register"])) {
    $name = cleanInput($conn, $_POST["name"]);
    $phone = cleanInput($conn, $_POST["phone"]);
    $staffId = cleanInput($conn, $_POST["staff_id"]);
    $password = $_POST["password"];

    $checkStaffId = "SELECT * FROM employee_staff
                     WHERE Staff_ID = '$staffId'
                     AND Is_Active = 'Yes'
                     AND User_ID IS NULL";

    $staffResult = mysqli_query($conn, $checkStaffId);

    if (mysqli_num_rows($staffResult) == 0) {
        $message = "Invalid staff ID, inactive staff ID, or already used staff ID.";
    } else {
        $checkPhone = "SELECT * FROM users WHERE Phone = '$phone'";
        $phoneResult = mysqli_query($conn, $checkPhone);

        if (mysqli_num_rows($phoneResult) > 0) {
            $message = "This phone number is already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertUser = "INSERT INTO users (Name, Phone, Password, Account_Status, Role)
                           VALUES ('$name', '$phone', '$hashedPassword', 'Active', 'Staff')";

            if (mysqli_query($conn, $insertUser)) {
                $userId = mysqli_insert_id($conn);

                $updateStaff = "UPDATE employee_staff
                                SET User_ID = '$userId', Join_Date = CURDATE()
                                WHERE Staff_ID = '$staffId'";

                if (mysqli_query($conn, $updateStaff)) {
                    $message = "Staff registration successful.";
                } else {
                    $message = "Staff update failed: " . mysqli_error($conn);
                }
            } else {
                $message = "User insert failed: " . mysqli_error($conn);
            }
        }
    }
}

/* =========================
   OWNER REGISTRATION
========================= */
if (isset($_POST["owner_register"])) {
    $name = cleanInput($conn, $_POST["name"]);
    $phone = cleanInput($conn, $_POST["phone"]);
    $ownerCode = cleanInput($conn, $_POST["owner_code"]);
    $password = $_POST["password"];

    $validOwnerCode = "ADMIN2026";

    if ($ownerCode != $validOwnerCode) {
        $message = "Invalid owner code.";
    } else {
        $checkPhone = "SELECT * FROM users WHERE Phone = '$phone'";
        $phoneResult = mysqli_query($conn, $checkPhone);

        if (mysqli_num_rows($phoneResult) > 0) {
            $message = "This phone number is already registered.";
        } else {
            $checkOwner = "SELECT * FROM owner WHERE Owner_Code = '$ownerCode'";
            $ownerResult = mysqli_query($conn, $checkOwner);

            if (mysqli_num_rows($ownerResult) > 0) {
                $message = "Owner account already exists with this owner code.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $insertUser = "INSERT INTO users (Name, Phone, Password, Account_Status, Role)
                               VALUES ('$name', '$phone', '$hashedPassword', 'Active', 'Owner')";

                if (mysqli_query($conn, $insertUser)) {
                    $userId = mysqli_insert_id($conn);

                    $insertOwner = "INSERT INTO owner (User_ID, Owner_Code)
                                    VALUES ('$userId', '$ownerCode')";

                    if (mysqli_query($conn, $insertOwner)) {
                        $message = "Owner registration successful.";
                    } else {
                        $message = "Owner insert failed: " . mysqli_error($conn);
                    }
                } else {
                    $message = "User insert failed: " . mysqli_error($conn);
                }
            }
        }
    }
}

/* =========================
   LOGIN
========================= */
if (isset($_POST["login"])) {
    $phone = cleanInput($conn, $_POST["phone"]);
    $password = $_POST["password"];

    $findUser = "SELECT * FROM users WHERE Phone = '$phone'";
    $result = mysqli_query($conn, $findUser);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if ($user["Account_Status"] == "Blocked") {
            $message = "Your account is blocked.";
        } else {
            if (password_verify($password, $user["Password"])) {
                if ($user["Role"] == "Staff") {
                    $userId = $user["User_ID"];

                    $checkStaff = "SELECT * FROM employee_staff
                                   WHERE User_ID = '$userId'
                                   AND Is_Active = 'Yes'";

                    $staffResult = mysqli_query($conn, $checkStaff);

                    if (mysqli_num_rows($staffResult) == 0) {
                        $message = "Your staff account is inactive or terminated.";
                    } else {
                        $_SESSION["User_ID"] = $user["User_ID"];
                        $_SESSION["Name"] = $user["Name"];
                        $_SESSION["Role"] = $user["Role"];

                        header("Location: dashboards/staff/staff_dashboard.php");
                        exit();
                    }
                } else {
                    $_SESSION["User_ID"] = $user["User_ID"];
                    $_SESSION["Name"] = $user["Name"];
                    $_SESSION["Role"] = $user["Role"];

                    if ($user["Role"] == "Customer") {
                        header("Location: dashboards/customer/customer_dashboard.php");
                        exit();
                    } elseif ($user["Role"] == "Owner") {
                        header("Location: dashboards/admin/owner_dashboard.php");
                        exit();
                    }
                }
            } else {
                $message = "Wrong password.";
            }
        }
    } else {
        $message = "No account found with this phone number.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Custom PC Builder Store</title>
    <link rel="stylesheet" href="webstyle/style.css">
</head>
<body>

    <h1>Custom PC Builder Store</h1>
    <h2>Role Selection Gateway</h2>

    <p>
        <a href="index.php?role=customer"><button>Customer</button></a>
        <a href="index.php?role=staff"><button>Staff Member</button></a>
        <a href="index.php?role=owner"><button>Owner</button></a>
        <a href="index.php?role=login"><button>Login</button></a>
    </p>

    <hr>

    <p style="color: red;">
        <?php echo $message; ?>
    </p>

    <?php
    if (isset($_GET["role"]) && $_GET["role"] == "customer") {
    ?>

        <h2>Customer Registration</h2>

        <form method="POST" action="">
            <label>Name:</label><br>
            <input type="text" name="name" required><br><br>

            <label>Phone:</label><br>
            <input type="text" name="phone" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required minlength="8"><br><br>

            <button type="submit" name="customer_register">Register as Customer</button>
        </form>

    <?php
    } elseif (isset($_GET["role"]) && $_GET["role"] == "staff") {
    ?>

        <h2>Staff Registration</h2>

        <form method="POST" action="">
            <label>Name:</label><br>
            <input type="text" name="name" required><br><br>

            <label>Phone:</label><br>
            <input type="text" name="phone" required><br><br>

            <label>Staff ID:</label><br>
            <input type="text" name="staff_id" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required minlength="8"><br><br>

            <button type="submit" name="staff_register">Register as Staff</button>
        </form>

    <?php
    } elseif (isset($_GET["role"]) && $_GET["role"] == "owner") {
    ?>

        <h2>Owner Registration</h2>

        <form method="POST" action="">
            <label>Name:</label><br>
            <input type="text" name="name" required><br><br>

            <label>Phone:</label><br>
            <input type="text" name="phone" required><br><br>

            <label>Owner Code:</label><br>
            <input type="text" name="owner_code" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required minlength="8"><br><br>

            <button type="submit" name="owner_register">Register as Owner</button>
        </form>

    <?php
    } elseif (isset($_GET["role"]) && $_GET["role"] == "login") {
    ?>


<form method="POST" action="">
    <div>
        <label for="login_phone">Phone:</label>
        <input type="text" id="login_phone" name="phone" required>
    </div>

    

    <div>
        <label for="login_password">Password:</label>
        <input type="password" id="login_password" name="password" required>
    </div>

    

    <div style="text-align: center; width: 270px;">
        <button type="submit" name="login">Login</button>
    </div>
</form>

    <?php
    } else {
    ?>

        <h3>Please select your role above.</h3>

    <?php
    }
    ?>

</body>
</html>