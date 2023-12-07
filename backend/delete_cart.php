<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:*');
include("db_connection.php");
include("middleware.php");

$user_id = authorize('costumer');
authorize('costumer', $user_id);

if (!isset($_POST['cart_id'])) {
    echo json_encode(["status" => "error", "message" => "wrong identifier"]);
    exit();
}

$cart_id = $_POST['cart_id'];

$delete_query = $mysqli->prepare('DELETE FROM shopping_cart WHERE cart_id=? AND user_id=?');
$delete_query->bind_param('ii', $cart_id, $user_id);
$delete_query->execute();

if ($delete_query->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Product deleted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete product or unauthorized access"]);
}
?>

