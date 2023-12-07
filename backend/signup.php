<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:*');
include("db_connection.php");

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];
$role_name = $_POST['role']; 

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$role_query = $mysqli->prepare('SELECT role_id FROM role WHERE role_name = ?');
$role_query->bind_param('s', $role_name);
$role_query->execute();
$role_result = $role_query->get_result();
$role_row = $role_result->fetch_assoc();

if ($role_row) {
    $role_id = $role_row['role_id'];

    $query = $mysqli->prepare('INSERT INTO users(first_name, last_name, email, username, password, role_id) VALUES (?, ?, ?, ?, ?, ?)');
    $query->bind_param('sssssi', $first_name, $last_name, $email, $username, $hashed_password, $role_id);
    $query->execute();

    if ($query->affected_rows > 0) {
        $response = ["status" => "true"];
    } else {
        $response = ["status" => "false", "message" => "Failed to insert user into the database."];
    }
} else {
    $response = ["status" => "false", "message" => "Role not found in the database."];
}

echo json_encode($response);
?>
