<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "admin") {
    header("Location: login.php");
    exit;
}

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

// Handle vendor restoration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vendor_id = $_POST['vendor_id'];
    $action = $_POST['action']; // "Restore"

    if ($action == "Restore") {
        $status = "Pending"; // Change status back to Pending for review

        $sql = "UPDATE vendors SET status = ? WHERE vendor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $vendor_id);

        if ($stmt->execute()) {
            $message = "Vendor has been restored successfully and is pending review.";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// Get list of denied vendors
$sql = "SELECT * FROM vendors WHERE status = 'Denied'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denied Vendors | Vendi</title>
    <link rel="icon" href="/assets/images/VendiBLK_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                    <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
                    <a href="admin_vendors_active.php"><i class="fas fa-user-tie"></i> Vendors <span id="ITALIC">(Active)</span></a>
                    <a href="admin_vendors_approval.php"><i class="fas fa-user-check"></i> Vendors <span id="ITALIC">(Pending)</span></a>
                    <a href="#" class="NAV_ACTIVE"><i class="fas fa-user-times"></i> <span>Vendors <span id="ITALIC">(Denied)</span></span></a>
                    <a href="admin_clients.php"><i class="fas fa-users"></i> Clients</a>
                    <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
            <div class="MENU_HEADER">SETTINGS</div>
                    <a href="admin_profile.php"><i class="fa fa-fw fa-user"></i> <span>Profile</span></a>            
                    <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Denied Vendors</h1>
                </div>

                    <?php if (isset($message)): ?>
                        <p><?php echo $message; ?></p>
                    <?php endif; ?>

                <div class="RIGHT_UPPER">
                    <div class="ACCOUNT">
                        <span class="HELLO"><?php echo $greeting; ?></span>
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
                    <h2>Denied Vendors</h2>
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
                                            <!-- Button to open the modal -->
                                            <a href="#IMAGE_VIEW_<?php echo $vendor['vendor_id']; ?>" class="VIEW_BUTTON" id="VIEW_BUTTON">
                                                <i class="fas fa-file-alt"></i> View File
                                            </a>
                                            
                                            <!-- Modal to display the image -->
                                            <div id="IMAGE_VIEW_<?php echo $vendor['vendor_id']; ?>" class="EXPAND">
                                                <a href="#" class="CLOSE_BUTTON">&times;</a>
                                                <img class="EXPANDED_IMAGE" src="<?php echo $vendor['business_document']; ?>" alt="Document File">
                                            </div>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="vendor_id" value="<?php echo $vendor['vendor_id']; ?>">
                                                <button type="submit" name="action" value="Restore" class="ACTION_BUTTON" id="RESTORE_BUTTON">
                                                    <i class="fas fa-undo"></i> Restore
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <table>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="NO_DATA_CELL">
                                        <p>No denied vendors found.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>