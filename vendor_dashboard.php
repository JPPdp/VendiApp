<?php
include 'db_connect.php';
session_start();

// Timezone and Greeting Setup
date_default_timezone_set('Asia/Manila');
$currentHour = date('H');

if ($currentHour < 12) {
    $greeting = '☀️ Good Morning,';
} elseif ($currentHour < 18) {
    $greeting = '🌤️ Good Afternoon,';
} else {
    $greeting = '🌙 Good Evening,';
}

// Database connection
$conn = new mysqli("localhost", "root", "", "vendi_services");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if vendor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];

// Fetch vendor details
$sql = "SELECT * FROM vendors WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

// Fetch vendor packages if approved
$packages = [];
if ($vendor['status'] == "Approved") {
    $sql = "SELECT * FROM vendor_packages WHERE vendor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $packages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch pending bookings
$sql = "SELECT b.booking_id, b.reference_id, c.name AS client_name, c.email, vp.package_name, 
               b.status, b.service_datetime
        FROM bookings b
        JOIN clients c ON b.client_id = c.client_id
        JOIN vendor_packages vp ON b.package_id = vp.package_id
        WHERE b.vendor_id = ? AND b.status = 'Pending'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$pending_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Count pending bookings
$sql = "SELECT COUNT(*) AS pending_count FROM bookings WHERE vendor_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$pending_summary = $stmt->get_result()->fetch_assoc();
$pending_count = $pending_summary['pending_count'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="profile.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <?php if ($vendor['status'] == "Pending"): ?>
        <p>Your account is awaiting approval from the admin.</p>
    <?php elseif ($vendor['status'] == "Denied"): ?>
        <p>Your registration was denied. Contact admin for more details.</p>
    <?php else: ?>
        
        <!-- Dashboard Summary -->
        <h3>Dashboard Summary</h3>
        <p>Pending Bookings: <strong><?php echo $pending_count; ?></strong></p>

        <!-- Pending Bookings Section -->
        <h3>Pending Bookings</h3>
        <?php if (!empty($pending_bookings)): ?>
            <table border="1">
                <tr>
                    <th>Reference ID</th>
                    <th>Email</th>
                    <th>Schedule On</th>
                    <th>Client Name</th>
                    <th>Package</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($pending_bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['reference_id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['email']); ?></td>
                        <td><?php echo date("F j, Y g:i A", strtotime($booking['service_datetime'])); ?></td>
                        <td><?php echo htmlspecialchars($booking['client_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['status']); ?></td>
                        <td>
                            <form action="update_booking_status.php" method="POST">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                <button type="submit" name="action" value="approve">✅ Approve</button>
                                <button type="submit" name="action" value="deny">❌ Deny</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No pending bookings.</p>
        <?php endif; ?>

        <!-- Vendor Packages -->
        <h3>Your Packages</h3>
        <?php if (!empty($packages)): ?>
            <table border="1">
                <tr>
                    <th>Package Name</th>
                    <th>Size</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($packages as $package): ?>
                    <tr>
                        <td><?php echo $package['package_name']; ?></td>
                        <td><?php echo $package['package_size']; ?> people</td>
                        <td>$<?php echo $package['price']; ?></td>
                        <td>
                            <a href="edit_package.php?id=<?php echo $package['package_id']; ?>">Edit</a> | 
                            <a href="delete_package.php?id=<?php echo $package['package_id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this package?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No packages added yet.</p>
        <?php endif; ?>

        <!-- Add New Package -->
        <h3>Add New Package</h3>
        <form action="add_package.php" method="POST">
            <label>Package Name:</label>
            <input type="text" name="package_name" required><br>

            <label>Package Size (people):</label>
            <input type="number" name="package_size" required><br>

            <label>Price ($):</label>
            <input type="number" step="0.01" name="price" required><br>

            <input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>">
            <button type="submit">Add Package</button>
        </form>
    <?php endif; ?>

    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
