<?php
include '../db_connect.php'; // Include your database connection

header("Content-Type: application/json"); // Set JSON response

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['name'], $data['email'], $data['password'], $data['mobile_number'])) {
    $name = $data['name'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_BCRYPT); // Hash password
    $mobile_number = $data['mobile_number'];

    // Check if email exists
    $checkEmail = $conn->prepare("SELECT * FROM clients WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists"]);
        exit;
    }

    // Insert new client
    $stmt = $conn->prepare("INSERT INTO clients (name, email, password, mobile_number) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $mobile_number);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Client registered successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error registering client"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}
?>
