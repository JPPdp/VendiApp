<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


// Check if vendor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Packages | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="listings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="NAV_CONTAINER">
        <!-- Navigation Bar -->
        <div class="NAVIGATION_BAR">
            <div class="LOGO">
                <div class="LOGO_NAME">Vendi
                    <span>VENDOR</span>
                </div>
            </div>
            
            <div class="MENU_HEADER">ADMINISTRATION</div>
            <a href="vendor_dashboard.php"><i class="fas fa-stream"></i> Dashboard</a>
            
            <!-- Bookings Dropdown -->
            <div class="NAV_DROPDOWN">
                <a class="NAV_DROPDOWN_TOGGLE" href="#">
                    <i class="fa fa-fw fa-calendar"></i> Bookings <i class="fas fa-chevron-down NAV_DROPDOWN_ICON"></i>
                </a>
                <div class="NAV_DROPDOWN_CONTENT">
                    <a href="vendor_bookings_approval.php"><i class="fas fa-calendar-alt"></i> <span id="ITALIC">Pending</span></a>
                    <a href="vendor_bookings_active.php"><i class="far fa-calendar-check"></i> <span id="ITALIC">Scheduled</span></a>
                    <a href="vendor_bookings_completed.php"><i class="fas fa-calendar-check"></i> <span id="ITALIC">Completed</span></a>
                    <a href="vendor_bookings_cancelled.php"><i class="fas fa-calendar-times"></i> <span id="ITALIC">Cancelled</span></a>
                </div>
            </div>
            
            <a href="vendor_package.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-store"></i> <span>Packages</span></a>
            
            <a href="vendor_clients.php"><i class="fas fa-users"></i> Clients</a>
            
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="vendor_profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="vendor_help.php"><i class="fas fa-question-circle"></i> Help</a>
            <a href="logout.php" class="LOGOUT"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD2" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Packages</h1>
                </div>
                <div class="RIGHT_UPPER">
                    <div class="ACCOUNT">
                        <div class="GREETING"><?php echo $greeting; ?></div>
                        <a href="vendor_profile.php">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? $vendor['profile_picture'] ?? 'assets/images/default_profile.jpg'); ?>" alt="" class="PROFILE_PIC">                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($vendor['business_name']); ?>!</span>             
                    </div>
                </div>
            </div>

            <div class="LISTINGS_CONTAINER">
                <header class="LISTINGS_HEADER">
                    <h2>Package Management</h2>
                    <a href="vendor_add_package.php" id="ADD_PACKAGE"><i class="fas fa-plus"></i> Add Package</a>
                </header>
            </div>


                <?php if (!empty($packages)): ?>
                    <?php foreach ($packages as $package): ?>
                        <div class="PACKAGE">
                            <div class="LEFT_PART">
                                <div class="PACKAGE_THUMBNAIL">
                                    <img src="<?php echo htmlspecialchars($package['package_image'] ?: 'assets/images/default_package.jpg'); ?>" alt="Package Thumbnail">
                                </div>
                            </div>
                            <div class="RIGHT_PART">
                                <!-- Package Name and Status -->
                                <div class="PACKAGE_HEADER">
                                    <div class="PACKAGE_NAME">
                                        <h3 id="PACKAGE_NAME"><?php echo htmlspecialchars($package['package_name']); ?></h3>
                                    </div>
                                </div>
                                <!-- Package Description and Features -->
                                    <div class="BESIDE_FIELDS">
                                        <div class="BESIDE_FIELD">
                                            <div class="PACKAGE_DESCRIPTION">
                                                <span id="PACKAGE_DESCRIPTION"><?php echo htmlspecialchars($package['package_description']); ?></span>
                                            </div>
                                        </div>                                        
                                        
                                        <!-- Package Details -->
                                        <div class="BESIDE_FIELD">
                                            <div class="PACKAGE_DETAIL">
                                                <h3 id="label"><i class="fas fa-tag"></i> Price</h3>
                                                <p id="STARTING_PRICE">₱ <?php echo number_format($package['price'], 2); ?></p>
                                            </div>
                                            <div class="PACKAGE_DETAIL">
                                                <h3 id="label"><i class="fas fa-users"></i> Capacity</h3>
                                                <p id="CAPACITY"><?php echo htmlspecialchars($package['package_size']); ?> guests</p>
                                            </div>
                                            <div class="PACKAGE_ACTIONS">
                                                <a href="edit_package.php?id=<?php echo $package['package_id']; ?>" id="EDIT_BUTTON" class="PACK_BTN"><i class="fas fa-edit"></i> Edit</a>
                                                <a href="delete_package.php?id=<?php echo $package['package_id']; ?>" id="DELETE_BUTTON" class="PACK_BTN" onclick="return confirm('Are you sure you want to delete this package?');">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <div class="NO_PACKAGES">
                        <p>No packages added yet. <a href="vendor_add_package.php">Create your first package</a></p>
                    </div>
                <?php endif; ?>
        </div>
    </div>
</body>
</html>