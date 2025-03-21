<?php
include('../config/db.php');
include('../utils/hash_utils.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $table = $_POST['user_type']; // 'users', 'vendors', 'admins'

    if ($table !== 'users' && $table !== 'vendors' && $table !== 'admins') {
        echo json_encode(array("error" => "Invalid user type."));
        exit();
    }

    $query = "SELECT * FROM $table WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (verifyPassword($password, $user['password_hash'])) {
            unset($user['password_hash']);
            echo json_encode($user);
        } else {
            echo json_encode(array("error" => "Invalid password."));
        }
    } else {
        echo json_encode(array("error" => "User not found."));
    }
}
?>
