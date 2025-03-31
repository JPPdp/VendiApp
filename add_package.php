<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


// Check if vendor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vendor_id = $_SESSION['user_id'];
    $package_name = $_POST['package_name'];
    $package_size = $_POST['package_size'];
    $price = $_POST['price'];

    // Insert into the database
    $sql = "INSERT INTO vendor_packages (vendor_id, package_name, package_size, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isid", $vendor_id, $package_name, $package_size, $price);

    if ($stmt->execute()) {
        $message = "Package added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

header("Location: vendor_package.php"); // Redirect back to the dashboard
exit;
?>