<?php
session_start();

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

// Database connection
$conn = new mysqli("localhost", "root", "", "vendi_services");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$vendor_id = $_SESSION['user_id'];

// Fetch vendor details
$sql = "SELECT * FROM vendors WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['MESSAGE'])) {
    // Get vendor ID
    $vendor_id = $vendor['vendor_id'];
    
    // Assuming admin_id 1 is the default support admin
    $admin_id = 1;
    $message = $_POST['MESSAGE'];
    
    // Insert message into database
    $sql = "INSERT INTO messages (vendor_id, admin_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $vendor_id, $admin_id, $message);
    
    if ($stmt->execute()) {
        $success = "Your message has been sent successfully!";
    } else {
        $error = "Error sending message: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="notifications.css">
    <link rel="stylesheet" href="profile.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="help.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    
    <div class="NAV_CONTAINER">
        <!-- Navigation Bar -->
        <div class="NAVIGATION_BAR">
            <div class="LOGO">
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
            <a href="vendor_profile.php" ><i class="fa fa-fw fa-user"></i> <span>Profile</span></a>
            <a href="vendor_help.php" class="NAV_ACTIVE"><i class="fas fa-question-circle"></i> <span>Help</span></a>
            <a href="logout.php" class="LOGOUT"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>

        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Help</h1>
                </div>
                <div class="RIGHT_UPPER">
                    <div class="ACCOUNT">
                        <span class="HELLO"><?php echo $greeting; ?></span>
                        <a href="profile.php">
                            <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($vendor['business_name']); ?>!</span>             
                    </div>
                </div>
            </div>

            <!-- Help Content -->
            <div class="HELP_CONTAINER">
                <div class="HELP_HEADER">
                    <h2>Submit Your Feedback</h2>
                </div>
            </div>

            <div class="HELP_FORM">
            <h1 class="HELP_TITLE">Need Help?</h1>
            <p class="HELP_SUBTITLE">If you have any questions or concerns, please fill out the form below to contact us. We'll get back to you as soon as possible.</p>

                <form id="HELP_FORM" action="" method="post">

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="BESIDE_FIELDS">
                        <div class="BESIDE_FIELD">
                                <label for="MESSAGE">Message</label>
                                <textarea id="MESSAGE" name="MESSAGE" rows="5" placeholder="Enter your message here..." maxlength="500" required></textarea>
                        </div>
                        <div class="BESIDE_FIELD">
                                <label for="NAME">Business Name</label>
                                <input type="text" id="business_name" name="businessname" value="<?php echo htmlspecialchars($vendor['business_name']); ?>" readonly>

                                <label for="EMAIL">Email</label>
                                <input type="email" id="VENDOR_EMAIL" name="EMAIL" value="<?php echo htmlspecialchars($vendor['email']); ?>" readonly>
                                
                                <div class="HELP_SUBMIT_GROUP">
                                    <button type="reset" id="RESET_PACKAGE" class="SUBMIT_BTN"><i class="fas fa-undo"></i> Reset</button>
                                    <button type="submit" id="SUBMIT_MESSAGE" class="SUBMIT_BTN"><i class="fas fa-envelope"></i> Send Message</button>
                                </div>


                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>