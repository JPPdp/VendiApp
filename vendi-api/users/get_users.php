<?php
// /users/get_users.php
include_once '../api/config.php';

$role = $_GET['role'] ?? '';

$table = '';
switch (strtolower($role)) {
    case 'user':
        $table = 'users';
        break;
    case 'vendor':
        $table = 'vendors';
        break;
    case 'admin':
        $table = 'admins';
        break;
    default:
        echo json_encode(["success" => false, "message" => "Invalid role specified"]);
        exit();
}

$query = "SELECT id, username AS name, email, created_at FROM $table";
$result = $conn->query($query);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(["success" => true, "data" => $users]);
} else {
    echo json_encode(["success" => false, "message" => "No $role found"]);
}
?>
