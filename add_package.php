<?php
include 'db_connect.php';
session_start();

// Check if vendor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vendor_id = $_SESSION['user_id'];
    $package_name = $_POST['package_name'];
    $package_description = $_POST['package_description'];
    $package_size = $_POST['package_size'];
    $price = $_POST['price'];
    
    // Handle file upload for package image
    $package_image = null;
    if (isset($_FILES['package_image']) && $_FILES['package_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/"; // Make sure this directory exists and is writable
        $target_file = $target_dir . basename($_FILES["package_image"]["name"]);
        
        // Move the uploaded file
        if (move_uploaded_file($_FILES["package_image"]["tmp_name"], $target_file)) {
            $package_image = $target_file;
        }
    }

    // Insert into the database
    $sql = "INSERT INTO vendor_packages (vendor_id, package_name, package_description, package_size, price, package_image) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issids", $vendor_id, $package_name, $package_description, $package_size, $price, $package_image);

    if ($stmt->execute()) {
        $message = "Package added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

header("Location: vendor_package.php"); // Redirect back to the dashboard
exit;
?>