<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:*');
include("db_connection.php");
include("middleware.php");

$seller_id = authorize('seller');

$query = $mysqli->prepare('SELECT * FROM product WHERE seller_id = ?');
$query->bind_param('i', $seller_id);
$query->execute();
$result = $query->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($products);
