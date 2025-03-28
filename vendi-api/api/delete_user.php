<?php
// Database connection details
$host = "localhost";
$user = "root";
$password = "";
$database = "vendiapp";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

// Get user ID from POST request
$user_id = $_POST['user_id'];

// Check if user exists
$sql_check = "SELECT * FROM users WHERE user_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "User not found."]);
    exit();
}

// Delete user if found
$sql_delete = "DELETE FROM users WHERE user_id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $user_id);

if ($stmt_delete->execute()) {
    echo json_encode(["success" => true, "message" => "User deleted successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete user."]);
}

// Close connection
$conn->close();
?>
