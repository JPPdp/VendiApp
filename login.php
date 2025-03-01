<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require 'vendor/autoload.php';
use Firebase\JWT\JWT;

// Database connection parameters
$servername = "localhost";
$username = "root";  // your database username
$password = "";      // your database password
$dbname = "vendi_db";

// Secret key for JWT
$secret_key = "2169b56560cdbff74b4c9050c2db773ee0747800b27a78781a4e84aceb10a4450ff8049c25bb276a077c1835c862922aaa799138c2b2bcfeec028954cb12540c8f7d654fd6d18816497884937bee07c58e07b971eccce646af12557ee488a30a"; // Replace with a strong, random key. Store securely!

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
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get and decode JSON input
    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);

    // Check if JSON parsing was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON format');
    }

    // Validate input
    if (!isset($data['email']) || !isset($data['password'])) {
        throw new Exception('Email and password are required');
    }

    // Sanitize input
    $email = sanitize_input($data['email']);
    $password = $data['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Prepare statement to prevent SQL injection
    $query = "SELECT * FROM users WHERE email = :email";
    $statement = $conn->prepare($query);
    $statement->execute([':email' => $email]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify password
        if ($user['user_password'] === $password) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $otp_expiry = time() + (5 * 60); // OTP valid for 5 minutes

            // Store OTP in the database
            $otp_stmt = $conn->prepare("INSERT INTO otp_verification (email, otp, expiry) VALUES (:email, :otp, :expiry)");
            $otp_stmt->bindParam(':email', $email);
            $otp_stmt->bindParam(':otp', $otp);
            $otp_stmt->bindParam(':expiry', $otp_expiry);
            $otp_stmt->execute();

            // Send OTP to user's email
            mail($email, "Your OTP Code", "Your OTP code is: $otp");

            // Send success response
            send_json_response(true, 'OTP sent to your email. Please verify.', [
                'user_id' => $user['user_id'],
                'email' => $user['email']
            ]);
        } else {
            throw new Exception('Wrong Password');
        }
    } else {
        throw new Exception('Wrong Email Address');
    }

} catch (Exception $e) {
    send_json_response(false, $e->getMessage());
} finally {
    // Close database connection
    if (isset($statement)) {
        $statement->closeCursor();
    }
    if (isset($conn)) {
        $conn = null;
    }
}
?>
