<?php
// /auth/register.php
include_once '../api/config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['username']) && !empty($data['email']) && !empty($data['password']) && !empty($data['role'])) {
    $username = $conn->real_escape_string($data['username']);
    $email = $conn->real_escape_string($data['email']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $role = strtolower($data['role']);

    if ($role === 'user') {
        $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', 'user')";
    } elseif ($role === 'vendor') {
        $query = "INSERT INTO vendors (vendor_name, email, password) VALUES ('$username', '$email', '$password')";
    } elseif ($role === 'admin') {
        $query = "INSERT INTO admins (admin_name, email, password) VALUES ('$username', '$email', '$password')";
    } else {
        echo json_encode(["success" => false, "message" => "Invalid role specified"]);
        exit();
    }

    if ($conn->query($query)) {
        echo json_encode(["success" => true, "message" => ucfirst($role) . " registered successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Registration failed: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
}
?>
