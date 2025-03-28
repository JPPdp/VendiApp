<?php
include 'db_connect.php';
session_start();

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
                    <span>VENDOR</span>
                </div>
            </div>
            <div class="MENU_HEADER">VENDOR PORTAL</div>
            <a href="vendor_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
            <?php if ($vendor['status'] == "Approved"): ?>
                <a href="vendor_packages.php"><i class="fas fa-box-open"></i> My Packages</a>
                <a href="vendor_bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
            <?php endif; ?>
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="vendor_profile.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-user"></i> <span> Profile</span></a>
            <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
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
                            <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['business_name']); ?>!</span>             
                    </div>
                </div>
            </div>

            <!-- Profile Container -->
            <div class="PROFILE_CONTAINER">
                <!-- Left Profile Section -->
                <div class="LEFT_PROFILE">
                    <div class="PROFILE_PIC_CONTAINER">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture" class="PROFILE_PIC2">
                        <div class="EDIT_ICON_CONTAINER" title="Change Profile Picture">
                            <form id="PROFILE_PIC_FORM" method="post" enctype="multipart/form-data">
                                <label for="VENDOR_PROFILE_PIC" class="EDIT_ICON_LABEL">
                                    <i class="EDIT_ICON fas fa-camera" aria-hidden="true"></i>
                                    <input type="file" id="VENDOR_PROFILE_PIC" name="profile_pic" accept="image/*" style="display: none;" onchange="document.getElementById('PROFILE_PIC_FORM').submit();">
                                </label>
                            </form>
                        </div>
                    </div>
                    <h2 id="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['business_name']); ?></h2>
                    <p class="USER_ID">ID: <?php echo htmlspecialchars($_SESSION['vendor_id']); ?></p>
                    <p class="STATUS <?php echo strtolower($_SESSION['status']); ?>"><?php echo htmlspecialchars($_SESSION['status']); ?></p>
                </div>

                <!-- Right Profile Section -->
                <div class="RIGHT_PROFILE">
                    <div class="PROF_CONTAINER">
                        <div class="PROF_HEADER">
                            <h2>Business Details</h2>
                        </div>
                    </div>    
                    <div class="STACK">
                        <h3>Business Information</h3>
                        <label>Business Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($vendor['business_name']); ?>" readonly>

                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($vendor['email']); ?>" readonly>
                        
                        <label>Service Type</label>
                        <input type="text" value="<?php echo htmlspecialchars($vendor['service_option']); ?>" readonly>

                        <label>Mobile Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($vendor['mobile_number']); ?>" readonly>

                        <label>Business Address</label>
                        <textarea readonly><?php echo htmlspecialchars($vendor['address']); ?></textarea>

                        <label>Vendor ID</label>
                        <input type="text" value="<?php echo htmlspecialchars($vendor['vendor_id']); ?>" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h2>Welcome, <?php echo $vendor['business_name']; ?></h2>
    
    <h3>Business Information</h3>
    <p><strong>Email:</strong> <?php echo $vendor['email']; ?></p>
    <p><strong>Service Type:</strong> <?php echo $vendor['service_option']; ?></p>
    <p><strong>Mobile:</strong> <?php echo $vendor['mobile_number']; ?></p>
    <p><strong>Address:</strong> <?php echo $vendor['address']; ?></p>
    <p><strong>Status:</strong> <?php echo $vendor['status']; ?></p>

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
