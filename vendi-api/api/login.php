<?php
// Start output buffering and set content type to JSON
ob_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if email and password are set
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode(["status" => "error", "message" => "Missing email or password"]);
    exit();
}

// Get email and password from request
$email = $_POST['email'];
$password = $_POST['password'];

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'vendi_services');

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed!"]);
    exit();
}

// Query to get user by email
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // ✅ Check plain text password (no hashing for now)
    if ($password === $user['password']) {
        // Successful login, return JSON response
        ob_clean(); // Clean output before sending JSON
        echo json_encode([
            "status" => "success",
            "message" => "Login successful!",
            "user_id" => $user['user_id'],
            "full_name" => $user['full_name'],
            "email" => $user['email'],
            "phone_number" => $user['phone_number']
        ]);
    } else {
        // Invalid credentials
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Invalid credentials!"]);
    }
} else {
    // User not found
    ob_clean();
    echo json_encode(["status" => "error", "message" => "User not found!"]);
}

// Close connections
$stmt->close();
$conn->close();
?>
