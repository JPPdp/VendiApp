<?php
require_once('../config/db.php');

$data = json_decode(file_get_contents('php://input'), true);

$fullName = $data['full_name'];
$email = $data['email'];
$phone = $data['phone_number'];
$password = password_hash($data['password'], PASSWORD_BCRYPT);

$sql = "INSERT INTO users (full_name, email, phone_number, password) 
        VALUES ('$fullName', '$email', '$phone', '$password')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'User registered successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
}

$conn->close();
?>
