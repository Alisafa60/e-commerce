<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:*');
include("db_connection.php");


function insertRoles($roles) {
    global $mysqli;

    foreach ($roles as $role) {
        $role_name = $role['role_name'];
        $query = $mysqli->prepare('INSERT INTO role (role_name) VALUES (?)');
        $query->bind_param('s', $role_name);

        if (!$query->execute()) {
            echo "Failed to insert role into the database: " . $mysqli->error . "\n";
            return false;
        }
    }

    return true;
}

$rolesToInsert = [
    ['role_name' => 'seller'],
    ['role_name' => 'costumer'],
];

// Initialize roles
if (insertRoles($rolesToInsert)) {
    echo "Roles initialized successfully.\n";
} else {
    echo "Roles initialization failed.\n";
}