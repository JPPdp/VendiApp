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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    $profilePic = $_FILES['profile_pic'];
    $profilePicPath = 'uploads/' . basename($profilePic['name']);
    
    if (move_uploaded_file($profilePic['tmp_name'], $profilePicPath)) {
        $sql = "UPDATE admins SET profile_picture = ? WHERE admin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $profilePicPath, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $_SESSION['profile_picture'] = $profilePicPath;
        } else {
            echo "Error updating profile picture: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Error uploading profile picture.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($vendor['business_name']); ?> Profile | Vendi</title>
    <link rel="icon" href="assets/images/VendiEnhanced.png" type="image/icon type">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="profile.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="NAV_CONTAINER">
        <!-- Navigation Bar -->
        <div class="NAVIGATION_BAR">
            <div class="LOGO">
                <img src="assets/images/Vendi_Icon.png" alt="Logo Icon" class="LOGO_ICON">
                <div class="LOGO_NAME">Vendi
                <span id="VENDORS">VENDORS</span>
                </div>
            </div>
            
            <div class="MENU_HEADER">MANAGEMENT</div>
            <a href="vendor_dashboard.php"><i class="fas fa-stream"></i> Dashboard</a>
            
            <!-- Bookings Dropdown -->
            <div class="NAV_DROPDOWN">
                <a class="NAV_DROPDOWN_TOGGLE" href="#">
                    <i class="fa fa-fw fa-calendar"></i> Bookings <i class="fas fa-chevron-down NAV_DROPDOWN_ICON"></i>
                </a>
                <div class="NAV_DROPDOWN_CONTENT">
                    <a href="vendor_bookings_approval.php"><i class="fas fa-calendar-alt"></i> <span id="ITALIC">Pending Bookings</span></a>
                    <a href="vendor_bookings_active.php"><i class="far fa-calendar-check"></i> <span id="ITALIC">Scheduled Bookings</span></a>
                    <a href="vendor_bookings_completed.php"><i class="fas fa-calendar-check"></i> <span id="ITALIC">Completed Bookings</span></a>
                    <a href="vendor_bookings_cancelled.php"><i class="fas fa-calendar-times"></i> <span id="ITALIC">Cancelled Bookings</span></a>
                </div>
            </div>
            
            <a href="vendor_package.php"><i class="fa fa-fw fa-store"></i> Packages</a>
            
            <a href="vendor_clients.php"><i class="fas fa-users"></i> Clients</a>
            
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="vendor_profile.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-user"></i> <span>Profile</span></a>
            <a href="vendor_help.php"><i class="fas fa-question-circle"></i> Help</a>
            <label for="LOGOUT_MODAL_TOGGLE" class="LOGOUT">
                <i class="fa fa-fw fa-sign-out-alt"></i> Log Out
            </label>        
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE"><i class="fas fa-user"></i> <?php echo htmlspecialchars($vendor['business_name']); ?>'s Profile</h1>
                </div>
                <div class="RIGHT_UPPER">
                    <div class="ACCOUNT">
                        <div class="GREETING"><?php echo $greeting; ?></div>
                        <a href="vendor_profile.php">
                            <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($vendor['business_name']); ?>!</span>             
                    </div>
                </div>
            </div>

            <!-- Profile Container -->
            <div class="PROFILE_CONTAINER">
                <!-- Left Profile Section -->
<div class="LEFT_PROFILE">
    <div class="PROFILE_PIC_CONTAINER">
        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="" class="PROFILE_PIC_BUSINESS">
        <div class="EDIT_ICON_CONTAINER" title="Change Profile Picture">
            <form id="PROFILE_PIC_FORM" method="post" enctype="multipart/form-data">
                <label for="VENDOR_PROFILE_PIC" class="EDIT_ICON_LABEL">
                    <i class="EDIT_ICON_BUSINESS fas fa-camera" aria-hidden="true"></i>
                    <input type="file" id="VENDOR_PROFILE_PIC" name="profile_pic" accept="image/*" style="display: none;" onchange="document.getElementById('PROFILE_PIC_FORM').submit();">
                </label>
            </form>
        </div>
    </div>
    <h2 id="BUSINESS_NAME"><?php echo htmlspecialchars($vendor['business_name']); ?></h2>
    
    <!-- Add this rating display section -->
    <div class="VENDOR_RATING">
        <?php
        $rating = $vendor['rating'];
        $fullStars = floor($rating);
        $hasHalfStar = ($rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
        ?>
        
        <div class="STARS">
            <?php for ($i = 0; $i < $fullStars; $i++): ?>
                <i class="fas fa-star"></i>
            <?php endfor; ?>
            
            <?php if ($hasHalfStar): ?>
                <i class="fas fa-star-half-alt"></i>
            <?php endif; ?>
            
            <?php for ($i = 0; $i < $emptyStars; $i++): ?>
                <i class="far fa-star"></i>
            <?php endfor; ?>
        </div>
        
        <span class="RATING_VALUE"><?php echo number_format($rating, 1); ?>/5.0</span>
    </div>
    
    <p class="USER_ID">ID: <?php echo htmlspecialchars($vendor['vendor_id']); ?></p>
</div>

                <!-- Right Profile Section -->
                <div class="RIGHT_PROFILE"> 
                    <div class="STACK3" id="FIRST_STACK">
                        <h3>Account Information</h3>

                        <div class="BESIDE_FIELDS">
                            <div class="BESIDE_FIELD">
                            <label>Business Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($vendor['business_name']); ?>" id="business_name" readonly>
                            </div>

                            <div class="BESIDE_FIELD">
                            <label>Email</label>
                            <input type="email" value="<?php echo htmlspecialchars($vendor['email']); ?>" id="business_email" readonly>
                            </div>
                        </div>

                        <div class="BESIDE_FIELDS">
                            <div class="BESIDE_FIELD">
                            <label>Mobile Number</label>
                            <input type="text" value="<?php echo htmlspecialchars($vendor['mobile_number']); ?>" id="business_mobile" readonly>
                            </div>

                            <div class="BESIDE_FIELD">
                            <label>Business Address</label>
                            <input type="text" id="business_address" readonly value="<?php echo htmlspecialchars($vendor['address']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="STACK3">
                        <h3>Business Information</h3>

                        <div class="BESIDE_FIELDS">
                            <div class="BESIDE_FIELD">
                            <label>Service Type</label>
                            <input type="text" value="<?php echo htmlspecialchars($vendor['service_option']); ?>" id="business_service" readonly>

                            <label>Business Features</label>
                            <input type="text" value="<?php echo htmlspecialchars($vendor['business_description_short']); ?>" id="business_service" readonly>

                            <label>Business Documents</label>
                                <a href="#IMAGE_VIEW_<?php echo $vendor['vendor_id']; ?>" class="VIEW_BUTTON" id="VIEW_BUTTON">
                                    <i class="fas fa-file-alt"></i> View File
                                </a>
                                <!-- Modal to display the image -->
                                <div id="IMAGE_VIEW_<?php echo $vendor['vendor_id']; ?>" class="EXPAND">
                                    <a href="#" class="CLOSE_BUTTON">&times;</a>
                                    <img class="EXPANDED_IMAGE" src="<?php echo $vendor['business_document']; ?>" alt="">
                                </div>

                            </div>

                            <div class="BESIDE_FIELD">
                            <label>Business Description Summary</label>
                            <textarea id="VENDOR_DESCRIPTION" placeholder="<?php echo htmlspecialchars($vendor['business_description_long']); ?>"></textarea>
                            </div>
                        </div>
                    </div>

                        <div class="STACK3">
                        <h3>Account Management</h3>
                            <div class="BESIDE_FIELDS">
                                <div class="BESIDE_FIELD">
                                    <button id="CHANGE_PASSWORD" class="ACCOUNT_MANAGE" onclick="window.location.href='change_password.php'">
                                        <i class="fas fa-key"></i> Change Password
                                    </button>
                                </div>
                                <div class="BESIDE_FIELD">
                                    <label for="DELETE_MODAL_TOGGLE" class="ACCOUNT_MANAGE" id="DELETE_ACCOUNT">
                                        <i class="fas fa-trash-alt"></i> Delete Account
                                    </label>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal Structure -->
    <input type="checkbox" id="LOGOUT_MODAL_TOGGLE" class="MODAL_TOGGLE">
    <div id="LOGOUT_MODAL_WRAPPER">
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
    </div>

    <!-- Delete Account Modal Structure -->
    <input type="checkbox" id="DELETE_MODAL_TOGGLE" class="MODAL_TOGGLE">
    <div id="DELETE_MODAL_WRAPPER">
        <div class="DELETE_MODAL">
            <div class="DELETE_MODAL_CONTENT">
                <h3>DELETE ACCOUNT</h3>
                <p>Are you sure you want to delete your account?</p>
                <div class="DELETE_BUTTON_ACTIONS">
                    <a href="delete_account.php" class="BUTTON_DELETE">
                        <i class="fas fa-trash-alt"></i> DELETE ACCOUNT
                    </a>
                    <label for="DELETE_MODAL_TOGGLE" class="BUTTON_CANCEL">
                        <i class="fas fa-times"></i> CANCEL
                    </label>
                </div>
            </div>
        </div>
    </div>

    
</body>
</html>
