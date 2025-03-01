<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$host = 'localhost';
$dbname = 'vendi_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null) {
        echo json_encode(["success" => false, "message" => "Invalid JSON"]);
        exit;
    }

    $email = $data['email'] ?? '';
    $newPassword = $data['new_password'] ?? '';

    // Validate inputs
    if (empty($email) || empty($newPassword)) {
        echo json_encode(["success" => false, "message" => "Email and new password are required"]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email address"]);
        exit;
    }

    if (strlen($newPassword) < 6) {
        echo json_encode(["success" => false, "message" => "Password must be at least 6 characters long"]);
        exit;
    }

    // Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        echo json_encode(["success" => false, "message" => "Email not found"]);
        exit;
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the password in the database
    $updateStmt = $pdo->prepare("UPDATE clients SET password = :password WHERE email = :email");
    $updateStmt->bindParam(':password', $hashedPassword);
    $updateStmt->bindParam(':email', $email);

    if ($updateStmt->execute()) {
        // Generate OTP
        $otp = rand(100000, 999999);
        $otp_expiry = time() + (5 * 60); // OTP valid for 5 minutes

        // Store OTP in the database
        $otp_stmt = $pdo->prepare("INSERT INTO otp_verification (email, otp, expiry) VALUES (:email, :otp, :expiry)");
        $otp_stmt->bindParam(':email', $email);
        $otp_stmt->bindParam(':otp', $otp);
        $otp_stmt->bindParam(':expiry', $otp_expiry);
        $otp_stmt->execute();

        // Send OTP to user's email
        mail($email, "Your OTP Code", "Your OTP code is: $otp");

        // Send success response
        echo json_encode(["success" => true, "message" => "Password reset successfully. OTP sent to your email. Please verify.", "otp" => $otp]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to reset password"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
}
?>