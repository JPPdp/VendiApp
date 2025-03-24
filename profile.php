<?php
session_start();

if (!isset($_SESSION['businessname'])) {
    header("Location: dashboard.php");
    exit();
}
date_default_timezone_set('Asia/Manila');

$currentHour = date('H');

// Determine the greeting based on the time
if ($currentHour >= 1 && $currentHour < 4) {
    $greeting = '🌄 Good Dawn!';
} elseif ($currentHour >= 16 && $currentHour < 18.5) {
    $greeting = '🌅 Good Dusk!';
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
$sql = "SELECT * FROM vendors WHERE businessname = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['businessname']);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

if ($vendor) {
    $_SESSION['vendors_id'] = $vendor['vendors_id'];
    $_SESSION['vendors_profile'] = $vendor['vendors_profile'];
    $_SESSION['business_description'] = $vendor['business_description'];
    $_SESSION['email'] = $vendor['email'];
    $_SESSION['mobile'] = $vendor['mobile'];
    $_SESSION['address'] = $vendor['address'];
    $_SESSION['city_municipal'] = $vendor['city_municipal'];
    $_SESSION['province'] = $vendor['province'];
    $_SESSION['business_category'] = $vendor['business_category'];
    $_SESSION['business_documents'] = $vendor['business_documents'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $profilePic = $_FILES['profile_pic'];
        $profilePicPath = 'uploads/' . basename($profilePic['name']);
        
        if (move_uploaded_file($profilePic['tmp_name'], $profilePicPath)) {
            $sql = "UPDATE vendors SET vendors_profile = ? WHERE businessname = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $profilePicPath, $_SESSION['businessname']);
            if ($stmt->execute()) {
                $_SESSION['vendors_profile'] = $profilePicPath;
            } else {
                echo "Error updating profile picture: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Error uploading profile picture.";
        }
    } else {
        $businessDescription = $_POST['BUSINESS_DESCRIPTION'];
        $sql = "UPDATE vendors SET business_description = ? WHERE businessname = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $businessDescription, $_SESSION['businessname']);
        if ($stmt->execute()) {
            $_SESSION['business_description'] = $businessDescription;
        } else {
            echo "Error updating business description: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Vendi</title>
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
                    <span>DASHBOARD</span>
                </div>
            </div>
            <div class="MENU_HEADER">MANAGEMENT</div>
            <a href="dashboard.php"><i class="fas fa-stream"></i> Dashboard</a>
            <a href="listings.php"><i class="fa fa-fw fa-store"></i> Listings</a>
            <a href="bookings.php"><i class="fa fa-fw fa-calendar"></i> Bookings</a>
            <a href="customers.php"><i class="fa fa-fw fa-users"></i> Customers</a>
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="profile.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-user"></i> <span> Profile</span> </a>
            <a href="help.php"><i class="fa fa-fw fa-question-circle"></i> Help</a>
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
                            <img src="<?php echo htmlspecialchars($_SESSION['vendors_profile']); ?>" alt="Profile Picture" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['businessname']); ?></span>             
                    </div>
                </div>
            </div>

            <!-- Profile Container -->
            <div class="PROFILE_CONTAINER">
                <!-- Left Profile Section -->
                <div class="LEFT_PROFILE">
                    <div class="PROFILE_PIC_CONTAINER">
                        <img src="<?php echo htmlspecialchars($_SESSION['vendors_profile']); ?>" alt="Profile Picture" class="PROFILE_PIC2">
                        <div class="EDIT_ICON_CONTAINER" title="Change Profile Picture">
                            <form id="PROFILE_PIC_FORM" method="post" enctype="multipart/form-data">
                                <label for="VENDOR_PROFILE_PIC" class="EDIT_ICON_LABEL">
                                    <i class="EDIT_ICON fas fa-camera" aria-hidden="true"></i>
                                    <input type="file" id="VENDOR_PROFILE_PIC" name="profile_pic" accept="image/*" style="display: none;" onchange="document.getElementById('PROFILE_PIC_FORM').submit();">
                                </label>
                            </form>
                        </div>
                    </div>
                    <!-- Business Name -->
                    <h2 id="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['businessname']); ?></h2>
                
                    <p class="USER_ID">ID: <?php echo htmlspecialchars($_SESSION['vendors_id']); ?></p>

                    <!-- Form for Business Description -->
                    <form id="DESCRIPTION_FORM" method="post">
                        <!-- Business Description -->
                        <label for="VENDOR_DESCRIPTION">Business Description</label>
                        <textarea id="VENDOR_DESCRIPTION" name="BUSINESS_DESCRIPTION" placeholder="Enter a brief description of your business"><?php echo htmlspecialchars($_SESSION['business_description']); ?></textarea>

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
                                    <!-- Business Name -->
                                    <label>Business Name</label>
                                    <input type="text" id="businessname" name="businessname" value="<?php echo htmlspecialchars($_SESSION['businessname']); ?>" readonly>
                                </div>
                                <div class="BESIDE_FIELD">
                                    <!-- Email -->
                                    <label>Email</label>
                                    <input type="email" id="business_email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
                                </div>

                                <div class="BESIDE_FIELD">
                                    <!-- Mobile Number -->
                                    <label>Mobile Number</label>
                                    <input type="tel" id="business_mobile" name="mobile" required minlength="10" maxlength="10" value="<?php echo htmlspecialchars($_SESSION['mobile']); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- 2nd Stack: Address Information -->
                        <div class="STACK">
                            <h3>Address Information</h3>
                            <div class="BESIDE_FIELDS">
                                <div class="BESIDE_FIELD">
                                    <label>Address</label>
                                    <input type="text" id="business_address" name="address" value="<?php echo htmlspecialchars($_SESSION['address']); ?>" readonly>
                                </div>

                                <div class="BESIDE_FIELD">
                                    <label>City</label>
                                    <input type="text" id="business_city" name="city" value="<?php echo htmlspecialchars($_SESSION['city_municipal']); ?>" readonly>
                                </div>

                                <div class="BESIDE_FIELD">
                                    <label>Province</label>
                                    <input type="text" id="business_province" name="province" value="<?php echo htmlspecialchars($_SESSION['province']); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- 3rd Stack: Additional Information -->
                        <div class="STACK3">
                            <h3>Additional Information</h3>
                            <div class="BESIDE_FIELDS">
                                <div class="BESIDE_FIELD">
                                    <label>Services Offered</label>
                                    <input type="text" id="business_service" name="Services" value="<?php echo htmlspecialchars($_SESSION['business_category']); ?>" readonly>
                                </div>

                                <div class="BESIDE_FIELD">
                                    <label>Business Documents</label>
                                    <!-- Button to view the document -->
                                    <a href="#IMAGE_VIEW" class="VIEW_BUTTON" id="VIEW_BUTTON">View File</a>
                                </div>

                                <div id="IMAGE_VIEW" class="EXPAND"><!-- Container for Expanded Image -->
                                    <a href="#" class="CLOSE_BUTTON">&times;</a>
                                    <img class="EXPANDED_IMAGE" src="image_view.php" alt="Expanded Business Document">
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

</body>
</html>
