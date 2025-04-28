<?php
header("Content-Type: application/json"); // Must be first line

include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

// Function to send consistent JSON responses
function sendJsonResponse($success, $message, $statusCode = 200) {
    http_response_code($statusCode);
    die(json_encode([
        'success' => $success,
        'message' => $message,
        'data' => null
    ]));
}

// Validate required fields
if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['mobile_number'])) {
    sendJsonResponse(false, "All fields are required", 400);
}

$name = trim($data['name']);
$email = trim($data['email']);
$password = trim($data['password']);
$mobile_number = trim($data['mobile_number']);
$address = isset($data['address']) ? trim($data['address']) : null; // Optional field

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJsonResponse(false, "Invalid email format", 400);
}

// Validate mobile number
if (!preg_match('/^[0-9]{10,15}$/', $mobile_number)) {
    sendJsonResponse(false, "Invalid mobile number (10-15 digits required)", 400);
}

// Check if email exists
$checkEmail = $conn->prepare("SELECT client_id FROM clients WHERE email = ?");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();

if ($checkEmail->get_result()->num_rows > 0) {
    sendJsonResponse(false, "Email already exists", 409);
}

// Hash password and create user
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Handle image upload and convert it to binary
$profile_picture = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
} else {
    // Optional: Handle if no profile picture is uploaded
    $profile_picture = null; // Can be set to NULL if not provided
}

// Prepare the insert query
$stmt = $conn->prepare("INSERT INTO clients (name, email, password, mobile_number, profile_picture, address) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $name, $email, $hashedPassword, $mobile_number, $profile_picture, $address);

// Execute the query
if ($stmt->execute()) {
    sendJsonResponse(true, "Registration successful", 201);
} else {
    sendJsonResponse(false, "Database error: " . $conn->error, 500);
}
?>
