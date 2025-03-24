<?php
session_start();

if (!isset($_SESSION['admin_name'])) {
    header("Location: admin_vendors.php");
    exit();
}
date_default_timezone_set('Asia/Manila');

$currentHour = date('H');

// Determine the greeting based on the time
if ($currentHour >= 1 && $currentHour < 4) {
    $greeting = '🌅 Good Dawn!';
} elseif ($currentHour >= 16 && $currentHour < 18.5) {
    $greeting = '🌄 Good Dusk!';
} elseif ($currentHour < 12) {
    $greeting = '☀️ Good Morning!';
} elseif ($currentHour < 18) {
    $greeting = '🌤️ Good Afternoon!';
} else {
    $greeting = '🌙 Good Evening!';
}

// Assuming you have a database connection established
$conn = new mysqli("localhost", "root", "", "janrich_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$sql = "SELECT * FROM admin_console WHERE admin_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['admin_name']);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($admin) {
    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['admin_profile'] = $admin['admin_profile'];
    $_SESSION['admin_description'] = $admin['admin_description'];
    $_SESSION['admin_email'] = $admin['admin_email'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $profilePic = $_FILES['profile_pic'];
        $profilePicPath = 'admin_uploads/' . basename($profilePic['name']);
        
        if (move_uploaded_file($profilePic['tmp_name'], $profilePicPath)) {
            $sql = "UPDATE admin_console SET admin_profile = ? WHERE admin_name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $profilePicPath, $_SESSION['admin_name']);
            if ($stmt->execute()) {
                $_SESSION['admin_profile'] = $profilePicPath;
            } else {
                echo "Error updating profile picture: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Error uploading profile picture.";
        }
    } else {
        if (isset($_POST['admin_description'])) {
            $adminDescription = $_POST['admin_description'];
            $sql = "UPDATE admin_console SET admin_description = ? WHERE admin_name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $adminDescription, $_SESSION['admin_name']);
            if ($stmt->execute()) {
                $_SESSION['admin_description'] = $adminDescription;
            } else {
                echo "Error updating admin description: " . $conn->error;
            }
            $stmt->close();
        }
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
    <link rel="stylesheet" href="notifications.css">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="profile.css?v=<?php echo time(); ?>">
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
            <div class="MENU_HEADER">MANAGEMENT</div>
            <a href="admin_vendors.php"><i class="fas fa-store"></i> Vendors</a>
            <a href="admin_clients.php"><i class="fa fa-fw fa-users"></i> Clients</a>
            <a href="admin_feedback.php"><i class="fa fa-fw fa-comment-dots"></i> Feedback</a>
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="admin_profile.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-user"></i> <span> Profile</span> </a>
            <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Profile</h1>
                </div>
                <div class="RIGHT_UPPER">
                    
                    <div class="ACCOUNT">
                        <div class="GREETING"><?php echo $greeting; ?></div>
                    
                        <a href="profile.php">
                            <img src="<?php echo htmlspecialchars($_SESSION['admin_profile']); ?>" alt="Profile Picture" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>             
                    </div>
                </div>
            </div>

            <!-- Profile Container -->
            <div class="PROFILE_CONTAINER">
                <!-- Left Profile Section -->
                <div class="LEFT_PROFILE">
                    <div class="PROFILE_PIC_CONTAINER">
                        <img src="<?php echo htmlspecialchars($_SESSION['admin_profile']); ?>" alt="Profile Picture" class="PROFILE_PIC2">
                        <div class="EDIT_ICON_CONTAINER" title="Change Profile Picture">
                            <form id="PROFILE_PIC_FORM" method="post" enctype="multipart/form-data">
                                <label for="ADMIN_PROFILE_PIC" class="EDIT_ICON_LABEL">
                                    <i class="EDIT_ICON fas fa-camera" aria-hidden="true"></i>
                                    <input type="file" id="ADMIN_PROFILE_PIC" name="profile_pic" accept="image/*" style="display: none;" onchange="document.getElementById('PROFILE_PIC_FORM').submit();">
                                </label>
                            </form>
                        </div>
                    </div>
                    <!-- Admin Name -->
                    <h2 id="Admin_Name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></h2>
                
                    <p class="USER_ID">ID: <?php echo htmlspecialchars($_SESSION['admin_id']); ?></p>

                    <!-- Form for Admin Description -->
                    <form id="ADMIN_DESCRIPTION_FORM" method="post">
                        <!-- Admin Description -->
                        <label for="ADMIN_DESCRIPTION">Admin Description</label>
                        <textarea id="ADMIN_DESCRIPTION" name="admin_description" placeholder="Enter a brief description of yourself"><?php echo htmlspecialchars($_SESSION['admin_description']); ?></textarea>

                        <div class="BUTTON_CONTAINER"> <!-- Button -->
                        <button type="submit" class="SUBMIT_BUTTON">Submit</button>
                        </div>
                    </form>
                </div>

                <!-- Right Profile Section -->
                <div class="RIGHT_PROFILE">
                    <div class="RIGHT_PART">
                        <h2>Account Details</h2>
                        <!-- 1st Stack: Contact Information -->
                        <div class="STACK">
                            <h3>Account Information</h3>
                            <div class="BESIDE_FIELDS">
                                <div class="BESIDE_FIELD">
                                    <!-- Admin Name -->
                                    <label>Admin Name</label>
                                    <input type="text" id="ADMIN_NAME" name="adminname" value="<?php echo htmlspecialchars($_SESSION['admin_name']); ?>" readonly>
                                </div>
                                <div class="BESIDE_FIELD">
                                    <!-- Email -->
                                    <label>Email</label>
                                    <input type="email" id="ADMIN_EMAIL" name="email" value="<?php echo htmlspecialchars($_SESSION['admin_email']); ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="RIGHT_PART2">
                        <h2>Account Management</h2>
                        <div class="STACK3">
                            <div class="BESIDE_FIELDS">
                                <div class="BESIDE_FIELD">
                                    <button id="CHANGE_PASSWORD" class="ACCOUNT_MANAGE" onclick="window.location.href='forgot_password.php'">Change Password</button>
                                </div>
                                <div class="BESIDE_FIELD">
                                    <button id="DELETE_ACCOUNT" class="ACCOUNT_MANAGE" onclick="confirmDelete()">Delete Account</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>