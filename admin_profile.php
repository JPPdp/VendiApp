<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "admin") {
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

// Database connection
$conn = new mysqli("localhost", "root", "", "vendi_services");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile | Vendi</title>
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
                    <span>ADMIN</span>
                </div>
            </div>
            <div class="MENU_HEADER">ADMINISTRATION</div>
            <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
            <a href="admin_vendors_active.php"><i class="fas fa-user-tie"></i> Vendors <span id="ITALIC">(Active)</span></a>
            <a href="admin_vendors_approval.php"><i class="fas fa-user-check"></i> Vendors <span id="ITALIC">(Pending)</span></a>
            <a href="admin_vendors_denied.php"><i class="fas fa-user-times"></i> <span>Vendors <span id="ITALIC">(Denied)</span></span></a>
            <a href="admin_clients.php"><i class="fas fa-users"></i> Clients</a>
            <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="admin_profile.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-user"></i> <span> Profile</span></a>
            <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Admin Profile</h1>
                </div>
                <div class="RIGHT_UPPER">
                    <div class="ACCOUNT">
                        <div class="GREETING"><?php echo $greeting; ?></div>
                        <a href="admin_profile.php">
                            <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</span>             
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
                                <label for="ADMIN_PROFILE_PIC" class="EDIT_ICON_LABEL">
                                    <i class="EDIT_ICON fas fa-camera" aria-hidden="true"></i>
                                    <input type="file" id="ADMIN_PROFILE_PIC" name="profile_pic" accept="image/*" style="display: none;" onchange="document.getElementById('PROFILE_PIC_FORM').submit();">
                                </label>
                            </form>
                        </div>
                    </div>
                    <h2 id="ADMIN_NAME"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></h2>
                    <p class="USER_ID">ID: <?php echo htmlspecialchars($_SESSION['admin_id']); ?></p>
                </div>

                <!-- Right Profile Section -->
                <div class="RIGHT_PROFILE">
                        <div class="PROF_CONTAINER">
                            <div class="PROF_HEADER">
                                    <h2>Account Details</h2>
                            </div>
                        </div>    
                            <div class="STACK">
                                <h3>Admin Information</h3>
                                        <label>Name</label>
                                        <input type="text" value="<?php echo htmlspecialchars($_SESSION['admin_name']); ?>" id="admin_name" readonly>

                                        <label>Email</label>
                                        <input type="email" value="<?php echo htmlspecialchars($_SESSION['admin_email']); ?>" id="admin_email" readonly>
                                        
                                        <label>Admin ID</label>
                                        <input type="text" value="<?php echo htmlspecialchars($_SESSION['admin_id']); ?>" id="admin_id" readonly>
                        </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>