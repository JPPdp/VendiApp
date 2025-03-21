<?php
$host = 'localhost';
$db_name = 'vendi_services';
// Default XAMPP username
$username = 'root'; 
// Default XAMPP password (empty)
$password = ''; 

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
