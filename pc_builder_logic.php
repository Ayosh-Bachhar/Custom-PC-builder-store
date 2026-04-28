<?php
session_start();
include "config/db.php";

$message = "";

/* =========================================
   1. ADD TO CART BACKEND LOGIC (SQL)
========================================= */
if (isset($_POST['add_build_to_cart'])) {
    if (!isset($_SESSION["User_ID"]) || $_SESSION["Role"] != "Customer") {
        $message = "<span style='color:red;'>You must be logged in as a Customer to add builds to your cart.</span> <a href='index.php?role=login'>Login here</a>";
    } else {
        $customerId = $_SESSION["User_ID"];
        
        // Find existing cart or create a new one
        $cartCheck = mysqli_query($conn, "SELECT Cart_ID FROM cart WHERE Customer_User_ID = '$customerId'");
        if (mysqli_num_rows($cartCheck) > 0) {
            $cartRow = mysqli_fetch_assoc($cartCheck);
            $cartId = $cartRow['Cart_ID'];
        } else {
            mysqli_query($conn, "INSERT INTO cart (Customer_User_ID) VALUES ('$customerId')");
            $cartId = mysqli_insert_id($conn);
        }

        // Required and optional fields from the form
        $selectedItems = ['cpu_id', 'mobo_id', 'ram_id', 'storage_id', 'psu_id', 'gpu_id'];
        $addedCount = 0;

        foreach ($selectedItems as $itemKey) {
            if (!empty($_POST[$itemKey])) {
                $productId = mysqli_real_escape_string($conn, $_POST[$itemKey]);
                
                // Fetch unit price for cart_item
                $priceQuery = mysqli_query($conn, "SELECT Price FROM product WHERE Product_ID = '$productId'");
                if ($priceRow = mysqli_fetch_assoc($priceQuery)) {
                    $price = $priceRow['Price'];
                    
                    // Insert into cart_item
                    $insertItem = "INSERT INTO cart_item (Cart_ID, Quantity, Unit_Price, Product_ID) 
                                   VALUES ('$cartId', 1, '$price', '$productId')";
                    if(mysqli_query($conn, $insertItem)) {
                        $addedCount++;
                    }
                }
            }
        }
        $message = "<span style='color:green;'>$addedCount components successfully added to your cart!</span>";
    }
}

/* =========================================
   2. FETCH PRODUCTS & SPECS FOR UI (SQL)
========================================= */
$query = "
    SELECT p.Product_ID, p.Name, p.Price, p.Watt_Value, p.Category_Name,
           MAX(CASE WHEN ps.Specs = 'Socket' THEN ps.Spec_Value END) as Socket,
           MAX(CASE WHEN ps.Specs = 'RAM Type' THEN ps.Spec_Value END) as RAM_Type
    FROM product p
    LEFT JOIN product_spec ps ON p.Product_ID = ps.Product_ID
    WHERE p.Stock_Qty > 0
    GROUP BY p.Product_ID
";
$result = mysqli_query($conn, $query);

$products = [
    'CPU' => [],
    'Motherboard' => [],
    'RAM' => [],
    'Storage' => [],
    'Power Supply' => [],
    'Graphics Card' => []
];

while ($row = mysqli_fetch_assoc($result)) {
    $cat = $row['Category_Name'];
    if (isset($products[$cat])) {
        $products[$cat][] = $row;
    }
}

// Convert PHP array to JSON string to pass it to JavaScript later
$productsJson = json_encode($products);
?>
