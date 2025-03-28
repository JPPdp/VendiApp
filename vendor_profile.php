<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
    header("Location: login.php");
    exit();
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

// Fetch vendor data
$vendor_id = $_SESSION['user_id'];
$sql = "SELECT * FROM vendors WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

if ($vendor) {
    $_SESSION['vendor_id'] = $vendor['vendor_id'];
    $_SESSION['profile_picture'] = $vendor['profile_picture'] ?: 'assets/images/default_profile.jpg';
    $_SESSION['business_name'] = $vendor['business_name'];
    $_SESSION['email'] = $vendor['email'];
    $_SESSION['status'] = $vendor['status'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    $profilePic = $_FILES['profile_pic'];
    $profilePicPath = 'uploads/' . basename($profilePic['name']);
    
    if (move_uploaded_file($profilePic['tmp_name'], $profilePicPath)) {
        $sql = "UPDATE vendors SET profile_picture = ? WHERE vendor_id = ?";
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Profile | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="profile.css?v=<?php echo time(); ?>">
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
            <div class="MENU_HEADER">VENDOR PORTAL</div>
            <a href="vendor_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
            <?php if ($vendor['status'] == "Approved"): ?>
                <a href="vendor_packages.php"><i class="fas fa-box-open"></i> My Packages</a>
                <a href="vendor_bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
            <?php endif; ?>
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="vendor_profile.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-user"></i> <span> Profile</span></a>
            <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Vendor Profile</h1>
                </div>
                <div class="RIGHT_UPPER">
                    <div class="ACCOUNT">
                        <div class="GREETING"><?php echo $greeting; ?></div>
                        <a href="vendor_profile.php">
                            <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['business_name']); ?>!</span>             
                    </div>
                </div>
            </div>

            <!-- Profile Container -->
            <div class="PROFILE_CONTAINER">
                <!-- Left Profile Section -->
                <div class="LEFT_PROFILE">
                    <div class="PROFILE_PIC_CONTAINER">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture" class="PROFILE_PIC2">
                        <div class="EDIT_ICON_CONTAINER" title="Change Profile Picture">
                            <form id="PROFILE_PIC_FORM" method="post" enctype="multipart/form-data">
                                <label for="VENDOR_PROFILE_PIC" class="EDIT_ICON_LABEL">
                                    <i class="EDIT_ICON fas fa-camera" aria-hidden="true"></i>
                                    <input type="file" id="VENDOR_PROFILE_PIC" name="profile_pic" accept="image/*" style="display: none;" onchange="document.getElementById('PROFILE_PIC_FORM').submit();">
                                </label>
                            </form>
                        </div>
                    </div>
                    <h2 id="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['business_name']); ?></h2>
                    <p class="USER_ID">ID: <?php echo htmlspecialchars($_SESSION['vendor_id']); ?></p>
                    <p class="STATUS <?php echo strtolower($_SESSION['status']); ?>"><?php echo htmlspecialchars($_SESSION['status']); ?></p>
                </div>

                <!-- Right Profile Section -->
                <div class="RIGHT_PROFILE">
                    <div class="PROF_CONTAINER">
                        <div class="PROF_HEADER">
                            <h2>Business Details</h2>
                        </div>
                    </div>    
                    <div class="STACK">
                        <h3>Business Information</h3>
                        <label>Business Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($vendor['business_name']); ?>" readonly>

                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($vendor['email']); ?>" readonly>
                        
                        <label>Service Type</label>
                        <input type="text" value="<?php echo htmlspecialchars($vendor['service_option']); ?>" readonly>

                        <label>Mobile Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($vendor['mobile_number']); ?>" readonly>

                        <label>Business Address</label>
                        <textarea readonly><?php echo htmlspecialchars($vendor['address']); ?></textarea>

                        <label>Vendor ID</label>
                        <input type="text" value="<?php echo htmlspecialchars($vendor['vendor_id']); ?>" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>