<?php
// Database connection
$host = "localhost";
$db_name = "your_database";
$username = "your_username";
$password = "your_password";

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get userId from query parameter
$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

if ($userId > 0) {
    $stmt = $conn->prepare("SELECT profileName, profileEmail FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode([
            "profileName" => $user['profileName'],
            "profileEmail" => $user['profileEmail']
        ]);
    } else {
        echo json_encode(["error" => "User not found."]);
    }
} else {
    echo json_encode(["error" => "Invalid user ID."]);
}

$conn->close();
?>
