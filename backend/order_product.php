<?php
session_start();
include("db_connection.php");
include("middleware.php");

$user_id = authorize('costumer');

// Check if the cart is not empty
if (isset($_SESSION['shopping_cart']) && !empty($_SESSION['shopping_cart'])) {
    $total_amount = 0;
    foreach ($_SESSION['shopping_cart'] as $product_id => $quantity) {
        $price_query = $mysqli->prepare('SELECT price FROM product WHERE product_id = ?');
        $price_query->bind_param('i', $product_id);
        $price_query->execute();
        $price_query->bind_result($price);
        $price_query->fetch();
        $price_query->close();

        $total_amount += $price * $quantity;
    }

    // Create an order record
    $insert_order_query = $mysqli->prepare('INSERT INTO orders (user_id, total_amount, order_date) VALUES (?, ?, NOW())');
    $insert_order_query->bind_param('id', $user_id, $total_amount);
    $insert_order_query->execute();
    $order_id = $insert_order_query->insert_id;
    $insert_order_query->close();

    // Add items to order history
    foreach ($_SESSION['shopping_cart'] as $product_id => $quantity) {
        $insert_history_query = $mysqli->prepare('INSERT INTO order_history (order_id, product_id, quantity, order_date) VALUES (?, ?, ?, NOW())');
        $insert_history_query->bind_param('iii', $order_id, $product_id, $quantity);
        $insert_history_query->execute();
        $insert_history_query->close();
    }

    // Clear the shopping cart
    unset($_SESSION['shopping_cart']);

    echo json_encode(["status" => "success", "message" => "Order placed successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Your cart is empty"]);
}
?>
