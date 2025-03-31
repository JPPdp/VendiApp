<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


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
    <title>Vendor Dashboard | Vendi</title>
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
            
            <a href="vendor_package.php"><i class="fa fa-fw fa-store"></i> Packages</a>
            
            <a href="vendor_clients.php"><i class="fas fa-users"></i> Clients</a>
            
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="vendor_profile.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-user"></i> <span>Profile</span></a>
            <a href="vendor_help.php"><i class="fas fa-question-circle"></i> Help</a>
            <a href="logout.php" class="LOGOUT"><i class="fas fa-sign-out-alt"></i> Log Out</a>
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
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="" class="PROFILE_PIC2">
                        <div class="EDIT_ICON_CONTAINER" title="Change Profile Picture">
                            <form id="PROFILE_PIC_FORM" method="post" enctype="multipart/form-data">
                                <label for="VENDOR_PROFILE_PIC" class="EDIT_ICON_LABEL">
                                    <i class="EDIT_ICON fas fa-camera" aria-hidden="true"></i>
                                    <input type="file" id="VENDOR_PROFILE_PIC" name="profile_pic" accept="image/*" style="display: none;" onchange="document.getElementById('PROFILE_PIC_FORM').submit();">
                                </label>
                            </form>
                        </div>
                    </div>
                    <h2 id="BUSINESS_NAME"><?php echo htmlspecialchars($vendor['business_name']); ?></h2>
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
                                    <button id="CHANGE_PASSWORD" class="ACCOUNT_MANAGE" onclick="window.location.href='forgot_password.php'">
                                        <i class="fas fa-key"></i> Change Password
                                    </button>
                                </div>
                                <div class="BESIDE_FIELD">
                                    <button id="DELETE_ACCOUNT" class="ACCOUNT_MANAGE" onclick="confirmDelete()">
                                        <i class="fas fa-trash-alt"></i> Delete Account
                                    </button>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





    <?php if ($vendor['status'] == "Pending"): ?>
        <p>Your account is awaiting approval from the admin.</p>
    <?php elseif ($vendor['status'] == "Denied"): ?>
        <p>Your registration was denied. Contact admin for more details.</p>
    <?php else: ?>
        <h3>Your Packages</h3>
        <?php if (!empty($packages)): ?>
            <table border="1">
                <tr>
                    <th>Package Name</th>
                    <th>Size</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($packages as $package): ?>
                    <tr>
                        <td><?php echo $package['package_name']; ?></td>
                        <td><?php echo $package['package_size']; ?> people</td>
                        <td>$<?php echo $package['price']; ?></td>
                        <td>
                            <a href="edit_package.php?id=<?php echo $package['package_id']; ?>">Edit</a> | 
                            <a href="delete_package.php?id=<?php echo $package['package_id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this package?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No packages added yet.</p>
        <?php endif; ?>

        <h3>Add New Package</h3>
        <form action="add_package.php" method="POST">
            <label>Package Name:</label>
            <input type="text" name="package_name" required><br>

            <label>Package Size (people):</label>
            <input type="number" name="package_size" required><br>

            <label>Price ($):</label>
            <input type="number" step="0.01" name="price" required><br>

            <input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>">
            <button type="submit">Add Package</button>
        </form>
    <?php endif; ?>

    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
