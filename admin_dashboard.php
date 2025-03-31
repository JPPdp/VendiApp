<?php
// Database and Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';

// Authentication Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: login.php");
    exit;
}

// Timezone and Greeting Setup
date_default_timezone_set('Asia/Manila');
$currentHour = date('H');

$greeting = match (true) {
    $currentHour < 12 => '☀️ Good Morning,',
    $currentHour < 18 => '🌤️ Good Afternoon,',
    default => '🌙 Good Evening,'
};

// Fetch Admin Data
$admin = [];
if ($stmt = $conn->prepare("SELECT * FROM admins WHERE admin_id = ?")) {
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();
}

// Initialize Dashboard Statistics
$stats = [
    'pending_vendors' => 0,
    'active_vendors' => 0,
    'total_vendors' => 0,
    'total_clients' => 0
];

// Fetch Vendor Statistics
$sql = "
    SELECT 
        COUNT(CASE WHEN v.status = 'Pending' THEN 1 END) AS pending_vendors,
        COUNT(CASE WHEN v.status = 'Approved' THEN 1 END) AS active_vendors,
        COUNT(*) AS total_vendors
    FROM vendors v";
if ($result = $conn->query($sql)) {
    $stats = array_merge($stats, $result->fetch_assoc());
    $result->free();
}

// Fetch Total Clients
$sql = "SELECT COUNT(*) AS count FROM clients";
if ($result = $conn->query($sql)) {
    $stats['total_clients'] = $result->fetch_assoc()['count'];
    $result->free();
}

// Fetch Recent Clients (Limit: 5)
$recent_clients = $conn->query("SELECT * FROM clients ORDER BY client_id DESC LIMIT 5");

// Fetch Vendor Status Breakdown
$status_counts = ['Pending' => 0, 'Approved' => 0, 'Denied' => 0];
$sql = "SELECT v.status, COUNT(*) AS count FROM vendors v GROUP BY v.status";
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $status_counts[$row['status']] = (int)$row['count'];
    }
    $result->free();
}

$total_vendors = array_sum($status_counts);

// Fetch Recent Vendors (With Category & Approval Details) – Limit: 5
$active_vendors = $conn->query("
    SELECT v.*, 
           vc.category_name, 
           va.approval_date, 
           a.name AS approved_by,
           v.status AS approval_status,
           va.action AS admin_decision
    FROM vendors v
    JOIN vendor_categories vc ON v.category_id = vc.category_id
    LEFT JOIN vendor_approvals va ON v.vendor_id = va.vendor_id
    LEFT JOIN admins a ON va.admin_id = a.admin_id
    WHERE v.status IN ('Approved', 'Pending')
    ORDER BY v.vendor_id DESC 
    LIMIT 5");

// Fetch Vendor Reviews for Admin to Manage
$reviews = $conn->query("
    SELECT r.review_id, c.name AS client_name, v.business_name AS vendor_name, 
           r.rating, r.review, r.created_at
    FROM vendor_reviews r
    JOIN clients c ON r.client_id = c.client_id
    JOIN vendors v ON r.vendor_id = v.vendor_id
    ORDER BY r.	created_at DESC
");

// Fetch notifications for the logged-in admin
$admin_id = $_SESSION['user_id'];
$notifications = $conn->query("
    SELECT * FROM notifications 
    WHERE admin_id = $admin_id 
    ORDER BY created_at DESC 
    LIMIT 10");




// Close database connection
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="notifications.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <div class="NAV_CONTAINER">
    <div class="NAVIGATION_BAR">
        <div class="LOGO">
            <div class="LOGO_NAME">Vendi <span>ADMIN</span></div>
        </div>
        <div class="MENU_HEADER">ADMINISTRATION</div>
        <a href="#DASHBOARD" class="NAV_ACTIVE"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
        <a href="admin_vendors_active.php"><i class="fas fa-user-tie"></i> Vendors <span id="ITALIC">(Active)</span></a>
        <a href="admin_vendors_approval.php"><i class="fas fa-user-check"></i> Vendors <span id="ITALIC">(Pending)</span></a>
        <a href="admin_vendors_denied.php"><i class="fas fa-user-times"></i> <span>Vendors <span id="ITALIC">(Denied)</span></span></a>
        <a href="admin_clients.php"><i class="fas fa-users"></i> Clients</a>
        <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
        <div class="MENU_HEADER">SETTINGS</div>
        <a href="admin_profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
        <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
    </div>


    <div class="DASHBOARD" id="DASHBOARD">
        <div class="UPPER">
            <div class="LEFT_UPPER">
                <h1 class="DASHBOARD_TITLE">Admin Dashboard</h1>
            </div>
            <div class="RIGHT_UPPER">
                <div class="ACCOUNT">
                    <span class="HELLO"><?php echo $greeting; ?></span>
                    <a href="admin_profile.php">
                        <img src="<?php echo htmlspecialchars($admin['profile_picture'] ?? 'assets/images/default_profile.jpg'); ?>" alt="Profile Picture" class="PROFILE_PIC">
                    </a>    
                    <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>!</span>             
                </div>
            </div>
        </div>

        <div class="CONTENT">
            <div class="BOX PENDING">
                <h3><i class="fas fa-user-clock"></i> Pending Vendors</h3>
                <p><?php echo (int) $stats['pending_vendors']; ?></p>
            </div>
            <div class="BOX SCHEDULED">
                <h3><i class="fas fa-user-tie"></i> Approved Vendors</h3>
                <p><?php echo (int) $stats['active_vendors']; ?></p>
            </div>
            <div class="BOX COMPLETED">
                <h3><i class="fas fa-user-tie"></i> Total Vendors</h3>
                <p><?php echo (int) $stats['total_vendors']; ?></p>
            </div>
            <div class="BOX CANCELLED">
                <h3><i class="fas fa-users"></i> Total Clients</h3>
                <p><?php echo (int) $stats['total_clients']; ?></p>
            </div>
        </div>

        <table border="1">
    <thead>
        <tr>
            <th>Client Name</th>
            <th>Vendor Name</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Review Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
    
        while ($row = $reviews->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['client_name']); ?></td>
                <td><?= htmlspecialchars($row['vendor_name']); ?></td>
                <td><?= str_repeat("⭐", (int) $row['rating']); ?></td>
                <td><?= nl2br(htmlspecialchars($row['review'])); ?></td>
                <td><?= htmlspecialchars($row['created_at']); ?></td>
                <td>
                    <form action="delete_review.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');">
                        <input type="hidden" name="review_id" value="<?= (int) $row['review_id']; ?>">
                        <button type="submit" style="color: red;">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<h2>🔔 Admin Notifications</h2>
<ul>
    <?php while ($notif = $notifications->fetch_assoc()) { ?>
        <li>
            <?= htmlspecialchars($notif['message']); ?> 
            <small>(<?= $notif['created_at']; ?>)</small>
            <form action="mark_as_read.php" method="POST" style="display:inline;">
                <input type="hidden" name="notif_id" value="<?= $notif['notification_id']; ?>">
                <button type="submit">Mark as Read</button>
            </form>
        </li>
    <?php } ?>
</ul>

        <div class="MAIN_CONTAINER">
            <div class="LEFT_MAIN">
                <div class="PACKAGE_OVERVIEW">
                    <h2>Recent Clients 
                        <a href="admin_clients.php"><i class="DIRECT fas fa-angle-right"></i></a>
                    </h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($client = $recent_clients->fetch_assoc()): ?>
                                <tr>
                                    <td><img src="<?php echo htmlspecialchars($client['profile_picture']); ?>" alt="Client Profile" class="CLIENT_PROFILE_PIC"></td>
                                    <td><?php echo htmlspecialchars($client['name']); ?></td>
                                    <td><?php echo htmlspecialchars($client['email']); ?></td>
                                    <td><?php echo htmlspecialchars($client['mobile_number']); ?></td>
                                    <td>
                                        <button class="DELETE_CLIENT" data-id="<?php echo (int) $client['client_id']; ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>   
                    </table>  
                </div>

                <div class="BOOKING_TABLE">
                    <h2>Vendors Summary<a href="admin_vendors_approval.php"><i class="DIRECT fas fa-angle-right"></i></a></h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Business</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Category</th>
                                <th>Approved By</th>
                                <th>Approval Date</th>
                                <th>Status</th>
                                <th>Feature</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($vendor = $active_vendors->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($vendor['business_name']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['email']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['mobile_number']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['address']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['category_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['approved_by'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['approval_date'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower(htmlspecialchars($vendor['status'])); ?>">
                                            <?php echo htmlspecialchars($vendor['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form action="feature_vendor.php" method="POST">
                                            <input type="hidden" name="vendor_id" value="<?php echo (int) $vendor['vendor_id']; ?>">
                                            <button type="submit" class="FEATURE_VENDOR">
                                                <?php echo $vendor['is_featured'] ? '⭐ Unfeature' : '🌟 Feature'; ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>   
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>
