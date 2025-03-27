<?php
session_start();

// Ensure the admin is logged in
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

// Connect to the database
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
    $_SESSION['admin_profile'] = $admin['admin_profile'] ?: 'assets/images/default_profile.jpg';
    $_SESSION['admin_description'] = $admin['admin_description'];
    $_SESSION['admin_email'] = $admin['admin_email'];
}

// Handle approval or rejection of vendors
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        $id = intval($_POST['vendor_id']); // Use the correct key 'vendor_id' and ensure it's an integer

        // Move the vendor to the vendors table, including the password
        $stmt = $conn->prepare("INSERT INTO vendors (businessname, vendors_email, vendors_mobile, business_documents, password, address, city_municipal, province, business_category) 
                                SELECT businessname, vendors_email, vendors_mobile, business_documents, password, address, city_municipal, province, business_category 
                                FROM pending_vendors WHERE vendors_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        // Delete the vendor from the pending_vendors table
        $stmt = $conn->prepare("DELETE FROM pending_vendors WHERE vendors_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif (isset($_POST['reject'])) {
        $id = intval($_POST['vendor_id']); // Use the correct key 'vendor_id' and ensure it's an integer

        // Remove the vendor from the pending_vendors table
        $stmt = $conn->prepare("DELETE FROM pending_vendors WHERE vendors_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// Fetch pending vendors for approval
$pendingVendors = $conn->query("SELECT vendors_id, businessname, vendors_email, vendors_mobile, business_documents FROM pending_vendors");
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
                    <a href="#" class="NAV_ACTIVE"><i class="fas fa-store"></i><span>Vendors Approval</span></a>
                    <a href="admin_vendors_tab.php"><i class="fas fa-users"></i>Vendors Management</a>
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
                    <h1 class="DASHBOARD_TITLE">Pending Vendor Approvals</h1>
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
                    <h2>Pending Vendors</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Business Documents</th>
                                <th>Business Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $pendingVendors->fetch_assoc()): ?>
                                <tr>
                                <td class="VIEW_DOCUMENT">
                                    <!-- Button to open the modal -->
                                    <a href="#IMAGE_VIEW_<?php echo $row['vendors_id']; ?>" class="VIEW_BUTTON" id="VIEW_BUTTON">View File</a>
                                </td>

                                <!-- Modal to display the image -->
                                <div id="IMAGE_VIEW_<?php echo $row['vendors_id']; ?>" class="EXPAND">
                                    <a href="#" class="CLOSE_BUTTON">&times;</a>
                                    <img class="EXPANDED_IMAGE" src="pending_image_view.php?vendor_id=<?php echo $row['vendors_id']; ?>" alt="Document File">
                                </div>
                                    <td><?php echo htmlspecialchars($row['businessname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['vendors_email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['vendors_mobile']); ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="vendor_id" value="<?php echo $row['vendors_id']; ?>">
                                            <button type="submit" name="approve">Approve</button>
                                        </form>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="vendor_id" value="<?php echo $row['vendors_id']; ?>">
                                            <button type="submit" name="reject">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="dashboard.js"></script>
    
</body>
</html>