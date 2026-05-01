<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

/* =========================
   GET SELECTED VALUES
========================= */

$cpuId = "";
$cpuCoolerId = "";
$motherboardId = "";
$ramId = "";
$storageId = "";
$graphicsCardId = "";
$powerSupplyId = "";
$casingId = "";

$monitorId = "";
$casingFanId = "";
$keyboardId = "";
$mouseId = "";
$speakerId = "";
$headphoneId = "";
$wifiId = "";
$antiVirusId = "";
$upsId = "";

if (isset($_GET["cpu_id"])) {
    $cpuId = $_GET["cpu_id"];
}

if (isset($_GET["cpu_cooler_id"])) {
    $cpuCoolerId = $_GET["cpu_cooler_id"];
}

if (isset($_GET["motherboard_id"])) {
    $motherboardId = $_GET["motherboard_id"];
}

if (isset($_GET["ram_id"])) {
    $ramId = $_GET["ram_id"];
}

if (isset($_GET["storage_id"])) {
    $storageId = $_GET["storage_id"];
}

if (isset($_GET["graphics_card_id"])) {
    $graphicsCardId = $_GET["graphics_card_id"];
}

if (isset($_GET["power_supply_id"])) {
    $powerSupplyId = $_GET["power_supply_id"];
}

if (isset($_GET["casing_id"])) {
    $casingId = $_GET["casing_id"];
}

if (isset($_GET["monitor_id"])) {
    $monitorId = $_GET["monitor_id"];
}

if (isset($_GET["casing_fan_id"])) {
    $casingFanId = $_GET["casing_fan_id"];
}

if (isset($_GET["keyboard_id"])) {
    $keyboardId = $_GET["keyboard_id"];
}

if (isset($_GET["mouse_id"])) {
    $mouseId = $_GET["mouse_id"];
}

if (isset($_GET["speaker_id"])) {
    $speakerId = $_GET["speaker_id"];
}

if (isset($_GET["headphone_id"])) {
    $headphoneId = $_GET["headphone_id"];
}

if (isset($_GET["wifi_id"])) {
    $wifiId = $_GET["wifi_id"];
}

if (isset($_GET["anti_virus_id"])) {
    $antiVirusId = $_GET["anti_virus_id"];
}

if (isset($_GET["ups_id"])) {
    $upsId = $_GET["ups_id"];
}

/* =========================
   BASIC PRODUCT QUERY
========================= */

function getProductsByCategory($conn, $categoryName) {
    $sql = "SELECT * FROM product WHERE Category_Name = '$categoryName'";
    return mysqli_query($conn, $sql);
}

/* =========================
   GET CPU LIST
========================= */

$cpuResult = getProductsByCategory($conn, "CPU");

/* =========================
   GET CPU SOCKET
========================= */

$cpuSocket = "";

if ($cpuId != "") {
    $cpuSocketSql = "SELECT Spec_Value 
                     FROM product_spec
                     WHERE Product_ID = '$cpuId'
                     AND Specs = 'Socket'";

    $cpuSocketResult = mysqli_query($conn, $cpuSocketSql);

    if (mysqli_num_rows($cpuSocketResult) > 0) {
        $cpuSocketRow = mysqli_fetch_assoc($cpuSocketResult);
        $cpuSocket = $cpuSocketRow["Spec_Value"];
    }
}

/* =========================
   CPU COOLER DEPENDS ON CPU SOCKET
========================= */

if ($cpuSocket != "") {
    $cpuCoolerSql = "SELECT product.*
                     FROM product
                     INNER JOIN product_spec
                     ON product.Product_ID = product_spec.Product_ID
                     WHERE product.Category_Name = 'CPU Cooler'
                     AND product_spec.Specs = 'Supported Socket'
                     AND product_spec.Spec_Value LIKE '%$cpuSocket%'";
} else {
    $cpuCoolerSql = "SELECT * FROM product WHERE Category_Name = 'CPU Cooler'";
}

$cpuCoolerResult = mysqli_query($conn, $cpuCoolerSql);

/* =========================
   MOTHERBOARD DEPENDS ON CPU SOCKET
========================= */

if ($cpuSocket != "") {
    $motherboardSql = "SELECT product.*
                       FROM product
                       INNER JOIN product_spec
                       ON product.Product_ID = product_spec.Product_ID
                       WHERE product.Category_Name = 'Motherboard'
                       AND product_spec.Specs = 'Socket'
                       AND product_spec.Spec_Value = '$cpuSocket'";
} else {
    $motherboardSql = "SELECT * FROM product WHERE Category_Name = 'Motherboard'";
}

$motherboardResult = mysqli_query($conn, $motherboardSql);

/* =========================
   GET MOTHERBOARD SPECS
========================= */

$motherboardRamType = "";
$motherboardStorageSupport = "";
$motherboardFormFactor = "";

if ($motherboardId != "") {
    $ramTypeSql = "SELECT Spec_Value 
                   FROM product_spec
                   WHERE Product_ID = '$motherboardId'
                   AND Specs = 'RAM Type'";

    $ramTypeResult = mysqli_query($conn, $ramTypeSql);

    if (mysqli_num_rows($ramTypeResult) > 0) {
        $ramTypeRow = mysqli_fetch_assoc($ramTypeResult);
        $motherboardRamType = $ramTypeRow["Spec_Value"];
    }

    $storageSupportSql = "SELECT Spec_Value 
                          FROM product_spec
                          WHERE Product_ID = '$motherboardId'
                          AND Specs = 'Storage Support'";

    $storageSupportResult = mysqli_query($conn, $storageSupportSql);

    if (mysqli_num_rows($storageSupportResult) > 0) {
        $storageSupportRow = mysqli_fetch_assoc($storageSupportResult);
        $motherboardStorageSupport = $storageSupportRow["Spec_Value"];
    }

    $formFactorSql = "SELECT Spec_Value 
                      FROM product_spec
                      WHERE Product_ID = '$motherboardId'
                      AND Specs = 'Form Factor'";

    $formFactorResult = mysqli_query($conn, $formFactorSql);

    if (mysqli_num_rows($formFactorResult) > 0) {
        $formFactorRow = mysqli_fetch_assoc($formFactorResult);
        $motherboardFormFactor = $formFactorRow["Spec_Value"];
    }
}

/* =========================
   RAM DEPENDS ON MOTHERBOARD RAM TYPE
========================= */

if ($motherboardRamType != "") {
    $ramSql = "SELECT product.*
               FROM product
               INNER JOIN product_spec
               ON product.Product_ID = product_spec.Product_ID
               WHERE product.Category_Name = 'RAM'
               AND product_spec.Specs = 'RAM Type'
               AND product_spec.Spec_Value = '$motherboardRamType'";
} else {
    $ramSql = "SELECT * FROM product WHERE Category_Name = 'RAM'";
}

$ramResult = mysqli_query($conn, $ramSql);

/* =========================
   STORAGE DEPENDS ON MOTHERBOARD STORAGE SUPPORT
========================= */

if ($motherboardStorageSupport != "") {
    $storageSql = "SELECT product.*
                   FROM product
                   INNER JOIN product_spec
                   ON product.Product_ID = product_spec.Product_ID
                   WHERE product.Category_Name = 'Storage'
                   AND product_spec.Specs = 'Storage Type'
                   AND product_spec.Spec_Value = '$motherboardStorageSupport'";
} else {
    $storageSql = "SELECT * FROM product WHERE Category_Name = 'Storage'";
}

$storageResult = mysqli_query($conn, $storageSql);

/* =========================
   CASING DEPENDS ON MOTHERBOARD FORM FACTOR
========================= */

if ($motherboardFormFactor != "") {
    $casingSql = "SELECT product.*
                  FROM product
                  INNER JOIN product_spec
                  ON product.Product_ID = product_spec.Product_ID
                  WHERE product.Category_Name = 'Casing'
                  AND product_spec.Specs = 'Motherboard Support'
                  AND product_spec.Spec_Value LIKE '%$motherboardFormFactor%'";
} else {
    $casingSql = "SELECT * FROM product WHERE Category_Name = 'Casing'";
}

$casingResult = mysqli_query($conn, $casingSql);

/* =========================
   NORMAL REQUIRED COMPONENTS
========================= */

$graphicsCardResult = getProductsByCategory($conn, "Graphics Card");
$powerSupplyResult = getProductsByCategory($conn, "Power Supply");

/* =========================
   OPTIONAL COMPONENTS
========================= */

$monitorResult = getProductsByCategory($conn, "Monitor");
$casingFanResult = getProductsByCategory($conn, "Casing Fan");
$keyboardResult = getProductsByCategory($conn, "Keyboard");
$mouseResult = getProductsByCategory($conn, "Mouse");
$speakerResult = getProductsByCategory($conn, "Speaker & Home Theater");
$headphoneResult = getProductsByCategory($conn, "Headphone");
$wifiResult = getProductsByCategory($conn, "WiFi Adapter/LAN Card");
$antiVirusResult = getProductsByCategory($conn, "Anti Virus");
$upsResult = getProductsByCategory($conn, "UPS");
?>

<!DOCTYPE html>
<html>
<head>
    <title>PC Builder</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>PC Builder</h1>

    <p>Select parts step by step. The system will show compatible options.</p>

    <form method="GET" action="pc_build.php">

        <h2>Required Components</h2>

        <label>1. CPU:</label><br>
        <select name="cpu_id" onchange="this.form.submit()" required>
            <option value="">Select CPU</option>

            <?php while ($row = mysqli_fetch_assoc($cpuResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($cpuId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>2. CPU Cooler:</label><br>
        <select name="cpu_cooler_id" onchange="this.form.submit()" required>
            <option value="">Select CPU Cooler</option>

            <?php while ($row = mysqli_fetch_assoc($cpuCoolerResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($cpuCoolerId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>3. Motherboard:</label><br>
        <select name="motherboard_id" onchange="this.form.submit()" required>
            <option value="">Select Motherboard</option>

            <?php while ($row = mysqli_fetch_assoc($motherboardResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($motherboardId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>4. RAM:</label><br>
        <select name="ram_id" onchange="this.form.submit()" required>
            <option value="">Select RAM</option>

            <?php while ($row = mysqli_fetch_assoc($ramResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($ramId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>5. Storage:</label><br>
        <select name="storage_id" onchange="this.form.submit()" required>
            <option value="">Select Storage</option>

            <?php while ($row = mysqli_fetch_assoc($storageResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($storageId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>6. Graphics Card:</label><br>
        <select name="graphics_card_id" onchange="this.form.submit()" required>
            <option value="">Select Graphics Card</option>

            <?php while ($row = mysqli_fetch_assoc($graphicsCardResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($graphicsCardId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>7. Power Supply:</label><br>
        <select name="power_supply_id" onchange="this.form.submit()" required>
            <option value="">Select Power Supply</option>

            <?php while ($row = mysqli_fetch_assoc($powerSupplyResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($powerSupplyId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>8. Casing:</label><br>
        <select name="casing_id" onchange="this.form.submit()" required>
            <option value="">Select Casing</option>

            <?php while ($row = mysqli_fetch_assoc($casingResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($casingId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <hr>

        <h2>Optional Components</h2>

        <label>Monitor:</label><br>
        <select name="monitor_id" onchange="this.form.submit()">
            <option value="">No Monitor</option>
            <?php while ($row = mysqli_fetch_assoc($monitorResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($monitorId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>Casing Fan:</label><br>
        <select name="casing_fan_id" onchange="this.form.submit()">
            <option value="">No Casing Fan</option>
            <?php while ($row = mysqli_fetch_assoc($casingFanResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($casingFanId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>Keyboard:</label><br>
        <select name="keyboard_id" onchange="this.form.submit()">
            <option value="">No Keyboard</option>
            <?php while ($row = mysqli_fetch_assoc($keyboardResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($keyboardId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>Mouse:</label><br>
        <select name="mouse_id" onchange="this.form.submit()">
            <option value="">No Mouse</option>
            <?php while ($row = mysqli_fetch_assoc($mouseResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($mouseId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>Speaker & Home Theater:</label><br>
        <select name="speaker_id" onchange="this.form.submit()">
            <option value="">No Speaker & Home Theater</option>
            <?php while ($row = mysqli_fetch_assoc($speakerResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($speakerId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>Headphone:</label><br>
        <select name="headphone_id" onchange="this.form.submit()">
            <option value="">No Headphone</option>
            <?php while ($row = mysqli_fetch_assoc($headphoneResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($headphoneId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>WiFi Adapter/LAN Card:</label><br>
        <select name="wifi_id" onchange="this.form.submit()">
            <option value="">No WiFi Adapter/LAN Card</option>
            <?php while ($row = mysqli_fetch_assoc($wifiResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($wifiId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>Anti Virus:</label><br>
        <select name="anti_virus_id" onchange="this.form.submit()">
            <option value="">No Anti Virus</option>
            <?php while ($row = mysqli_fetch_assoc($antiVirusResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($antiVirusId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <label>UPS:</label><br>
        <select name="ups_id" onchange="this.form.submit()">
            <option value="">No UPS</option>
            <?php while ($row = mysqli_fetch_assoc($upsResult)) { ?>
                <option value="<?php echo $row["Product_ID"]; ?>"
                    <?php if ($upsId == $row["Product_ID"]) { echo "selected"; } ?>>
                    <?php echo $row["Name"]; ?> - <?php echo $row["Price"]; ?> Tk
                </option>
            <?php } ?>
        </select>

        <br><br>

        <?php
        if (
            $cpuId != "" &&
            $cpuCoolerId != "" &&
            $motherboardId != "" &&
            $ramId != "" &&
            $storageId != "" &&
            $graphicsCardId != "" &&
            $powerSupplyId != "" &&
            $casingId != ""
        ) {
        ?>
            <button type="submit" formaction="build_summary.php" formmethod="POST">
                Build Summary
            </button>

        <?php
        } else {
            echo "<p>Please select all required components to continue.</p>";
        }
        ?>

    </form>

    <br>

    <a href="../customer/customer_home.php">Back to Customer Home</a>

</body>
</html>