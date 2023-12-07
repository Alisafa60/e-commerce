<?php
session_start();
include("db_connection.php");
include("middleware.php");

$user_id = authorize('costumer');

$product_id = $_POST['product_id'] ?? null;

// Validate product_id
if ($product_id !== null) {
    $validate_query = $mysqli->prepare('SELECT COUNT(*) FROM product WHERE product_id = ?');
    $validate_query->bind_param('i', $product_id);
    $validate_query->execute();
    $validate_query->bind_result($count);
    $validate_query->fetch();
    $validate_query->close(); 

    if ($count > 0) {
        // Initialize the cart if it doesn't exist
        if (!isset($_SESSION['shopping_cart'])) {
            $_SESSION['shopping_cart'] = [];
        }
        // Add product to the cart
        if (!isset($_SESSION['shopping_cart'][$product_id])) {
            $_SESSION['shopping_cart'][$product_id] = 1; 
        } else {
            $_SESSION['shopping_cart'][$product_id]++; 
        }
        // Store cart items in the database
        $quantity = $_SESSION['shopping_cart'][$product_id];

        // Check if the item already exists in the shopping_cart table a
        $check_query = $mysqli->prepare('SELECT COUNT(*) FROM shopping_cart WHERE user_id = ? AND product_id = ?');
        $check_query->bind_param('ii', $user_id, $product_id);
        $check_query->execute();
        $check_query->bind_result($existing_count);
        $check_query->fetch();
        $check_query->close(); 

        if ($existing_count > 0) {
            // Update the quantity if the item already exists
            $update_query = $mysqli->prepare('UPDATE shopping_cart SET quantity = ? WHERE user_id = ? AND product_id = ?');
            $update_query->bind_param('iii', $quantity, $user_id, $product_id);
            $update_query->execute();
        } else {
            // Insert a new record if the item doesn't exist
            $insert_query = $mysqli->prepare('INSERT INTO shopping_cart (user_id, product_id, quantity) VALUES (?, ?, ?)');
            $insert_query->bind_param('iii', $user_id, $product_id, $quantity);
            $insert_query->execute();
        }

        echo json_encode(["status" => "success", "message" => "Product added to cart"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid product_id"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Missing product_id"]);
}

// Display cart contents after adding items
echo "Shopping Cart Contents:\n";
foreach ($_SESSION['shopping_cart'] as $product_id => $quantity) {
    $product_query = $mysqli->prepare('SELECT product_name FROM product WHERE product_id = ?');
    $product_query->bind_param('i', $product_id);
    $product_query->execute();
    $product_query->bind_result($product_name);
    $product_query->fetch();
    echo "Product ID: $product_id, Product Name: $product_name, Quantity: $quantity\n";
    $product_query->close();
}
?>
