<?php
session_start();

if (!isset($_SESSION['admin_name'])) {
    header("Location: admin_dashboard.php");
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
    if (isset($_POST['delete_feedback'])) {
        // Get the feedback ID to delete
        $feedbackId = $_POST['feedback_id'];

        // Here, you would typically delete the feedback from the database
        // Example SQL: DELETE FROM feedback WHERE feedback_id = '$feedbackId';
        echo "Deleted feedback with ID: $feedbackId<br>";

        // Redirect back to the feedback page
        header("Location: admin_feedback.php");
        exit();
    } elseif (isset($_POST['mark_resolved'])) {
        // Get the feedback ID to mark as resolved
        $feedbackId = $_POST['feedback_id'];

        // Here, you would typically update the feedback status in the database
        // Example SQL: UPDATE feedback SET status = 'Resolved' WHERE feedback_id = '$feedbackId';
        echo "Marked feedback with ID: $feedbackId as resolved<br>";

        // Redirect back to the feedback page
        header("Location: admin_feedback.php");
        exit();
    }
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    // Get the temporary file path and read the binary data
    $tmpFilePath = $_FILES['profile_pic']['tmp_name'];
    $profilePicData = file_get_contents($tmpFilePath);
    
    // Validate it's actually an image
    $imageInfo = getimagesize($tmpFilePath);
    if ($imageInfo === false) {
        die("Uploaded file is not a valid image");
    }
    
    // Update database with binary data
    $sql = "UPDATE admin_console SET admin_profile = ? WHERE admin_name = ?";
    $stmt = $conn->prepare($sql);
    
    // Use 'b' for blob type in bind_param
    $null = null;
    $stmt->bind_param("bs", $null, $_SESSION['admin_name']);
    $stmt->send_long_data(0, $profilePicData);
    
    if ($stmt->execute()) {
        // For session, we'll use a data URI
        $mimeType = $imageInfo['mime'];
        $_SESSION['admin_profile'] = 'data:' . $mimeType . ';base64,' . base64_encode($profilePicData);
    } else {
        echo "Error updating profile picture: " . $conn->error;
    }
    $stmt->close();
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
    <title>Admin Feedback | Vendi</title>
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
                    <a href="admin_vendors.php"><i class="fas fa-store"></i> Vendors Approval</a>
                    <a href="admin_vendors_tab.php"><i class="fas fa-users"></i> Vendors Management</a>
                    <a href="admin_clients.php"><i class="fas fa-users"></i>Clients</a>
                    <a href="#" class="NAV_ACTIVE"><i class="fas fa-comment-dots"></i> <span>Feedback</span> </a>
            <div class="MENU_HEADER">SETTINGS</div>
                    <a href="admin_profile.php"><i class="fa fa-fw fa-user"></i> <span>Profile</span></a>
                    <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Feedback</h1>
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
            
            <!-- Feedback Table -->
            <div class="MAIN_CONTAINER">
                <div class="BOOKING_TABLE">
                    <h2>Vendor Feedback</h2>
                    <form action="admin_feedback.php" method="POST">
                        <table>
                            <thead>
                                <tr>
                                    <th>Business Name</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example Row 1 -->
                                <tr>
                                    <td class="BUSINESS_NAME">Jollibee</td>
                                    <td class="EMAIL">jollibee@gmail.com</td>
                                    <td class="MESSAGE">The platform is great, but I encountered an issue with uploading documents.</td>
                                    <td class="ACTION_BUTTONS">
                                        <button type="submit" name="mark_resolved" class="RESOLVE_BUTTON">Mark as Resolved</button>
                                        <button type="submit" name="delete_feedback" class="DELETE_BUTTON">Delete</button>
                                        <input type="hidden" name="feedback_id" value="1">
                                    </td>
                                </tr>
                                <!-- Example Row 2 -->
                                <tr>
                                    <td class="BUSINESS_NAME">McDonald's</td>
                                    <td class="EMAIL">mcdonalds@gmail.com</td>
                                    <td class="MESSAGE">Excellent service! Keep up the good work.</td>
                                    <td class="ACTION_BUTTONS">
                                        <button type="submit" name="mark_resolved" class="RESOLVE_BUTTON">Mark as Resolved</button>
                                        <button type="submit" name="delete_feedback" class="DELETE_BUTTON">Delete</button>
                                        <input type="hidden" name="feedback_id" value="2">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="dashboard.js"></script>
    
</body>
</html>