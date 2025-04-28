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

$vendor_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $package_id = $_GET['id'];

    // Ensure the package belongs to the vendor
    $sql = "DELETE FROM vendor_packages WHERE package_id = ? AND vendor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $package_id, $vendor_id);

    if ($stmt->execute()) {
        header("Location: vendor_dashboard.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
