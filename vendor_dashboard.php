<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


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

// Initialize Stats (Prevents Undefined Variable Errors)
$total_bookings = 0;
$total_revenue = 0.00;
$most_booked_package = "No bookings yet";
$monthly_bookings = array_fill(1, 12, 0); // Initialize monthly bookings array

// Fetch Vendor Details
$stmt = $conn->prepare("SELECT * FROM vendors WHERE vendor_id = ?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$vendor = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch Total Bookings, Revenue & Most Booked Package
$sql = "
    SELECT 
        COUNT(b.booking_id) AS total_bookings,
        SUM(CASE WHEN b.status = 'Completed' THEN vp.price ELSE 0 END) AS total_revenue
    FROM bookings b
    JOIN vendor_packages vp ON b.package_id = vp.package_id
    WHERE b.vendor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$total_bookings = $result['total_bookings'] ?? 0;
$total_revenue = $result['total_revenue'] ?? 0.00;
$stmt->close();

// Fetch Most Booked Package
$sql = "SELECT vp.package_name, COUNT(b.package_id) AS count 
        FROM bookings b
        JOIN vendor_packages vp ON b.package_id = vp.package_id
        WHERE b.vendor_id = ?
        GROUP BY b.package_id
        ORDER BY count DESC
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$most_booked = $stmt->get_result()->fetch_assoc();
$most_booked_package = $most_booked['package_name'] ?? "No bookings yet";
$stmt->close();

// Fetch Monthly Booking Trends
$sql = "SELECT MONTH(b.service_datetime) AS month, COUNT(*) AS count 
        FROM bookings b
        WHERE b.vendor_id = ? AND YEAR(b.service_datetime) = YEAR(CURDATE())
        GROUP BY MONTH(b.service_datetime)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $monthly_bookings[(int)$row['month']] = $row['count'];
}

// Fetch Pending & Active Bookings
$sql = "SELECT b.booking_id, b.reference_id, c.name AS client_name, c.email, vp.package_name, 
               b.status, b.service_datetime
        FROM bookings b
        JOIN clients c ON b.client_id = c.client_id
        JOIN vendor_packages vp ON b.package_id = vp.package_id
        WHERE b.vendor_id = ? AND b.status IN ('Pending', 'Approved')
        ORDER BY b.service_datetime ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$pending_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// Fetch active bookings (Approved but not yet completed)
$sql = "SELECT b.booking_id, b.reference_id, c.name AS client_name, c.email, vp.package_name, 
               b.status, b.service_datetime
        FROM bookings b
        JOIN clients c ON b.client_id = c.client_id
        JOIN vendor_packages vp ON b.package_id = vp.package_id
        WHERE b.vendor_id = ? AND b.status = 'Approved'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$active_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch Booking History (Completed & Canceled)
$sql = "SELECT b.booking_id, b.reference_id, c.name AS client_name, c.email, vp.package_name, 
               b.status, b.service_datetime
        FROM bookings b
        JOIN clients c ON b.client_id = c.client_id
        JOIN vendor_packages vp ON b.package_id = vp.package_id
        WHERE b.vendor_id = ? AND (b.status = 'Completed' OR b.status = 'Canceled')
        ORDER BY b.service_datetime DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$booking_history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Count pending bookings
$sql = "SELECT COUNT(*) AS pending_count FROM bookings WHERE vendor_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$pending_summary = $stmt->get_result()->fetch_assoc();
$pending_count = $pending_summary['pending_count'];

// Fetch Booking History (Approved & Completed)
$sql = "SELECT b.booking_id, b.reference_id, c.name AS client_name, c.email, vp.package_name, 
               b.status, b.service_datetime
        FROM bookings b
        JOIN clients c ON b.client_id = c.client_id
        JOIN vendor_packages vp ON b.package_id = vp.package_id
        WHERE b.vendor_id = ? AND (b.status = 'Approved' OR b.status = 'Completed')
        ORDER BY b.service_datetime DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$booking_history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);



// Retrieve filter values
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Build SQL query dynamically
$sql = "SELECT b.booking_id, b.reference_id, c.name AS client_name, c.email, vp.package_name, 
               b.status, b.service_datetime
        FROM bookings b
        JOIN clients c ON b.client_id = c.client_id
        JOIN vendor_packages vp ON b.package_id = vp.package_id
        WHERE b.vendor_id = ?";

$params = [$vendor_id];
$types = "i";

// Apply filters
if (!empty($search)) {
    $sql .= " AND (c.name LIKE ? OR b.reference_id LIKE ?)";
    $params[] = "%" . $search . "%";
    $params[] = "%" . $search . "%";
    $types .= "ss";
}

if (!empty($status)) {
    $sql .= " AND b.status = ?";
    $params[] = $status;
    $types .= "s";
}

if (!empty($date)) {
    $sql .= " AND DATE(b.service_datetime) = ?";
    $params[] = $date;
    $types .= "s";
}

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$filtered_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


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
        <h3>📊 Vendor Performance Overview</h3>
<div class="analytics">
    <div class="stat-box">
        <h4>Total Bookings</h4>
        <p><?php echo $total_bookings; ?></p>
    </div>
    <div class="stat-box">
        <h4>Total Revenue</h4>
        <p>₱<?php echo number_format($total_revenue, 2); ?></p>
    </div>
    <div class="stat-box">
        <h4>Most Booked Package</h4>
        <p><?php echo htmlspecialchars($most_booked_package); ?></p>
    </div>
</div>

<h3>📈 Monthly Booking Trends (This Year)</h3>
<table border="1">
    <tr>
        <th>Month</th>
        <th>Total Bookings</th>
    </tr>
    <?php foreach ($monthly_bookings as $month => $total): ?>
        <tr>
            <td><?php echo date("F", mktime(0, 0, 0, $month, 1)); ?></td>
            <td><?php echo $total; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Booking Filters -->
<h3>Filter & Search Bookings</h3>
<form method="GET">
    <input type="text" name="search" placeholder="Search by Client Name or Reference ID"
           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <select name="status">
        <option value="">All Status</option>
        <option value="Pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
        <option value="Approved" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
        <option value="Completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
        <option value="Canceled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
    </select>
    <input type="date" name="date" value="<?php echo isset($_GET['date']) ? $_GET['date'] : ''; ?>">
    <button type="submit">🔍 Apply Filters</button>
</form>

<!-- Fetch Bookings with Filters -->
<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

$sql = "SELECT b.booking_id, b.reference_id, c.name AS client_name, c.email, vp.package_name, 
               b.status, b.service_datetime
        FROM bookings b
        JOIN clients c ON b.client_id = c.client_id
        JOIN vendor_packages vp ON b.package_id = vp.package_id
        WHERE b.vendor_id = ?";

$params = [$vendor_id];
$types = "i";

if (!empty($search)) {
    $sql .= " AND (c.name LIKE ? OR b.reference_id LIKE ?)";
    $params[] = "%" . $search . "%";
    $params[] = "%" . $search . "%";
    $types .= "ss";
}

if (!empty($status)) {
    $sql .= " AND b.status = ?";
    $params[] = $status;
    $types .= "s";
}

if (!empty($date)) {
    $sql .= " AND DATE(b.service_datetime) = ?";
    $params[] = $date;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$filtered_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!-- Display Filtered Bookings -->
<h3>Filtered Bookings</h3>
<?php if (!empty($filtered_bookings)): ?>
    <table border="1">
        <tr>
            <th>Reference ID</th>
            <th>Email</th>
            <th>Schedule On</th>
            <th>Client Name</th>
            <th>Package</th>
            <th>Status</th>
        </tr>
        <?php foreach ($filtered_bookings as $booking): ?>
            <tr>
                <td><?php echo htmlspecialchars($booking['reference_id']); ?></td>
                <td><?php echo htmlspecialchars($booking['email']); ?></td>
                <td><?php echo date("F j, Y g:i A", strtotime($booking['service_datetime'])); ?></td>
                <td><?php echo htmlspecialchars($booking['client_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['status']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No bookings found.</p>
<?php endif; ?>

        

        
        
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

        <!-- Active Bookings (Approved) -->
        <h3>Active Bookings</h3>
        <?php if (!empty($active_bookings)): ?>
            <table border="1">
                <tr>
                    <th>Reference ID</th>
                    <th>Email</th>
                    <th>Schedule On</th>
                    <th>Client Name</th>
                    <th>Package</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
                <?php foreach ($active_bookings as $booking): ?>
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
                                <button type="submit" name="action" value="complete">✅ Mark as Completed</button>
                                <button type="submit" name="action" value="cancel">🚫 Cancel Booking</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No active bookings.</p>
        <?php endif; ?>

        <!-- Booking History -->
        <h3>Booking History</h3>
        <?php if (!empty($booking_history)): ?>
            <table border="1">
                <tr>
                    <th>Reference ID</th>
                    <th>Email</th>
                    <th>Schedule On</th>
                    <th>Client Name</th>
                    <th>Package</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($booking_history as $history): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($history['reference_id']); ?></td>
                        <td><?php echo htmlspecialchars($history['email']); ?></td>
                        <td><?php echo date("F j, Y g:i A", strtotime($history['service_datetime'])); ?></td>
                        <td><?php echo htmlspecialchars($history['client_name']); ?></td>
                        <td><?php echo htmlspecialchars($history['package_name']); ?></td>
                        <td><?php echo htmlspecialchars($history['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No past bookings found.</p>
        <?php endif; ?>

        <!-- Vendor Packages -->
        <h3>Your Packages</h3>
        <a href="add_package.php">➕ Add Package</a>
        <br><br>

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
                        <td>₱<?php echo number_format($package['price'], 2); ?></td>
                        <td>
                            <a href="edit_package.php?id=<?php echo $package['package_id']; ?>">✏ Edit</a> |
                            <a href="delete_package.php?id=<?php echo $package['package_id']; ?>" onclick="return confirm('Are you sure?');">🗑 Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No packages added yet.</p>
        <?php endif; ?>

    <?php endif; ?>

    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
