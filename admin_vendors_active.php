<?php
include 'db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "admin") {
    header("Location: login.php");
    exit;
}

// Fetch admin data
$sql = "SELECT * FROM admins WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($admin) {
    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['profile_picture'] = $admin['profile_picture'] ?: 'assets/images/default_profile.jpg';
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_email'] = $admin['email'];
}

date_default_timezone_set('Asia/Manila');
// Get current hour for greeting
$currentHour = date('G');

// Determine the greeting based on the time
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_vendor'])) {
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

// Get list of active vendors
$sql = "SELECT * FROM vendors WHERE status = 'Approved'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Vendors | Vendi</title>
    <link rel="icon" href="assets/images/VendiEnhanced.png" type="image/icon type">
    <link rel="stylesheet" href="bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="NAV_CONTAINER">
    <!-- Navigation Bar -->
    <div class="NAVIGATION_BAR">
            <div class="LOGO">
                <img src="assets/images/Vendi_Icon.png" alt="Logo Icon" class="LOGO_ICON">
                <div class="LOGO_NAME">Vendi
                <span>ADMIN</span>
                </div>
            </div>

        <div class="MENU_HEADER">ADMINISTRATION</div>
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href="#" class="NAV_ACTIVE"><i class="fas fa-user-tie"></i> <span>Vendors <span id="ITALIC">(Active)</span></span</a>
        <a href="admin_vendors_approval.php"><i class="fas fa-user-check"></i> Vendors <span id="ITALIC">(Pending)</span></a>
        <a href="admin_vendors_denied.php"><i class="fas fa-user-times"></i> <span>Vendors <span id="ITALIC">(Denied)</span></span></a>
        <a href="admin_clients.php"><i class="fas fa-users"></i> Client Management</a>
        <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
        <div class="MENU_HEADER">SETTINGS</div>
        <a href="admin_profile.php"><i class="fa fa-fw fa-user"></i> <span>Profile</span></a>            
        <label for="LOGOUT_MODAL_TOGGLE" class="LOGOUT">
                <i class="fa fa-fw fa-sign-out-alt"></i> Log Out
        </label>
    </div>
    
    <!-- Dashboard Content -->
    <div class="DASHBOARD" id="DASHBOARD">
        <div class="UPPER">
            <div class="LEFT_UPPER">
                <h1 class="DASHBOARD_TITLE"><i class="fas fa-user-tie"></i> Active Vendors</h1>
            </div>

            <?php if (isset($message)): ?>
                <div class="ALERT_MESSAGE"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="RIGHT_UPPER">
                <div class="ACCOUNT">
                    <span class="GREETING"><?php echo $greeting; ?></span>
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
        </div>
            
                <div class="BOOKING_TABLE">
                <?php if ($result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Business Name</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Address</th>
                                <th>Service Type</th>
                                <th>Features</th>
                                <th>Business Document</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($vendor = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><b><?php echo htmlspecialchars($vendor['business_name']); ?></b></td>
                                    <td><?php echo htmlspecialchars($vendor['email']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['mobile_number']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['address']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['service_option']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['business_description_short']); ?></td>
                                    <td class="VIEW_DOCUMENT">
                                        <a href="#IMAGE_VIEW_<?php echo $vendor['vendor_id']; ?>" class="VIEW_BUTTON" id="VIEW_BUTTON">
                                            <i class="fas fa-file-alt"></i> View File
                                        </a>
                                        
                                        <div id="IMAGE_VIEW_<?php echo $vendor['vendor_id']; ?>" class="EXPAND">
                                            <a href="#" class="CLOSE_BUTTON">&times;</a>
                                            <img class="EXPANDED_IMAGE" src="<?php echo $vendor['business_document']; ?>" alt="Document File">
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
                <table>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="NO_DATA_CELL">
                                        <p>No active vendors found.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<input type="checkbox" id="LOGOUT_MODAL_TOGGLE" class="MODAL_TOGGLE">
    <div class="LOGOUT_MODAL">
        <div class="LOGOUT_MODAL_CONTENT">
            <h3>CONFIRM LOGOUT</h3>
            <p>Are you sure you want to log out?</p>
            <div class="BUTTON_ACTIONS">
                <a href="logout.php" class="BUTTON_CONFIRM">
                    <i class="fas fa-sign-out-alt"></i> LOG OUT
                </a>
                <label for="LOGOUT_MODAL_TOGGLE" class="BUTTON_CANCEL">
                    <i class="fas fa-times"></i> CANCEL
                </label>
            </div>
        </div>
    </div>
    
<script src="dashboard.js"></script>
</body>
</html>