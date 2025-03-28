<?php
include 'db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "admin") {
    header("Location: login.php");
    exit;
}

// Handle vendor approval/denial
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vendor_id = $_POST['vendor_id'];
    $action = $_POST['action']; // "Approve" or "Deny"

    if ($action == "Approve") {
        $status = "Approved";
    } else {
        $status = "Denied";
    }

    $sql = "UPDATE vendors SET status = ? WHERE vendor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $vendor_id);

    if ($stmt->execute()) {
        $message = "Vendor has been $status successfully.";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Get list of pending vendors
$sql = "SELECT * FROM vendors WHERE status = 'Pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Vendi</title>
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
                    <a href="#" class="NAV_ACTIVE"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                    <a href="admin_vendors_approval.php"><i class="fas fa-user-check"></i>Vendors Management</a>
                    <a href="admin_vendors_tab.php"><i class="fas fa-users"></i>Clients (App)</a>
                    <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
            <div class="MENU_HEADER">SETTINGS</div>
                    <a href="admin_profile.php"><i class="fa fa-fw fa-user"></i> <span>Profile</span></a>            
                    <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Admin Dashboard</h1>
                </div>

                    <?php if (isset($message)): ?>
                        <p><?php echo $message; ?></p>
                    <?php endif; ?>

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




    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Business Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Address</th>
                <th>Service</th>
                <th>Business Document</th>
                <th>Action</th>
            </tr>
            <?php while ($vendor = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $vendor['business_name']; ?></td>
                    <td><?php echo $vendor['email']; ?></td>
                    <td><?php echo $vendor['mobile_number']; ?></td>
                    <td><?php echo $vendor['address']; ?></td>
                    <td><?php echo $vendor['service_option']; ?></td>
                    <td>
                        <a href="<?php echo $vendor['business_document']; ?>" target="_blank">View Document</a>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="vendor_id" value="<?php echo $vendor['vendor_id']; ?>">
                            <button type="submit" name="action" value="Approve">Approve</button>
                            <button type="submit" name="action" value="Deny">Deny</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No pending vendors.</p>
    <?php endif; ?>

    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
