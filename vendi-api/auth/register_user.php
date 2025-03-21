<?php
include('../config/db.php');
include('../utils/hash_utils.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];

    $password_hash = hashPassword($password);

    $query = "INSERT INTO users (full_name, email, phone_number, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $full_name, $email, $phone_number, $password_hash);

    if ($stmt->execute()) {
        echo json_encode(array("success" => "User registered successfully!"));
    } else {
        echo json_encode(array("error" => "Registration failed!"));
    }
}
?>
