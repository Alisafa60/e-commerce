<?php
header('Access-Control-Allow-Origin: *');
include('db_connection.php');

$username = $_POST['username'];
$password = $_POST['password'];

$query = $mysqli->prepare('SELECT u.user_id, u.username, u.password, r.role_name, r.role_id FROM users u 
                          INNER JOIN role r ON u.role_id = r.role_id 
                          WHERE u.username=?');
$query->bind_param('s', $username);
$query->execute();
$query->store_result();

if ($query->num_rows > 0) {
    $query->bind_result($user_id, $username, $hashed_password, $role_name, $role_id);
    $query->fetch();

    if (password_verify($password, $hashed_password)) {
        $token_payload = [
            "user_id" => $user_id,
            "role" => $role_name,
            "role_id" => $role_id
        ];

        $secret_key = "lazy_susan";
        $jwt_token = jwt_encode($token_payload, $secret_key);
        $response = ["status" => "true", "token" => $jwt_token];
    } else {
        $response = ["status" => "false", "message" => "Incorrect credentials"];
    }
} else {
    $response = ["status" => "false", "message" => "Incorrect credentials"];
}

echo json_encode($response);

// Function to encode a JWT token
function jwt_encode($payload, $secret_key) {
    // Base64Url encode the JWT header and payload
    $base64UrlHeader = base64UrlEncode(json_encode(["alg" => "HS256", "typ" => "JWT"]));
    $base64UrlPayload = base64UrlEncode(json_encode($payload));

    // Creating the signature using HMAC-SHA256
    $signature = hash_hmac("sha256", "$base64UrlHeader.$base64UrlPayload", $secret_key, true);

    // Base64Url encode the signature
    $base64UrlSignature = base64UrlEncode($signature);

    // Concatenate the base64Url-encoded header, payload, and signature to form the JWT token
    return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
}

// Function to perform Base64Url encoding
function base64UrlEncode($data) {
    $base64 = base64_encode($data);
    $base64Url = strtr($base64, '+/', '-_');
    return rtrim($base64Url, '=');
}
?>
