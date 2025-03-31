<?php
include 'db_connect.php';
session_start();

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

// Handle form submission to mark as completed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complete'])) {
    $booking_id = $_POST['complete'];
    $status = "Completed";

    // Update the booking status to Completed
    $sql = "UPDATE bookings SET status = ? WHERE booking_id = ? AND vendor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $status, $booking_id, $vendor_id);

    if ($stmt->execute()) {
        $message = "Booking has been marked as completed successfully.";
    } else {
        $message = "Error updating booking: " . $conn->error;
    }
}

// Fetch vendor details
$sql = "SELECT * FROM vendors WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

// Fetch approved bookings
$sql = "SELECT b.booking_id, b.reference_id, c.name AS client_name, 
               c.email, c.mobile_number, vp.package_name, b.status, 
               b.service_date, b.service_time, b.booking_location
        FROM bookings b
        JOIN clients c ON b.client_id = c.client_id
        JOIN vendor_packages vp ON b.package_id = vp.package_id
        WHERE b.vendor_id = ? AND b.status = 'Approved'
        ORDER BY b.service_date ASC, b.service_time ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$approved_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Bookings | Vendi</title>
    <link rel="icon" href="assets/images/Vendi_Icon.png" type="image/icon type">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="NAV_CONTAINER">
    <!-- Navigation Bar -->
    <div class="NAVIGATION_BAR">
        <div class="LOGO">
            <div class="LOGO_NAME">Vendi
                <span id="VENDORS">VENDORS</span>
            </div>
        </div>
        
        <div class="MENU_HEADER">MANAGEMENT</div>
        <a href="vendor_dashboard.php"><i class="fas fa-stream"></i> Dashboard</a>
        
        <!-- Bookings Dropdown -->
        <div class="NAV_DROPDOWN">
            <a class="NAV_DROPDOWN_TOGGLE_ACTIVE" href="#">
                <span><i id="BOOK_ICON" class="fa fa-fw fa-calendar"></i> Bookings <i class="fas fa-chevron-down NAV_DROPDOWN_ICON_ACTIVE"></i></span>
            </a>
            <div class="NAV_DROPDOWN_CONTENT_ACTIVE">
                <a href="vendor_bookings_approval.php"><i class="fas fa-calendar-alt"></i> <span id="ITALIC">Pending Bookings</span></a>
                <a href="vendor_bookings_active.php" class="NAV_ACTIVE"><i class="far fa-calendar-check"></i> <span id="ITALIC">Scheduled Bookings</span></a>
                <a href="vendor_bookings_completed.php"><i class="fas fa-calendar-check"></i> <span id="ITALIC">Completed Bookings</span></a>
                <a href="vendor_bookings_cancelled.php"><i class="fas fa-calendar-times"></i> <span id="ITALIC">Cancelled Bookings</span></a>
            </div>
        </div>
        
        <a href="vendor_package.php"><i class="fa fa-fw fa-store"></i> Packages</a>
        
        <a href="vendor_clients.php"><i class="fas fa-users"></i> Clients</a>
        
        <div class="MENU_HEADER">SETTINGS</div>
        <a href="vendor_profile.php"><i class="fa fa-fw fa-user"></i> <span>Profile</span></a>
        <a href="vendor_help.php"><i class="fas fa-question-circle"></i> Help</a>
        <a href="logout.php" class="LOGOUT"><i class="fas fa-sign-out-alt"></i> Log Out</a>
    </div>
    
    <!-- Dashboard Content -->
    <div class="DASHBOARD" id="DASHBOARD">
        <div class="UPPER">
            <div class="LEFT_UPPER">
                <h1 class="DASHBOARD_TITLE">Scheduled Bookings</h1>
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

        <?php if (isset($message)): ?>
            <div class="MESSAGE">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <!-- Booking Details Table -->
        <div class="BOOKINGS_CONTAINER">
            <header class="BOOKINGS_HEADER">
                <h2>Approved Bookings</h2>
            </header>
        </div>

        <div class="BOOKING_TABLE">
            <?php if (!empty($approved_bookings)): ?>
                <form method="POST">
                    <table>
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> Booking ID</th>
                                <th><i class="fas fa-user"></i> Client Name</th>
                                <th><i class="fas fa-phone"></i> Mobile Number</th>
                                <th><i class="fas fa-calendar-alt"></i> Scheduled On</th>
                                <th><i class="fas fa-map-marker-alt"></i> Event Location</th>
                                <th><i class="fas fa-box"></i> Package</th>
                                <th><i class="fas fa-info-circle"></i> Status</th>
                                <th><i class="fas fa-cogs"></i> Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($approved_bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                <td><b><?php echo htmlspecialchars($booking['client_name']); ?></b></td>
                                <td><?php echo htmlspecialchars($booking['mobile_number']); ?></td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($booking['service_date'])); ?> 
                                    <small><?php echo date('h:i A', strtotime($booking['service_time'])); ?></small>
                                </td>
                                <td><?php echo !empty($booking['booking_location']) ? htmlspecialchars($booking['booking_location']) : 'Not specified'; ?></td>
                                <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($booking['status']); ?>">
                                        <?php echo htmlspecialchars($booking['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="submit" name="complete" value="<?php echo $booking['booking_id']; ?>" class="ACTION_BUTTON" id="APPROVE_BUTTON">
                                        <i class="fas fa-check-circle"></i> Mark as Done
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
            <?php else: ?>
                <table>
                    <tbody>
                        <tr>
                            <td colspan="8" class="NO_DATA_CELL">
                                <p>No scheduled bookings found.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>