<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


// Ensure only admins can access this feature
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: login.php");
    exit;
}

// Check if vendor_id is provided
if (!isset($_POST['vendor_id'])) {
    die("Error: No vendor ID provided.");
}

$vendor_id = $_POST['vendor_id'];

// Fetch the current status of is_featured
$stmt = $conn->prepare("SELECT is_featured FROM vendors WHERE vendor_id = ?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Vendor not found.");
}

$vendor = $result->fetch_assoc();
$current_status = $vendor['is_featured'];

// Toggle the is_featured status (1 → 0 or 0 → 1)
$new_status = $current_status ? 0 : 1;

// Update the database
$stmt = $conn->prepare("UPDATE vendors SET is_featured = ? WHERE vendor_id = ?");
$stmt->bind_param("ii", $new_status, $vendor_id);

if ($stmt->execute()) {
    header("Location: admin_vendors_active.php"); // Redirect back to vendor list
    exit;
} else {
    echo "Error: Could not update vendor status.";
}

// Close connection
$stmt->close();
$conn->close();
?>
