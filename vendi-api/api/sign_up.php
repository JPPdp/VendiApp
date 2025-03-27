<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (full_name, email, phone_number, password_hash) VALUES (:full_name, :email, :phone_number, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['full_name' => $full_name, 'email' => $email, 'phone_number' => $phone_number, 'password' => $password]);

    echo json_encode(['success' => true, 'message' => 'User registered successfully']);
}
?>
