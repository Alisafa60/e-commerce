<?php

$host = "localhost";
$db_user = "root";
$db_name = "e-commerce";
$db_pass = null;

$mysqli = new mysqli($host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
} else {
    
}
