<?php
include 'db_connect.php';
session_start();

// Check if vendor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
    header("Location: login.php");
    exit;
}

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

$vendor_id = $_SESSION['user_id'];

// Fetch vendor details
$sql = "SELECT * FROM vendors WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

// Fetch clients who have transactions with this vendor
$sql = "SELECT DISTINCT c.client_id, c.name, c.email, c.mobile_number, 
               c.profile_picture, COUNT(cth.history_id) AS transaction_count
        FROM clients c
        JOIN client_transaction_history cth ON c.client_id = cth.client_id
        WHERE cth.vendor_id = ?
        GROUP BY c.client_id
        ORDER BY c.name ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$clients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Clients | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="customers.css?v=<?php echo time(); ?>">
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
                <a class="NAV_DROPDOWN_TOGGLE" href="#">
                    <i class="fa fa-fw fa-calendar"></i> Bookings <i class="fas fa-chevron-down NAV_DROPDOWN_ICON"></i>
                </a>
                <div class="NAV_DROPDOWN_CONTENT">
                    <a href="vendor_bookings_approval.php"><i class="fas fa-calendar-alt"></i> <span id="ITALIC">Pending Bookings</span></a>
                    <a href="vendor_bookings_active.php"><i class="far fa-calendar-check"></i> <span id="ITALIC">Scheduled Bookings</span></a>
                    <a href="vendor_bookings_completed.php"><i class="fas fa-calendar-check"></i> <span id="ITALIC">Completed Bookings</span></a>
                    <a href="vendor_bookings_cancelled.php"><i class="fas fa-calendar-times"></i> <span id="ITALIC">Cancelled Bookings</span></a>
                </div>
            </div>
            
            <a href="vendor_package.php"><i class="fa fa-fw fa-store"></i> Packages</a>
            
            <a href="vendor_clients.php" class="NAV_ACTIVE"><i class="fas fa-users"></i> <span>Clients</span></a>
            
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="vendor_profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="vendor_help.php"><i class="fas fa-question-circle"></i> Help</a>
            <a href="logout.php" class="LOGOUT"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Clients</h1>
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

            <!-- Clients Table -->
            <div class="CUSTOMERS_CONTAINER">
                <header class="CUSTOMERS_HEADER">
                    <h2>Clients Management</h2>
                </header>
            </div> 

            <div class="CUSTOMER_TABLE">
                <table>
                    <thead>
                        <tr>
                            <th id="PROFILE_PIC_HEADER"><i class="fas fa-image"></i> Profile</th>
                            <th><i class="fas fa-user"></i> Client Name</th>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <th><i class="fas fa-phone"></i> Mobile</th>
                            <th><i class="fas fa-exchange-alt"></i> Transactions</th>
                            <th><i class="fas fa-history"></i> History</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($clients)): ?>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td id="PROFILE_PIC_CELL">
                                        <img src="<?php echo htmlspecialchars($client['profile_picture'] ?? 'assets/images/default_profile.jpg'); ?>" alt="Profile Picture" class="PROFILE_PIC">
                                    </td>
                                    <td class="CLIENT_NAME"><?php echo htmlspecialchars($client['name']); ?></td>
                                    <td class="CLIENT_EMAIL"><?php echo htmlspecialchars($client['email']); ?></td>
                                    <td class="MOBILE_NUMBER"><?php echo htmlspecialchars($client['mobile_number']); ?></td>
                                    <td><span class="TRANSACTION_COUNT"><?php echo $client['transaction_count']; ?></span></td>
                                    <td class="ACTION_BUTTONS">
                                        <a href="vendor_clients_history.php?id=<?php echo $client['client_id']; ?>" class="VIEW_BUTTON">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No clients found with transaction history</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>