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
$client_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($client_id == 0) {
    header("Location: vendor_clients.php");
    exit();
}

// Fetch vendor details
$sql = "SELECT * FROM vendors WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

// Fetch client details
$sql = "SELECT * FROM clients WHERE client_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();

if (!$client) {
    header("Location: vendor_clients.php");
    exit();
}

// Fetch transaction history with this client
$sql = "SELECT * FROM client_transaction_history 
        WHERE client_id = ? AND vendor_id = ?
        ORDER BY transaction_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $client_id, $vendor_id);
$stmt->execute();
$transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client History | Vendi</title>
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
                    <a href="vendor_packages.php"><i class="fa fa-fw fa-store"></i> Packages</a>
                    <a href="vendor_bookings.php"><i class="fa fa-fw fa-calendar"></i> Bookings</a>
                    <a href="vendor_clients.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-users"></i> <span> Clients</span></a>
            <div class="MENU_HEADER">SETTINGS</div>
                    <a href="vendor_profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
                    <a href="help.php"><i class="fa fa-fw fa-question-circle"></i> Help</a>
                    <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE"><a href="vendor_clients.php" id="BREADCRUMB">Clients /</a> History: <?php echo htmlspecialchars($client['name']); ?></h1>
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

            <!-- Client Information Section -->
            <div class="CUSTOMERS_CONTAINER">
                <header class="CUSTOMERS_HEADER">
                    <h2>View Client Transaction History</h2>
                    <a href="vendor_clients.php" id="GO_BACK"><i class="fas fa-arrow-left"></i> Go Back</a>
                </header>
                
                <div class="CLIENT_INFO">
                    <div class="CLIENT_PROFILE">
                        <img src="<?php echo htmlspecialchars($client['profile_picture'] ?? 'assets/images/default_profile.jpg'); ?>" alt="Profile Picture" class="PROFILE_PIC">
                    </div>
                    <div class="CLIENT_DETAILS">
                        <h3><?php echo htmlspecialchars($client['name']); ?></h3>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($client['email']); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($client['mobile_number']); ?></p>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($client['address'] ?? 'Address not provided'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Transaction History Table -->
            <div class="CUSTOMERS_CONTAINER">
                <div class="CUSTOMER_TABLE">
                    <table>
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar-alt"></i> Date</th>
                                <th><i class="fas fa-info-circle"></i> Transaction Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($transactions)): ?>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?php echo date("F j, Y g:i A", strtotime($transaction['transaction_date'])); ?></td>
                                        <td class="TRANSACTION_DETAILS"><?php echo htmlspecialchars($transaction['transaction_details']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align: center;">No transaction history found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>