<?php
session_start();
include 'db_connect.php';

// Ensure Admin is Logged In
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: login.php");
    exit;
}

// Check if review_id is provided
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['review_id'])) {
    $review_id = $_POST['review_id'];

    // Fetch review details before deleting
    $stmt = $conn->prepare("
        SELECT r.review, c.name AS client_name, v.business_name 
        FROM vendor_reviews r
        JOIN clients c ON r.client_id = c.client_id
        JOIN vendors v ON r.vendor_id = v.vendor_id
        WHERE r.review_id = ?");
    $stmt->bind_param("i", $review_id);
    $stmt->execute();
    $review = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($review) {
        // Delete review
        $stmt = $conn->prepare("DELETE FROM vendor_reviews WHERE review_id = ?");
        $stmt->bind_param("i", $review_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Review deleted successfully!";

            // Insert notification for admin
            $admin_id = $_SESSION['user_id']; // Logged-in admin
            $message = "Review from {$review['client_name']} for {$review['business_name']} was deleted.";

            $notif_stmt = $conn->prepare("INSERT INTO notifications (admin_id, message) VALUES (?, ?)");
            $notif_stmt->bind_param("is", $admin_id, $message);
            $notif_stmt->execute();
            $notif_stmt->close();
        } else {
            $_SESSION['error_message'] = "Failed to delete review!";
        }
        $stmt->close();
    }
}

$conn->close();
header("Location: admin_reviews.php");
exit;
?>
