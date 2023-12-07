<?php

function decode_jwt($jwt_token, $secret_key) {
    $token_parts = explode(".", $jwt_token);
    $token_payload = json_decode(base64UrlDecode($token_parts[1]), true);

    // verify the signature
    $signature = base64UrlDecode($token_parts[2]);
    $expected_signature = hash_hmac("sha256", "$token_parts[0].$token_parts[1]", $secret_key, true);

    if (hash_equals($expected_signature, $signature)) {
        return $token_payload;
    } else {
        return false;
    }
}

function base64UrlDecode($data) {
    $base64 = strtr($data, '-_', '+/');
    $base64_padded = str_pad($base64, strlen($data) % 4, '=', STR_PAD_RIGHT);
    return base64_decode($base64_padded);
}

// check if the user with a given user_id has the specified role
function isValidUserWithRole($user_id, $required_role) {
    global $mysqli;

    // check if the required role exists in the role table
    $role_query = $mysqli->prepare('SELECT role_id FROM role WHERE role_name = ?');
    $role_query->bind_param('s', $required_role);
    $role_query->execute();
    $role_query->store_result();

    if ($role_query->num_rows > 0) {
        $role_query->bind_result($role_id);
        $role_query->fetch();

        // check if the user has the required role
        $user_query = $mysqli->prepare('SELECT user_id FROM users WHERE user_id = ? AND role_id = ?');
        $user_query->bind_param('ii', $user_id, $role_id);
        $user_query->execute();
        $user_query->store_result();

        return $user_query->num_rows > 0;
    }

    return false;
}


// Authorization middleware
function authorize($required_role, $required_user_id = null) {
    $secret_key = "lazy_susan";

    // Authorization header
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
    list($jwt_token) = sscanf($auth_header, 'Bearer %s');

    if ($jwt_token) {
        $token_payload = decode_jwt($jwt_token, $secret_key);

        if (
            $token_payload &&
            isset($token_payload['role']) &&
            isValidUserWithRole($token_payload['user_id'], $required_role) &&
            (!$required_user_id || $token_payload['user_id'] == $required_user_id)
        ) {
            return true;
        }
    }

    http_response_code(401);
    echo json_encode(["status" => "false", "message" => "Unauthorized access"]);
    exit();
}