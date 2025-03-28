<?php
$servername = "localhost";  // Change if needed
$username = "root";         // Change to your DB username
$password = "";             // Change to your DB password
$dbname = "vendi_services"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
