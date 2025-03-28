<?php
// /auth/login.php
include_once '../api/config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['email']) && !empty($data['password']) && !empty($data['role'])) {
    $email = $conn->real_escape_string($data['email']);
    $password = $data['password'];
    $role = strtolower($data['role']);

    $table = '';
    switch ($role) {
        case 'user':
            $table = 'users';
            $username_field = 'username';
            break;
        case 'vendor':
            $table = 'vendors';
            $username_field = 'vendor_name';
            break;
        case 'admin':
            $table = 'admins';
            $username_field = 'admin_name';
            break;
        default:
            echo json_encode(["success" => false, "message" => "Invalid role specified"]);
            exit();
    }

    $query = "SELECT id, $username_field AS name, email, password FROM $table WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            unset($user['password']); // Remove password from response
            $user['role'] = $role;
            echo json_encode(["success" => true, "message" => "Login successful", "data" => $user]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => ucfirst($role) . " not found"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
}
?>
