<?php
// /users/update_user.php
include_once '../api/config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['id']) && !empty($data['username']) && !empty($data['email']) && !empty($data['role'])) {
    $id = $conn->real_escape_string($data['id']);
    $username = $conn->real_escape_string($data['username']);
    $email = $conn->real_escape_string($data['email']);
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

    $query = "UPDATE $table SET $username_field = '$username', email = '$email' WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(["success" => true, "message" => ucfirst($role) . " updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update $role: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
}
?>
