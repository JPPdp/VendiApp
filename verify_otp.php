<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'vendor/autoload.php'; // Include JWT library

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Database connection parameters
$servername = "localhost";
$username = "root";  // your database username
$password = "";      // your database password
$dbname = "vendi_db";

// Secret key for JWT
$secret_key = "2169b56560cdbff74b4c9050c2db773ee0747800b27a78781a4e84aceb10a4450ff8049c25bb276a077c1835c862922aaa799138c2b2bcfeec028954cb12540c8f7d654fd6d18816497884937bee07c58e07b971eccce646af12557ee488a30a"; // Replace with a strong, random key.  Store securely!

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to send JSON response
function send_json_response($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response);
    exit();
}

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    // Get and decode JSON input
    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);

    // Check if JSON parsing was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON format');
    }

    // Validate input
    if (!isset($data['email']) || !isset($data['otp'])) {
        throw new Exception('Email and OTP are required');
    }

    // Sanitize input
    $email = sanitize_input($data['email']);
    $otp = sanitize_input($data['otp']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM otp_verification WHERE email = ? AND otp = ? LIMIT 1");
    if (!$stmt) {
        throw new Exception('Prepare statement failed: ' . $conn->error);
    }

    $stmt->bind_param("ss", $email, $otp);
    
    // Execute the statement
    if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }

    // Get result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $otp_data = $result->fetch_assoc();
        
        // Check if OTP is expired
        if (time() > $otp_data['expiry']) {
            throw new Exception('OTP has expired');
        }

        // Generate JWT Token
        $payload = array(
            "iss" => "localhost", // Replace with your domain
            "aud" => "localhost", // Replace with your domain
            "iat" => time(),
            "nbf" => time(),
            "exp" => time() + (60 * 60), // Token valid for 1 hour
            "data" => array(
                "email" => $email
            )
        );

        $jwt = JWT::encode($payload, $secret_key, 'HS256');

        // Send success response with JWT
        send_json_response(true, 'OTP verified successfully', [
            'token' => $jwt
        ]);
    } else {
        throw new Exception('Invalid OTP');
    }

} catch (Exception $e) {
    send_json_response(false, $e->getMessage());
} finally {
    // Close database connection
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>