<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
include('db_connection.php');
include('middleware.php'); 


$seller_id = $_POST['seller_id']; 
$product_name = $_POST['product_name'];
$description = $_POST['description'];
$price = $_POST['price'];

authorize('seller', $seller_id);
// the entered seller ID corresponds to a user with a seller role
if (!isValidSeller($seller_id)) {
    $response = ["status" => "false", "message" => "Invalid seller ID"];
    echo json_encode($response);
    exit();
}

$query = $mysqli->prepare('INSERT INTO product (seller_id, product_name, description, price) VALUES (?, ?, ?, ?)');
$query->bind_param('issd', $seller_id, $product_name, $description, $price);

if ($query->execute()) {
    $response = ["status" => "true", "message" => "Product added successfully"];
} else {
    $response = ["status" => "false", "message" => "Failed to add product"];
}

echo json_encode($response);

function isValidSeller($seller_id) {
    global $mysqli;

    $query = $mysqli->prepare('SELECT user_id FROM users WHERE user_id = ? AND role_id = (SELECT role_id FROM role WHERE role_name = "seller")');
    $query->bind_param('i', $seller_id);
    $query->execute();
    $query->store_result();

    return $query->num_rows > 0;
}
?>
