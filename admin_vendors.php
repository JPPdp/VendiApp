<?php
session_start();

if (!isset($_SESSION['admin_name'])) {
    header("Location: admin_vendors.php");
    exit();
}
date_default_timezone_set('Asia/Manila');

// Get the current hour (24-hour format)
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


if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Get the status updates from the form
    $statusUpdates = $_POST['status'];

    // Example: Loop through the status updates and process them
    foreach ($statusUpdates as $vendorId => $newStatus) {
        // Here, you would typically update the database
        // Example SQL: UPDATE vendors SET status = '$newStatus' WHERE vendor_id = '$vendorId';
        echo "Updated vendor $vendorId to status: $newStatus<br>";
    }

    // Redirect back to the vendors page
    header("Location: vendors.php");
    exit();

    if (isset($_FILES['admin_profile_pic']) && $_FILES['admin_profile_pic']['error'] == 0) {
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
    <title>Admin Vendors | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
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

            <div class="MENU_HEADER">ADMINISTRATION</div>
                    <a href="#" class="NAV_ACTIVE"><i class="fas fa-store"></i> <span>Vendors</span></a>
                    <a href="admin_clients.php"><i class="fas fa-users"></i> Clients</a>
                    <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
            <div class="MENU_HEADER">SETTINGS</div>
                    <a href="admin_profile.php"><i class="fa fa-fw fa-user"></i> <span>Profile</span></a>            
                    <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Vendors</h1>
                </div>

                <div class="RIGHT_UPPER">
                    <div class="ACCOUNT">
                        <span class="HELLO"><?php echo $greeting; ?></span>
                        <a href="profile.php">
                            <img src="<?php echo htmlspecialchars($_SESSION['admin_profile']); ?>" alt="Profile Picture" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>             
                    </div>
                </div>
            </div>
            
            <!-- Vendor Details Table -->
            <div class="MAIN_CONTAINER">
                <div class="BOOKING_TABLE">
                    <h2>Vendor Details</h2>
                    <form action="update_vendors.php" method="POST">
                        <table>
                            <thead>
                                <tr>
                                    <th>Business Documents</th>
                                    <th>Business Name</th>
                                    <th>Business Email</th>
                                    <th>Mobile Number</th>
                                    <th>Address</th>
                                    <th>City/Municipality</th>
                                    <th>Province</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example Row 1 -->
                                <tr>
                                    <td class="VIEW_DOCUMENT">
                                        <a href="#IMAGE_VIEW" class="VIEW_BUTTON" id="VIEW_BUTTON">View File</a>
                                    </td>
                                    <div id="IMAGE_VIEW" class="EXPAND">
                                        <a href="#" class="CLOSE_BUTTON">&times;</a>
                                        <img class="EXPANDED_IMAGE" src="assets/images/sample_document.png" alt="Document File">
                                    </div>
                                    <td class="CLIENT_NAME">Jollibee</td>
                                    <td class="CLIENT_EMAIL">jollibee@gmail.com</td>
                                    <td class="MOBILE_NUMBER">+1234567890</td>
                                    <td class="ADDRESS">70 Bacayao</td>
                                    <td class="CITY_MUNICIPALITY">Dagupan City</td>
                                    <td class="PROVINCE">Pangasinan</td>
                                    <td class="CATEGORY">Food</td>
                                    <td class="STATUS_PENDING">Pending</td>
                                    <td class="ACTION_BUTTONS">
                                        <select name="status[1]" class="STATUS_DROPDOWN">
                                            <option value="Pending">Pending</option>
                                            <option value="Approved">Approve</option>
                                            <option value="Rejected">Reject</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="SUBMIT_BUTTON_CONTAINER">
                            <span id="SUBMIT_TEXT">Update vendor status?</span>
                            <button type="submit" class="SUBMIT_BUTTON">Submit</button>
                        </div>                    
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="dashboard.js"></script>
    
</body>
</html>