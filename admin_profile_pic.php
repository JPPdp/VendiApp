<?php
session_start();

if (!isset($_SESSION['admin_profile'])) {
    header("Location: admin_vendors.php");
    exit();
}

// Assuming you have a database connection established
$conn = new mysqli("localhost", "root", "", "janrich_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the image blob from the database
$sql = "SELECT admin_profile FROM vendors WHERE admin_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['admin_name']);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($admin) {
    header("Content-Type: image/jpeg");
    echo $admin['admin_profile'];
} else {
    echo "No image found.";
}

$stmt->close();
$conn->close();
?>