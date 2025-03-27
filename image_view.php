<?php
session_start();

if (!isset($_SESSION['business_documents'])) {
    header("Location: dashboard.php");
    exit();
}

// Assuming you have a database connection established
$conn = new mysqli("localhost", "root", "", "janrich_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the image blob from the database
$sql = "SELECT business_documents FROM vendors WHERE businessname = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['businessname']);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

if ($vendor) {
    header("Content-Type: image/jpeg");
    echo $vendor['business_documents'];
} else {
    echo "No image found.";
}

$stmt->close();
$conn->close();
?>