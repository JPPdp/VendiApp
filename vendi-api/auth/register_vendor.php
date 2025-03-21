<?php
include('../config/db.php');
include('../utils/hash_utils.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $service_type = $_POST['service_type'];
    $location = $_POST['location'];
    $password = $_POST['password'];

    $password_hash = hashPassword($password);

    $query = "INSERT INTO vendors (full_name, email, phone_number, service_type, location, availability_status, password_hash, rating, created_at) VALUES (?, ?, ?, ?, ?, 'available', ?, 5, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $full_name, $email, $phone_number, $service_type, $location, $password_hash);

    if ($stmt->execute()) {
        echo json_encode(array("success" => "Vendor registered successfully!"));
    } else {
        echo json_encode(array("error" => "Vendor registration failed!"));
    }
}
?>
