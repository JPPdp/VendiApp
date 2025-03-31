<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


// Check if admin is logged in
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

// Handle vendor approval/denial
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vendor_id'], $_POST['action'])) {
    $vendor_id = $_POST['vendor_id'];
    $action = $_POST['action']; // "Approve" or "Deny"

    if ($action === "Approve") {
        $status = "Approved";
        $approval_date = date("Y-m-d H:i:s");
        $admin_id = $_SESSION['user_id'];

        // Update vendor status
        $sql = "UPDATE vendors SET status = ? WHERE vendor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $vendor_id);
        $stmt->execute();

        // Insert approval details into vendor_approvals table
        $sql = "INSERT INTO vendor_approvals (vendor_id, admin_id, approval_date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $vendor_id, $admin_id, $approval_date);
        $stmt->execute();

    } else {
        $status = "Denied";

        // Update vendor status
        $sql = "UPDATE vendors SET status = ? WHERE vendor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $vendor_id);
        $stmt->execute();
    }

    $message = "Vendor has been $status successfully.";
}

// Get list of pending vendors with category name
$sql = "
    SELECT v.*, vc.category_name
    FROM vendors v
    LEFT JOIN vendor_categories vc ON v.category_id = vc.category_id
    WHERE v.status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Management | Vendi</title>
    <link rel="icon" href="/assets/images/VendiBLK_NoBG.png" type="image/png">
    <link rel="stylesheet" href="bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="NAV_CONTAINER">
    <!-- Navigation Bar -->
    <div class="NAVIGATION_BAR">
        <div class="LOGO">
            <div class="LOGO_NAME">Vendi <span>ADMIN</span></div>
        </div>

        <div class="MENU_HEADER">ADMINISTRATION</div>
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="admin_vendors_active.php"><i class="fas fa-user-tie"></i> Vendors <span id="ITALIC">(Active)</span></a>
        <a href="#" class="NAV_ACTIVE"><i class="fas fa-user-check"></i> Vendors <span id="ITALIC">(Pending)</span></a>
        <a href="admin_vendors_denied.php"><i class="fas fa-user-times"></i> Vendors <span id="ITALIC">(Denied)</span></a>
        <a href="admin_clients.php"><i class="fas fa-users"></i> Clients</a>
        <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>

        <div class="MENU_HEADER">SETTINGS</div>
        <a href="admin_profile.php"><i class="fa fa-user"></i> Profile</a>
        <a href="logout.php" class="LOGOUT"><i class="fa fa-sign-out-alt"></i> Log Out</a>
    </div>
    
    <!-- Dashboard Content -->
    <div class="DASHBOARD">
        <div class="UPPER">
            <div class="LEFT_UPPER">
                <h1 class="DASHBOARD_TITLE">Vendor Management</h1>
            </div>

            <?php if (isset($message)): ?>
                <p class="ALERT"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <div class="RIGHT_UPPER">
                <div class="ACCOUNT">
                    <span class="HELLO"><?php echo $greeting; ?></span>
                    <a href="admin_profile.php">
                        <img src="<?php echo htmlspecialchars($admin['profile_picture'] ?? 'assets/images/default_profile.jpg'); ?>" 
                             alt="Profile Picture" class="PROFILE_PIC">
                    </a>    
                    <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</span>             
                </div>
            </div>
        </div>
        
        <!-- Vendor Details Table -->
        <div class="BOOKINGS_CONTAINER">
            <?php if ($result->num_rows > 0): ?>
                <header class="BOOKINGS_HEADER">
                    <h2>Pending Vendors</h2>
                </header>

                <div class="BOOKING_TABLE">
                    <table>
                        <thead>
                            <tr>
                                <th>Business Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
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
                                    <td><?php echo htmlspecialchars($vendor['category_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['business_description_short']); ?></td>
                                    <td class="VIEW_DOCUMENT">
                                        <!-- Button to open modal -->
                                        <a href="#IMAGE_VIEW_<?php echo $vendor['vendor_id']; ?>" class="VIEW_BUTTON">
                                            <i class="fas fa-file-alt"></i> View File
                                        </a>
                                        
                                        <!-- Modal for Document -->
                                        <div id="IMAGE_VIEW_<?php echo $vendor['vendor_id']; ?>" class="EXPAND">
                                            <a href="#" class="CLOSE_BUTTON">&times;</a>
                                            <img class="EXPANDED_IMAGE" src="<?php echo htmlspecialchars($vendor['business_document']); ?>" alt="Document File">
                                        </div>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="vendor_id" value="<?php echo $vendor['vendor_id']; ?>">
                                            <button type="submit" name="action" value="Approve" class="ACTION_BUTTON" id="APPROVE_BUTTON">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="vendor_id" value="<?php echo $vendor['vendor_id']; ?>">
                                            <button type="submit" name="action" value="Deny" class="ACTION_BUTTON" id="DENY_BUTTON">
                                                <i class="fas fa-times"></i>
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
                    <h2>Pending Vendors</h2>
                    <p>No pending vendors.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Close Modal on Click
    document.querySelectorAll(".CLOSE_BUTTON").forEach(button => {
        button.addEventListener("click", function(event) {
            event.preventDefault();
            this.closest(".EXPAND").style.display = "none";
        });
    });

    // Open Modal
    document.querySelectorAll(".VIEW_BUTTON").forEach(button => {
        button.addEventListener("click", function(event) {
            event.preventDefault();
            document.querySelector(this.getAttribute("href")).style.display = "block";
        });
    });
</script>

</body>
</html>
