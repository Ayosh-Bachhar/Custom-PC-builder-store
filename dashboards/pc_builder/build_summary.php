<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: pc_build.php");
    exit();
}

/* Get one product by Product_ID */
function getProduct($conn, $productId) {
    if ($productId == "") {
        return NULL;
    }

    $sql = "SELECT * FROM product WHERE Product_ID = '$productId'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        return NULL;
    }
}

/* Get one spec value from product_spec */
function getSpecValue($conn, $productId, $specName) {
    if ($productId == "") {
        return "";
    }

    $sql = "SELECT Spec_Value 
            FROM product_spec 
            WHERE Product_ID = '$productId' 
            AND Specs = '$specName'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row["Spec_Value"];
    } else {
        return "";
    }
}

/* Required products */
$cpu = getProduct($conn, $_POST["cpu_id"]);
$cpuCooler = getProduct($conn, $_POST["cpu_cooler_id"]);
$motherboard = getProduct($conn, $_POST["motherboard_id"]);
$ram = getProduct($conn, $_POST["ram_id"]);
$storage = getProduct($conn, $_POST["storage_id"]);
$graphicsCard = getProduct($conn, $_POST["graphics_card_id"]);
$powerSupply = getProduct($conn, $_POST["power_supply_id"]);
$casing = getProduct($conn, $_POST["casing_id"]);

/* Optional products */
$monitor = getProduct($conn, $_POST["monitor_id"]);
$casingFan = getProduct($conn, $_POST["casing_fan_id"]);
$keyboard = getProduct($conn, $_POST["keyboard_id"]);
$mouse = getProduct($conn, $_POST["mouse_id"]);
$speaker = getProduct($conn, $_POST["speaker_id"]);
$headphone = getProduct($conn, $_POST["headphone_id"]);
$wifi = getProduct($conn, $_POST["wifi_id"]);
$antiVirus = getProduct($conn, $_POST["anti_virus_id"]);
$ups = getProduct($conn, $_POST["ups_id"]);

/* Put selected products into one array */
$selectedProducts = array();

$selectedProducts[] = $cpu;
$selectedProducts[] = $cpuCooler;
$selectedProducts[] = $motherboard;
$selectedProducts[] = $ram;
$selectedProducts[] = $storage;
$selectedProducts[] = $graphicsCard;
$selectedProducts[] = $powerSupply;
$selectedProducts[] = $casing;

if ($monitor != NULL) {
    $selectedProducts[] = $monitor;
}

if ($casingFan != NULL) {
    $selectedProducts[] = $casingFan;
}

if ($keyboard != NULL) {
    $selectedProducts[] = $keyboard;
}

if ($mouse != NULL) {
    $selectedProducts[] = $mouse;
}

if ($speaker != NULL) {
    $selectedProducts[] = $speaker;
}

if ($headphone != NULL) {
    $selectedProducts[] = $headphone;
}

if ($wifi != NULL) {
    $selectedProducts[] = $wifi;
}

if ($antiVirus != NULL) {
    $selectedProducts[] = $antiVirus;
}

if ($ups != NULL) {
    $selectedProducts[] = $ups;
}

/* Price and wattage */
$totalPrice = 0;
$totalWattage = 0;

/* Compatibility data */
$cpuSocket = getSpecValue($conn, $_POST["cpu_id"], "Socket");
$motherboardSocket = getSpecValue($conn, $_POST["motherboard_id"], "Socket");

$ramType = getSpecValue($conn, $_POST["ram_id"], "RAM Type");
$motherboardRamType = getSpecValue($conn, $_POST["motherboard_id"], "RAM Type");

$storageType = getSpecValue($conn, $_POST["storage_id"], "Storage Type");
$motherboardStorageSupport = getSpecValue($conn, $_POST["motherboard_id"], "Storage Support");

/* Warning variables */
$socketWarning = "";
$ramWarning = "";
$storageWarning = "";

/* CPU and motherboard check */
if ($cpuSocket != "" && $motherboardSocket != "") {
    if ($cpuSocket != $motherboardSocket) {
        $socketWarning = "CPU and motherboard socket do not match.";
    }
}

/* RAM and motherboard check */
if ($ramType != "" && $motherboardRamType != "") {
    if ($ramType != $motherboardRamType) {
        $ramWarning = "RAM type and motherboard RAM type do not match.";
    }
}

/* SSD and motherboard check */
if ($storageType != "" && $motherboardStorageSupport != "") {
    if ($storageType != $motherboardStorageSupport) {
        $storageWarning = "SSD type and motherboard storage support do not match.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Build Summary</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>Build Summary</h1>

    <h2>Selected Components</h2>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Category</th>
            <th>Product</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Price</th>
            <th>Wattage</th>
        </tr>

        <?php
        foreach ($selectedProducts as $product) {
            if ($product != NULL) {
                $totalPrice = $totalPrice + $product["Price"];
                $totalWattage = $totalWattage + $product["Watt_Value"];
        ?>
                <tr>
                    <td><?php echo $product["Category_Name"]; ?></td>
                    <td><?php echo $product["Name"]; ?></td>
                    <td><?php echo $product["Brand"]; ?></td>
                    <td><?php echo $product["Model"]; ?></td>
                    <td><?php echo $product["Price"]; ?> Tk</td>
                    <td><?php echo $product["Watt_Value"]; ?> W</td>
                </tr>
        <?php
            }
        }
        ?>
    </table>

    <h2>Compatibility Check</h2>

    <?php
    if ($socketWarning == "" && $ramWarning == "" && $storageWarning == "") {
        echo "<p style='color:black; font-size:18px;'>";
        echo "CPU ✅ → Motherboard ✅ → RAM ✅ → SSD ✅";
        echo "</p>";
    } else {
        echo "<p style='color:black; font-size:18px;'>";
        echo "Compatibility problem found.";
        echo "</p>";

        if ($socketWarning != "") {
            echo "<p style='color:red;'>CPU ❌ → Motherboard ❌</p>";
        }

        if ($ramWarning != "") {
            echo "<p style='color:red;'>Motherboard ❌ → RAM ❌</p>";
        }

        if ($storageWarning != "") {
            echo "<p style='color:red;'>Motherboard ❌ → SSD ❌</p>";
        }
    }
    ?>

    <h2>Price and Wattage Summary</h2>

    <p><strong>Total Price:</strong> <?php echo $totalPrice; ?> Tk</p>
    <p><strong>Estimated Wattage:</strong> <?php echo $totalWattage; ?> W</p>

<br>
    
<form method="POST" action="../cart/add_build_to_cart.php">
    <?php
    foreach ($selectedProducts as $product) {
        if ($product != NULL) {
    ?>
            <input type="hidden" name="product_ids[]" value="<?php echo $product["Product_ID"]; ?>">
    <?php
        }
    }
    ?>

    <button type="submit">Add Full Build to Cart</button>
</form>

<br>

<button onclick="window.print()">Save Build as PDF</button>

<br><br>

<a href="pc_build.php">Back to PC Builder</a>

</body>
</html>