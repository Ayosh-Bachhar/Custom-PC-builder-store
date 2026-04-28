<?php
session_start();

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Owner") {
    header("Location: ../index.php?role=login");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Owner Dashboard</title>
</head>
<body>

    <h1>Owner Dashboard</h1>

    <p>Welcome, <?php echo $_SESSION["Name"]; ?></p>

    <a href="profile.php">Manage Profile</a>
    <br><br>

    <a href="../logout.php">Logout</a>

</body>
</html>