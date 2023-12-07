<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:*');
include("db_connection.php");
include("middleware.php");

$seller_id = authorize('seller');
authorize('seller', $seller_id);

//if missing some parameter
if (!isset($_POST['product_id'], $_POST['product_name'], $_POST['price'], $_POST['description'])) {
    echo json_encode(["status" => "error", "message" => "Missing required parameters"]);
    exit();
}

$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$price = $_POST['price'];
$description = $_POST['description'];

$update_query = $mysqli->prepare('UPDATE product SET product_name=?, price=?, description=? WHERE product_id=? AND seller_id=?');
$update_query->bind_param('sdsii', $product_name, $price, $description, $product_id, $seller_id);
$update_query->execute();

if ($update_query->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Product updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update product or unauthorized access"]);
}

?> 