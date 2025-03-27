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

// Database connection
$conn = new mysqli("localhost", "root", "", "janrich_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch admin details
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

// Fetch all vendors from the database
$sql = "SELECT * FROM vendors";
$result = $conn->query($sql);

$vendors = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Handle profile picture
        if (!empty($row['vendors_profile'])) {
            // Check if the data is already base64 encoded (might be stored as text)
            if (base64_encode(base64_decode($row['vendors_profile'])) === $row['vendors_profile']) {
                // If it's already base64 encoded, use it directly
                $row['vendors_profile'] = 'data:image/jpeg;base64,' . $row['vendors_profile'];
            } else {
                // Otherwise, encode it
                $row['vendors_profile'] = 'data:image/jpeg;base64,' . base64_encode($row['vendors_profile']);
            }
        } else {
            // Default image if no profile picture is available
            $row['vendors_profile'] = 'assets/images/default_profile.jpg';
        }
        $vendors[] = $row;
    }
}

// Handle client deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_client'])) {
    $vendorsId = $_POST['client_id'];

    // Delete the client from the database
    $deleteSql = "DELETE FROM vendors WHERE vendors_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $vendorsId);
    if ($deleteStmt->execute()) {
        header("Location: admin_vendors_tab.php");
        exit();
    } else {
        echo "Error deleting client: " . $conn->error;
    }
    $deleteStmt->close();
}

$conn->close();
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
                    <a href="admin_vendors.php"><i class="fas fa-store"></i>Vendors Approval</a>
                    <a href="#" class="NAV_ACTIVE"><i class="fas fa-users"></i><span>Vendors Management</span></a>
                    <a href="admin_clients.php"><i class="fas fa-users"></i>Clients</a>
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
            
            <!-- Client Details Table -->
            <div class="MAIN_CONTAINER">
                <div class="BOOKING_TABLE">
                    <h2>Vendors Details</h2>
                    <form action="admin_vendors_tab.php" method="POST">
                        <table>
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Profile Picture</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile Number</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vendors as $vendor): ?>
                                <tr>
                                    <td class="USER_ID"><?php echo htmlspecialchars($vendor['vendors_id']); ?></td>
                                    <td class="PROFILE_PICTURE">
                                        <div class="Admin_Circular_Image_Container">
                                            <?php if (strpos($vendor['vendors_profile'], 'data:image') === 0): ?>
                                                <img src="<?php echo $vendor['vendors_profile']; ?>" alt="Profile Picture" class="Admin_Circular_Image">
                                            <?php else: ?>
                                                <img src="assets/images/default_profile.jpg" alt="Default Profile" class="Admin_Circular_Image">
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="CLIENT_NAME"><?php echo htmlspecialchars($vendor['businessname']); ?></td>
                                    <td class="CLIENT_EMAIL"><?php echo htmlspecialchars($vendor['vendors_email']); ?></td>
                                    <td class="MOBILE_NUMBER"><?php echo htmlspecialchars($vendor['vendors_mobile']); ?></td>
                                    <td class="ACTION_BUTTONS">
                                        <button type="submit" name="delete_client" class="DELETE_BUTTON">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                        <input type="hidden" name="client_id" value="<?php echo htmlspecialchars($vendor['vendors_id']); ?>">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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