<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:*');
include("db_connection.php");
include("middleware.php");

$seller_id = authorize('seller');
authorize('seller', $seller_id);

if (!isset($_POST['product_id'])) {
    echo json_encode(["status" => "error", "message" => "wrong identifier"]);
    exit();
}

$product_id = $_POST['product_id'];

$delete_query = $mysqli->prepare('DELETE FROM product WHERE product_id=? AND seller_id=?');
$delete_query->bind_param('ii', $product_id, $seller_id);
$delete_query->execute();

if ($delete_query->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Product deleted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete product or unauthorized access"]);
}
?>
