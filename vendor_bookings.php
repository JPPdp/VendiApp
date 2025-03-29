<?php
include 'db_connect.php';
session_start();

// Check if vendor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];

// Fetch all bookings for this vendor
$sql = "SELECT b.*, c.name AS client_name, p.package_name 
        FROM bookings b 
        JOIN clients c ON b.client_id = c.client_id 
        JOIN vendor_packages p ON b.package_id = p.package_id 
        WHERE b.vendor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bookings | Vendor</title>
</head>
<body>
    <h2>All Bookings</h2>

    <?php if (!empty($bookings)): ?>
        <table border="1">
            <tr>
                <th>Reference ID</th>
                <th>Client</th>
                <th>Package</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo $booking['reference_id']; ?></td>
                    <td><?php echo $booking['client_name']; ?></td>
                    <td><?php echo $booking['package_name']; ?></td>
                    <td><?php echo $booking['status']; ?></td>
                    <td>
                        <?php if ($booking['status'] == 'Pending'): ?>
                            <a href="approve_booking.php?id=<?php echo $booking['booking_id']; ?>">Approve</a> | 
                            <a href="reject_booking.php?id=<?php echo $booking['booking_id']; ?>">Reject</a>
                        <?php else: ?>
                            <span>No actions available</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>

    <br>
    <a href="vendor_dashboard.php">Back to Dashboard</a>
</body>
</html>
