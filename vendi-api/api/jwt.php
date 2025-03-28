<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;

$secret_key = "VENDIAPP_SECRET"; // Change this to a secure secret key

function generateJWT($user_id) {
    global $secret_key;

    $payload = [
        "iat" => time(),
        "exp" => time() + (60 * 60 * 24), // 1 day expiration
        "user_id" => $user_id
    ];

    return JWT::encode($payload, $secret_key, 'HS256');
}
?>
