<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: login.php");
    exit;
}

// Set timezone
date_default_timezone_set('Asia/Manila');

// Get current hour for greeting
$currentHour = date('G');
if ($currentHour >= 1 && $currentHour < 4) {
    $greeting = '🌙 Good Evening,';
} elseif ($currentHour < 12) {
    $greeting = '☀️ Good Morning,';
} elseif ($currentHour < 18) {
    $greeting = '🌤️ Good Afternoon,';
} else {
    $greeting = '🌙 Good Evening,';
}


// Handle vendor deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_vendor'])) {
    $vendor_id = $_POST['vendor_id'];

    $sql = "DELETE FROM vendors WHERE vendor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vendor_id);

    if ($stmt->execute()) {
        $message = "Vendor has been deleted successfully.";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Get list of active vendors with category names
$sql = "SELECT v.*, c.category_name AS service_option 
        FROM vendors v
        LEFT JOIN vendor_categories c ON v.category_id = c.category_id
        WHERE v.status = 'Approved'";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Vendors | Vendi</title>
    <link rel="icon" href="/assets/images/VendiBLK_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="NAV_CONTAINER">
    <!-- Navigation Bar -->
    <div class="NAVIGATION_BAR">
        <div class="LOGO">
            <div class="LOGO_NAME">Vendi
                <span>ADMIN</span>
            </div>
        </div>

        <div class="MENU_HEADER">ADMINISTRATION</div>
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="#" class="NAV_ACTIVE"><i class="fas fa-user-tie"></i> Vendors <span id="ITALIC">(Active)</span></a>
        <a href="admin_vendors_approval.php"><i class="fas fa-user-check"></i> Vendors <span id="ITALIC">(Pending)</span></a>
        <a href="admin_vendors_denied.php"><i class="fas fa-user-times"></i> Vendors <span id="ITALIC">(Denied)</span></a>
        <a href="admin_clients.php"><i class="fas fa-users"></i> Clients</a>
        <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
        
        <div class="MENU_HEADER">SETTINGS</div>
        <a href="admin_profile.php"><i class="fa fa-user"></i> Profile</a>            
        <a href="logout.php" class="LOGOUT"><i class="fa fa-sign-out-alt"></i> Log Out</a>
    </div>
    
    <!-- Dashboard Content -->
    <div class="DASHBOARD" id="DASHBOARD">
    <div class="UPPER">
        <div class="LEFT_UPPER">
            <h1 class="DASHBOARD_TITLE">Active Vendors</h1>
            
            <!-- Display Message -->
            <?php if (!empty($message)): ?>
                <div class="ALERT_MESSAGE">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="RIGHT_UPPER">
            <div class="ACCOUNT">
                <a href="profile.php">
                    <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture" class="PROFILE_PIC">
                </a>    
                <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</span>             
            </div>
        </div>
    </div>

        <!-- Vendor Details Table -->
        <div class="BOOKINGS_CONTAINER">
            <header class="BOOKINGS_HEADER">
                <h2>Active Vendors</h2>
            </header>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="BOOKING_TABLE">
                    <table>
                        <thead>
                            <tr>
                                <th>Business Name</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Address</th>
                                <th>Service Type</th>
                                <th>Featured</th>
                                <th>Business Document</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($vendor = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><b><?php echo htmlspecialchars($vendor['business_name']); ?></b></td>
                                    <td><?php echo htmlspecialchars($vendor['email']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['mobile_number']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['address']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['service_option'] ?? 'Not Specified'); ?></td>

                                    <!-- Feature/Unfeature Toggle -->
                                    <td>
                                        <form action="feature_vendor.php" method="POST">
                                            <input type="hidden" name="vendor_id" value="<?php echo $vendor['vendor_id']; ?>">
                                            <button type="submit" class="ACTION_BUTTON">
                                                <?php echo $vendor['is_featured'] ? '⭐ Unfeature' : '🌟 Feature'; ?>
                                            </button>
                                        </form>
                                    </td>

                                    <td class="VIEW_DOCUMENT">
                                        <a href="#IMAGE_VIEW_<?php echo $vendor['vendor_id']; ?>" class="VIEW_BUTTON">
                                            <i class="fas fa-file-alt"></i> View File
                                        </a>
                                        
                                        <div id="IMAGE_VIEW_<?php echo $vendor['vendor_id']; ?>" class="EXPAND">
                                            <a href="#" class="CLOSE_BUTTON">&times;</a>
                                            <img class="EXPANDED_IMAGE" src="<?php echo htmlspecialchars($vendor['business_document']); ?>" alt="Document File">
                                        </div>
                                    </td>

                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="vendor_id" value="<?php echo $vendor['vendor_id']; ?>">
                                            <button type="submit" name="delete_vendor" class="ACTION_BUTTON DELETE_BUTTON" onclick="return confirm('Are you sure you want to delete this vendor?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="BOOKING_TABLE">
                    <p>No active vendors found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
