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

if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // Update booking status to Rejected
    $sql = "UPDATE bookings SET status = 'Rejected' WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        header("Location: vendor_bookings.php");
        exit;
    } else {
        echo "Error updating booking: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
