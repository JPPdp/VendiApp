<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];

    // Determine new status
    $new_status = ($action === 'approve') ? 'Approved' : 'Denied';

    // Update booking status in the database
    $sql = "UPDATE bookings SET status = ? WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $booking_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Booking has been {$new_status} successfully.";
    } else {
        $_SESSION['error'] = "Error updating booking status.";
    }

    header("Location: vendor_dashboard.php");
    exit;
}
?>
