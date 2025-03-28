<?php
include('../config/db.php');
include('../utils/hash_utils.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $password_hash = hashPassword($password);

    $query = "INSERT INTO admins (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $email, $password_hash);

    if ($stmt->execute()) {
        echo json_encode(array("success" => "Admin registered successfully!"));
    } else {
        echo json_encode(array("error" => "Admin registration failed!"));
    }
}
?>
