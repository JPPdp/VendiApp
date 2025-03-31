<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $notif_id = $_POST['notif_id'];

    // Delete the notification (or update status if you prefer)
    $stmt = $conn->prepare("DELETE FROM notifications WHERE notification_id = ?");
    $stmt->bind_param("i", $notif_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: admin_dashboard.php");
exit;
?>
