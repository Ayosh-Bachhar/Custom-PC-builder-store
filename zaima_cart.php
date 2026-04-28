<?php
session_start();

/* Initialize cart */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

/* Add product to cart */
if (isset($_GET['add'])) {
    $id = $_GET['id'];
    $name = $_GET['name'];
    $price = $_GET['price'];

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        $_SESSION['cart'][$id] = array(
            "name" => $name,
            "price" => $price,
            "quantity" => 1
        );
    }

    header("Location: cart.php");
    exit();
}

/* Remove product from cart */
if (isset($_GET['remove'])) {
    $id = $_GET['id'];

    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }

    header("Location: cart.php");
    exit();
}

/* Update quantity */
if (isset($_POST['update'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        $qty = intval($qty);

        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['quantity'] = $qty;
        }
    }

    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart Management</title>
</head>
<body>

<h2>Cart Management</h2>

<h3>Products</h3>

<a href="cart.php?add=1&id=1&name=CPU&price=20000">Add CPU - ৳20000</a><br>
<a href="cart.php?add=1&id=2&name=RAM&price=5000">Add RAM - ৳5000</a><br>
<a href="cart.php?add=1&id=3&name=SSD&price=8000">Add SSD - ৳8000</a><br>
<a href="cart.php?add=1&id=4&name=GPU&price=42000">Add GPU - ৳42000</a><br>

<hr>

<h3>Your Cart</h3>

<form method="POST" action="cart.php">

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>Product</th>
        <th>Unit Price</th>
        <th>Quantity</th>
        <th>Line Total</th>
        <th>Action</th>
    </tr>

    <?php
    $total = 0;

    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $id => $item) {
            $line_total = $item['price'] * $item['quantity'];
            $total += $line_total;
    ?>

    <tr>
        <td><?php echo $item['name']; ?></td>
        <td>৳<?php echo $item['price']; ?></td>
        <td>
            <input 
                type="number" 
                name="qty[<?php echo $id; ?>]" 
                value="<?php echo $item['quantity']; ?>" 
                min="1"
            >
        </td>
        <td>৳<?php echo $line_total; ?></td>
        <td>
            <a href="cart.php?remove=1&id=<?php echo $id; ?>">Remove</a>
        </td>
    </tr>

    <?php
        }
    } else {
        echo "<tr><td colspan='5'>Cart is empty</td></tr>";
    }
    ?>

    <tr>
        <td colspan="3"><strong>Total Price</strong></td>
        <td colspan="2"><strong>৳<?php echo $total; ?></strong></td>
    </tr>
</table>

<br>

<button type="submit" name="update">Update Cart</button>

</form>

</body>
</html>
