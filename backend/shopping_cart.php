<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:*');
include("db_connection.php");
include("middleware.php");

authorize('costumer');

$select_query = $mysqli->prepare('SELECT * FROM shopping_cart');
$select_query->execute();
$result = $select_query->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($products);
?>