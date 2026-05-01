<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
    header("Location: ../../index.php?role=login");
    exit();
}

$customerUserId = $_SESSION["User_ID"];

$cartSql = "SELECT * FROM cart WHERE Customer_User_ID = '$customerUserId'";
$cartResult = mysqli_query($conn, $cartSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Cart</title>
    <link rel="stylesheet" href="../../webstyle/style.css">
</head>
<body>

    <h1>My Cart</h1>

    <?php
    if (mysqli_num_rows($cartResult) == 0) {
        echo "<p>Your cart is empty.</p>";
    } else {
        $cart = mysqli_fetch_assoc($cartResult);
        $cartId = $cart["Cart_ID"];

        $itemSql = "SELECT cart_item.Cart_ID,
                           cart_item.Cart_Item_ID,
                           cart_item.Quantity,
                           cart_item.Unit_Price,
                           product.Name,
                           product.Brand,
                           product.Model
                    FROM cart_item
                    INNER JOIN product
                    ON cart_item.Product_ID = product.Product_ID
                    WHERE cart_item.Cart_ID = '$cartId'
                    ORDER BY cart_item.Cart_Item_ID ASC";

        $itemResult = mysqli_query($conn, $itemSql);

        if (mysqli_num_rows($itemResult) == 0) {
            echo "<p>Your cart is empty.</p>";
        } else {
            $grandTotal = 0;
    ?>

            <table border="1" cellpadding="10" cellspacing="0">
                <tr>
                    <th>Item ID</th>
                    <th>Product</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Line Total</th>
                    <th>Remove</th>
                </tr>

                <?php
                while ($item = mysqli_fetch_assoc($itemResult)) {
                    $lineTotal = $item["Quantity"] * $item["Unit_Price"];
                    $grandTotal = $grandTotal + $lineTotal;
                ?>
                    <tr>
                        <td><?php echo $item["Cart_Item_ID"]; ?></td>
                        <td><?php echo $item["Name"]; ?></td>
                        <td><?php echo $item["Brand"]; ?></td>
                        <td><?php echo $item["Model"]; ?></td>

                        <td>
                            <form method="POST" action="update_cart.php" style="display:inline;">
                                <input type="hidden" name="cart_id" value="<?php echo $item["Cart_ID"]; ?>">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item["Cart_Item_ID"]; ?>">
                                <button type="submit" name="decrease_quantity">-</button>
                            </form>

                            <?php echo $item["Quantity"]; ?>

                            <form method="POST" action="update_cart.php" style="display:inline;">
                                <input type="hidden" name="cart_id" value="<?php echo $item["Cart_ID"]; ?>">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item["Cart_Item_ID"]; ?>">
                                <button type="submit" name="increase_quantity">+</button>
                            </form>
                        </td>

                        <td><?php echo $item["Unit_Price"]; ?> Tk</td>
                        <td><?php echo $lineTotal; ?> Tk</td>

                        <td>
                            <form method="POST" action="update_cart.php">
                                <input type="hidden" name="cart_id" value="<?php echo $item["Cart_ID"]; ?>">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item["Cart_Item_ID"]; ?>">
                                <button type="submit" name="remove_item">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php
                }
                ?>

            </table>

            <h3>Grand Total: <?php echo $grandTotal; ?> Tk</h3>

            <a href="../checkout_order/checkout.php">
                <button>Proceed to Checkout</button>
            </a>

    <?php
        }
    }
    ?>

    <br><br>

    <a href="../customer/customer_home.php">Continue Shopping</a>
    <br><br>

    <a href="../customer/customer_dashboard.php">Back to Dashboard</a>

</body>
</html>