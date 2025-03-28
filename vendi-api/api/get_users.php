<?php
require_once('../config/db.php');

$sql = "SELECT user_id, full_name, email, phone_number FROM users";
$result = $conn->query($sql);

$users = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(['status' => 'success', 'users' => $users]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No users found']);
}

$conn->close();
?>
