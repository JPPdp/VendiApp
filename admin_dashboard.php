<?php
// Database and Session Initialization
include 'db_connect.php';
session_start();

// Authentication Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "admin") {
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

// Admin Data Fetch
$sql = "SELECT * FROM admins WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Dashboard Statistics
// Vendor Statistics
$stats = [];
$sql = "SELECT COUNT(*) as count FROM vendors WHERE status = 'Pending'";
$result = $conn->query($sql);
$stats['pending_vendors'] = $result->fetch_assoc()['count'];

$sql = "SELECT COUNT(*) as count FROM vendors WHERE status = 'Approved'";
$result = $conn->query($sql);
$stats['active_vendors'] = $result->fetch_assoc()['count'];

$sql = "SELECT COUNT(*) as count FROM vendors";
$result = $conn->query($sql);
$stats['total_vendors'] = $result->fetch_assoc()['count'];

// Client Statistics
$sql = "SELECT COUNT(*) as count FROM clients";
$result = $conn->query($sql);
$stats['total_clients'] = $result->fetch_assoc()['count'];

// Recent Data Fetch
$sql = "SELECT * FROM clients ORDER BY client_id DESC LIMIT 5";
$recent_clients = $conn->query($sql);

// Vendor Status Breakdown
$status_counts = [
    'Pending' => 0,
    'Approved' => 0,
    'Denied' => 0
];

$sql = "SELECT status, COUNT(*) as count FROM vendors GROUP BY status";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (array_key_exists($row['status'], $status_counts)) {
            $status_counts[$row['status']] = (int)$row['count'];
        }
    }
}

$total_vendors = array_sum($status_counts);

// Recent Vendors Data
$sql = "SELECT * FROM vendors WHERE status IN ('Approved', 'Pending') ORDER BY vendor_id DESC LIMIT 5";
$active_vendors = $conn->query($sql);

// Add this with your other database queries
// Fetch top 5 vendors by rating
$sql = "SELECT vendor_id, business_name, rating FROM vendors 
        WHERE status = 'Approved' AND rating > 0 
        ORDER BY rating DESC LIMIT 5";
$top_vendors = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Vendi</title>
    <link rel="icon" href="assets/images/VendiEnhanced.png" type="image/icon type">
    <link rel="stylesheet" href="bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="notifications.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <!-- Navigation Structure -->
    <div class="NAV_CONTAINER">
        <!-- Navigation Bar Content -->
        <div class="NAVIGATION_BAR">
            <div class="LOGO">
                <img src="assets/images/Vendi_Icon.png" alt="Logo Icon" class="LOGO_ICON">
                <div class="LOGO_NAME">Vendi
                <span>ADMIN</span>
                </div>
            </div>

            <div class="MENU_HEADER">ADMINISTRATION</div>
            <a href="#DASHBOARD" class="NAV_ACTIVE"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
            <a href="admin_vendors_active.php"><i class="fas fa-user-tie"></i> Vendors <span id="ITALIC">(Active)</span></a>
            <a href="admin_vendors_approval.php"><i class="fas fa-user-check"></i> Vendors <span id="ITALIC">(Pending)</span></a>
            <a href="admin_vendors_denied.php"><i class="fas fa-user-times"></i> <span>Vendors <span id="ITALIC">(Denied)</span></span></a>
            <a href="admin_clients.php"><i class="fas fa-users"></i> Client Management</a>
            <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
            
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="admin_profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <label for="LOGOUT_MODAL_TOGGLE" class="LOGOUT">
                <i class="fa fa-fw fa-sign-out-alt"></i> Log Out
            </label>
        </div>
        
        <!-- Main Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <!-- Dashboard Header -->
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE"><i class="fas fa-home"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?>'s Dashboard</h1>
                </div>
                    <div class="RIGHT_UPPER">
                        <div class="ACCOUNT">

                            
                            <span class="GREETING"><?php echo $greeting; ?></span>
                            <a href="admin_profile.php">
                                <img src="<?php echo htmlspecialchars($admin['profile_picture'] ?? 'assets/images/default_profile.jpg'); ?>" alt="Profile Picture" class="PROFILE_PIC">
                            </a>    
                            <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</span>             
                        </div>
                        <div class="NOTIFICATIONS_DROPDOWN">
                                <a href="admin_vendors_approval.php" class="NOTIFICATION_ICON">
                                    <i class="fas fa-bell"></i>
                                    <?php if ($stats['pending_vendors'] > 0): ?>
                                        <span class="NOTIFICATION_BADGE"><?php echo $stats['pending_vendors']; ?></span>
                                    <?php endif; ?>
                                </a>
                                <div class="NOTIFICATIONS_CONTENT">
                                    <h3>Pending Registrations</h3>
                                    <?php if ($stats['pending_vendors'] > 0): ?>
                                        <p>You have <?php echo $stats['pending_vendors']; ?> vendor(s) awaiting approval</p>
                                        <a href="admin_vendors_approval.php" class="VIEW_ALL">Review Now</a>
                                    <?php else: ?>
                                        <p>No pending vendors at this time</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                    </div>
            </div>
            
            <!-- Stats Boxes -->
            <div class="CONTENT">
                <div class="BOX PENDING">
                    <h3><i class="fas fa-user-clock"></i> Pending Vendors</h3>
                    <p><?php echo $stats['pending_vendors']; ?></p>
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="BOX SCHEDULED">
                    <h3><i class="fas fa-user-tie"></i> Approved Vendors</h3>
                    <p><?php echo $stats['active_vendors']; ?></p>
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="BOX COMPLETED">
                    <h3><i class="fas fa-user-tie"></i> Total Vendors</h3>
                    <p><?php echo $stats['total_vendors']; ?></p>
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="BOX CANCELLED">
                    <h3><i class="fas fa-users"></i> Total Clients</h3>
                    <p><?php echo $stats['total_clients']; ?></p>
                    <i class="fas fa-users"></i>
                </div>
            </div>

            <!-- Main Content Container -->
            <div class="MAIN_CONTAINER">
                <!-- Left Column -->
                <div class="LEFT_MAIN">
                    <!-- Recent Clients and Vendor Chart -->
                    <div class="FLEX_CONTAINER">
                        <!-- Recent Clients Table -->
                        <div class="PACKAGE_OVERVIEW">
                            <h2>Recent Clients 
                            <a href="admin_clients.php"><i class="DIRECT fas fa-angle-right"></i></a>
                            </h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th id="PROFILE_PIC_HEADER"><i class="fas fa-image"></i> Profile</th>
                                        <th><i class="fas fa-user"></i> Name</th>
                                        <th><i class="fas fa-envelope"></i> Email</th>
                                        <th><i class="fas fa-phone"></i> Mobile Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recent_clients->num_rows > 0): ?>
                                        <?php while ($client = $recent_clients->fetch_assoc()): ?>
                                            <tr>
                                                <td id="PROFILE_PIC_CELL"><img src="<?php echo htmlspecialchars($client['profile_picture']); ?>" alt="" class="CLIENT_PROFILE_PIC"></td>
                                                <td><b><?php echo htmlspecialchars($client['name']); ?></b></td>
                                                <td><?php echo htmlspecialchars($client['email']); ?></td>
                                                <td><?php echo htmlspecialchars($client['mobile_number']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4">No recent clients found</td></tr>
                                    <?php endif; ?>
                                </tbody>   
                            </table>  
                        </div>

                        <!-- Vendor Status Chart -->
                        <div class="VENDOR_CHART">
                            <h2>Vendor Status Overview</h2>
                            <div class="CHART_CONTAINER">
                                <?php foreach ($status_counts as $status => $count): 
                                    $percentage = $total_vendors > 0 ? ($count / $total_vendors) * 100 : 0;
                                    $gradient = '';
                                    switch($status) {
                                        case 'Pending': 
                                            $gradient = 'linear-gradient(to top, #f7b500, #f77b00)'; 
                                            break;
                                        case 'Approved': 
                                            $gradient = 'linear-gradient(to top, #00f70c, #009c31)'; 
                                            break;
                                        case 'Denied': 
                                            $gradient = 'linear-gradient(to top, #fc241d, #c40202)'; 
                                            break;
                                    }
                                ?>
                                <div class="chart-row">
                                    <div class="chart-label"><?php echo $status; ?></div>
                                    <div class="chart-bar-container">
                                        <div class="chart-bar" style="width: <?php echo $percentage; ?>%; background: <?php echo $gradient; ?>;">
                                            <span class="chart-value"><?php echo $count; ?> (<?php echo round($percentage); ?>%)</span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="chart-legend">
                                <div class="legend-item"><span class="legend-color" style="background: var(--gradient_Yellow)"></span><span class="legend-text">Pending</span></div>
                                <div class="legend-item"><span class="legend-color" style="background: var(--gradient_Green)"></span><span class="legend-text">Approved</span></div>
                                <div class="legend-item"><span class="legend-color" style="background: var(--gradient_Red);"></span><span class="legend-text">Denied</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Vendors Summary Table -->
                    <div class="BOOKING_TABLE">
                        <h2>Vendors Summary<a href="admin_vendors_approval.php"><i class="DIRECT fas fa-angle-right"></i></a></h2>
                        <table>
                            <thead>
                                <tr>
                                    <th><i class="fas fa-store"></i> Business</th>
                                    <th><i class="fas fa-envelope"></i> Email</th>
                                    <th><i class="fas fa-phone"></i> Contact</th>
                                    <th><i class="fas fa-map-marker-alt"></i> Address</th>
                                    <th><i class="fas fa-tag"></i> Service</th>
                                    <th><i class="fas fa-info-circle"></i> Features</th>
                                    <th><i class="fas fa-clipboard-check"></i> Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($active_vendors->num_rows > 0): ?>
                                    <?php while ($vendor = $active_vendors->fetch_assoc()): ?>
                                        <tr>
                                            <td><b><?php echo htmlspecialchars($vendor['business_name']); ?></b></td>
                                            <td><?php echo htmlspecialchars($vendor['email']); ?></td>
                                            <td><?php echo htmlspecialchars($vendor['mobile_number']); ?></td>
                                            <td><?php echo htmlspecialchars($vendor['address']); ?></td>
                                            <td><?php echo htmlspecialchars($vendor['service_option']); ?></td>
                                            <td><?php echo htmlspecialchars($vendor['business_description_short']); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo strtolower(htmlspecialchars($vendor['status'])); ?>">
                                                    <?php echo htmlspecialchars($vendor['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="7">No active vendors found</td></tr>
                                <?php endif; ?>
                            </tbody>   
                        </table>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="RIGHT_MAIN">
                    <!-- Calendar Widget -->
                    <div class="CALENDAR">
                        <h2>Calendar</h2>
                        <div class="CALENDAR_PLACEHOLDER">
                            <div class="WRAPPER">
                                <header>
                                    <div class="ICONS">
                                        <span id="PREV" class="ICON_CLASS"><i class="fa fa-caret-left"></i></span>
                                        <p class="CURRENT_DATE"></p>
                                        <span id="NEXT" class="ICON_CLASS"><i class="fa fa-caret-right"></i></span>
                                    </div>
                                </header>
                                <div class="CALENDAR_BODY">
                                    <ul class="WEEKS">
                                        <li>Sun</li><li>Mon</li><li>Tue</li><li>Wed</li><li>Thu</li><li>Fri</li><li>Sat</li>
                                    </ul>
                                    <ul class="DAYS"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Top Vendors Widget -->
                    <div class="VENDOR_RATINGS">
                        <div class="RATINGS_HEADER">
                            <h2>Top Performing Vendors</h2>
                        </div>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th><i class="fas fa-store"></i> Vendor</th>
                                    <th><i class="fas fa-star"></i> Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($top_vendors->num_rows > 0): ?>
                                    <?php while ($vendor = $top_vendors->fetch_assoc()): ?>
                                        <tr>
                                            <td> <b>
                                                <a href="admin_vendor_details.php?id=<?php echo $vendor['vendor_id']; ?>" 
                                                class="VENDOR_LINK">
                                                    <?php echo htmlspecialchars($vendor['business_name']); ?>
                                                </a></b>
                                            </td>
                                            <td class="STAR_RATING">
                                                <?php
                                                $rating = $vendor['rating'];
                                                $fullStars = floor($rating);
                                                $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                                $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                                
                                                // Full stars
                                                for ($i = 0; $i < $fullStars; $i++) {
                                                    echo '<i class="fas fa-star"></i>';
                                                }
                                                
                                                // Half star
                                                if ($hasHalfStar) {
                                                    echo '<i class="fas fa-star-half-alt"></i>';
                                                }
                                                
                                                // Empty stars
                                                for ($i = 0; $i < $emptyStars; $i++) {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                                
                                                // Numeric value
                                                echo '<span class="RATING_VALUE">'.number_format($rating, 1).'</span>';
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2">No rated vendors yet</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                <footer class="DASHBOARD_FOOTER">
                    <div class="FOOTER_CONTENT">
                        <span class="COPYRIGHT">&copy; 2025 GitRat</span>
                    </div>
                </footer>
        </div>
    </div>

    <input type="checkbox" id="LOGOUT_MODAL_TOGGLE" class="MODAL_TOGGLE">
    <div class="LOGOUT_MODAL">
        <div class="LOGOUT_MODAL_CONTENT">
            <h3>CONFIRM LOGOUT</h3>
            <p>Are you sure you want to log out?</p>
            <div class="BUTTON_ACTIONS">
                <a href="logout.php" class="BUTTON_CONFIRM">
                    <i class="fas fa-sign-out-alt"></i> LOG OUT
                </a>
                <label for="LOGOUT_MODAL_TOGGLE" class="BUTTON_CANCEL">
                    <i class="fas fa-times"></i> CANCEL
                </label>
            </div>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>