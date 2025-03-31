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
    <link rel="stylesheet" href="listings_add_package.css?v=<?php echo time(); ?>">
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

            <!-- Add Package Form -->
            <div class="LISTINGS_CONTAINER">
                <header class="LISTINGS_HEADER">
                    <h2>Add New Package</h2>
                    <a href="vendor_package.php" id="ADD_PACKAGE"><i class="fas fa-arrow-left"></i> Go Back</a>
                </header>
            </div>

            <div class="MAIN_CONTAINER">

                        <div class="RIGHT_MAIN">
                    <form class="PACKAGE_FORM" action="add_package.php" method="POST" enctype="multipart/form-data" >

                                <!-- Package Thumbnail -->
                                <div class="FORM_GROUP">
                                    <label for="PACKAGE_THUMBNAIL"><i class="fas fa-image"></i> Package Image</label>
                                    <div class="THUMBNAIL_PREVIEW_CONTAINER">
                                        <img id="thumbnailPreview" src="#" alt="Preview">
                                        <div class="PLACEHOLDER_TEXT">
                                            <i class="fas fa-image"></i>
                                            <span>Package Image</span>
                                        </div>
                                    </div>
                                    <div class="FILE_INPUT_CONTAINER">
                                        <input type="file" id="PACKAGE_THUMBNAIL" name="PACKAGE_THUMBNAIL" accept="image/*" required>
                                    </div>
                                </div>
                        </div>

                        <div class="LEFT_MAIN">
                                    <!-- Package Name -->
                                    <div class="FORM_GROUP">
                                        <label for="PACKAGE_NAME"><i class="fas fa-box"></i> Package Name</label>
                                        <input type="text" id="PACKAGE_NAME" name="package_name" placeholder="Enter package name" required>
                                    </div>

                                    <div class="BESIDE_FIELDS">
                                        <div class="BESIDE_FIELD">
                                            <!-- Package Description -->
                                            <div class="FORM_GROUP" id="DESC">
                                                <label for="PACKAGE_DESCRIPTION"><i class="fas fa-info-circle"></i> Description</label>
                                                <textarea id="PACKAGE_DESCRIPTION" name="PACKAGE_DESCRIPTION" maxlength="400" placeholder="Enter package description" required></textarea>
                                            </div>
                                        </div>

                                        <div class="BESIDE_FIELD">
                                                <!-- Starting Price -->
                                            <div class="FORM_GROUP" id="PRAYS">
                                                <label for="STARTING_PRICE"><i class="fas fa-tag"></i> Starting Price</label>
                                                <input type="number" id="STARTING_PRICE" name="price" placeholder="Enter starting price" required>
                                            </div>

                                            <!-- Capacity -->
                                            <div class="FORM_GROUP" id="CAPACITEE">
                                                <label for="CAPACITY"><i class="fas fa-users"></i> Capacity</label>
                                                <input type="number" id="CAPACITY" name="package_size" placeholder="Enter guest capacity (e.g., 50)" required>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>">

                                <!-- Submit Button -->
                                <div class="FORM_GROUP_SUBMIT">
                                    <div class="LEFT_BUTTON">
                                        <a href="vendor_package.php" id="CANCEL_PACKAGE" class="CANCEL_BUTTON"><i class="fas fa-times"></i> Cancel</a>
                                    </div>
                                    <div class="RIGHT_BUTTONS">
                                        <button type="reset" id="RESET_PACKAGE"><i class="fas fa-undo"></i> Reset</button>
                                        <button type="submit" id="SUBMIT_PACKAGE"><i class="fas fa-upload"></i> Publish</button>
                                    </div>
                                </div>
                        </div>
                    </form>
                </div>
            </div>



        </div>
    </div>
    <script>
    document.getElementById('PACKAGE_THUMBNAIL').addEventListener('change', function(e) {
        const preview = document.getElementById('thumbnailPreview');
        const placeholder = document.querySelector('.PLACEHOLDER_TEXT');
        const file = e.target.files[0];

        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }

            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            placeholder.style.display = 'flex';
            preview.src = '#';
        }
    });
    </script>
</body>
</html>
