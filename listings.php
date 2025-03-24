<?php
session_start();

//Display name on Upper Right Nav
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
    $_SESSION['vendors_profile'] = $vendor['vendors_profile'];
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
            }
            $conn->close(); 
            }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listings | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="notifications.css">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="listings.css?v=<?php echo time(); ?>">
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
            <a href="listings.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-store"></i><span> Listings</span></a>
            <a href="bookings.php"><i class="fa fa-fw fa-calendar"></i> Bookings</a>
            <a href="customers.php"><i class="fa fa-fw fa-users"></i> Customers</a>
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="help.php"><i class="fa fa-fw fa-question-circle"></i> Help</a>
            <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD2" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Listings</h1>
                </div>
                <div class="RIGHT_UPPER">
                    <div class="ACCOUNT">
                        <span class="HELLO"><?php echo $greeting; ?></span>
                        <a href="profile.php">
                            <img src="<?php echo htmlspecialchars($_SESSION['vendors_profile'])?>" alt="Profile Picture" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['businessname']); ?></span>             
                    </div>
                </div>
            </div>

            <div class="LISTINGS_CONTAINER">
                <header class="LISTINGS_HEADER">
                    <h2>Package Management</h2>
                        <a href="listings_add_package.php" id="ADD_PACKAGE"><i class="fas fa-plus"></i> Add Package</a>
                </header>

                <!-- Example Package -->
                <div class="PACKAGE">
                    <div class="LEFT_PART">
                        <div class="PACKAGE_THUMBNAIL">
                            <img src="assets/images/jollitown.png" alt="Package Thumbnail">
                        </div>
                    </div>
                    <div class="RIGHT_PART">
                        <!-- Package Name and Status -->
                        <div class="PACKAGE_HEADER">
                            <div class="PACKAGE_NAME">
                                <h3 id="PACKAGE_NAME"><?php echo "Basic Package" ?></h3>
                            </div>

                        </div>
                        <!-- Package Description and Features -->
                        <div class="PACKAGE_CONTENT">
                            <div class="PACKAGE_DESCRIPTION">
                                <span id="PACKAGE_DESCRIPTION"><?php echo "With colorful decorations, 
                                langhap-sarap favorites, and non-stop fun - it's the ultimate celebration 
                                for your little one! From exciting games to a special appearance by Jollibee 
                                himself, every moment is filled with joy. With our customizable party packages, 
                                you can create the perfect event that fits your budget and preferences." ?></span>
                            </div>
                        </div>
                        <!-- Package Details -->
                        <div class="BESIDE_FIELDS">
                            <div class="BESIDE_FIELD">
                                <div class="PACKAGE_DETAIL">
                                    <h3 id="label"><i class="fas fa-tag"></i>Starting Price</h3>
                                    <p id="STARTING_PRICE"> ₱<?php echo "1,299" ?></p>
                                </div>
                            </div>
                            <div class="BESIDE_FIELD">
                                <div class="PACKAGE_DETAIL">
                                    <h3 id="label"><i class="fas fa-users"></i>Capacity</h3>
                                    <p id="CAPACITY"><?php echo "50" ?> guests</p>
                                </div>
                            </div>
                            <div class="BESIDE_FIELD">
                                <div class="PACKAGE_DETAIL">
                                    <h3 id="label"><i class="fas fa-calendar-alt"></i>Date Created</h3>
                                    <p id="DATE_CREATED"> <?php echo "March 17, 2025" ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- End div-Package -->

            </div>
</body>
</html>