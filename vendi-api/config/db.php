<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$db_name = 'vendi_db';

$conn = new mysqli($host, $user, $password, $db_name);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}
?>
