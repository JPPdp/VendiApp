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

// Handle admin task submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_admin_task'])) {
    $task = trim($_POST['admin_task']);
    if (!empty($task)) {
        $stmt = $conn->prepare("INSERT INTO admin_todos (admin_id, task) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['user_id'], $task);
        
        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $admin_todo_error = "Error adding task: " . $conn->error;
        }
    } else {
        $admin_todo_error = "Task cannot be empty";
    }
}

// Handle admin task deletion
if (isset($_GET['delete_admin_task'])) {
    $task_id = (int)$_GET['delete_admin_task'];
    $stmt = $conn->prepare("DELETE FROM admin_todos WHERE id = ? AND admin_id = ?");
    $stmt->bind_param("ii", $task_id, $_SESSION['user_id']);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch admin's tasks
$admin_todos = [];
$sql = "SELECT * FROM admin_todos WHERE admin_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$admin_todos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK_NoBG.png" type="image/icon type">
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
            <!-- Logo and Menu Items -->
            <div class="LOGO">
                <div class="LOGO_NAME">Vendi <span>ADMIN</span></div>
            </div>

            <div class="MENU_HEADER">ADMINISTRATION</div>
            <a href="#DASHBOARD" class="NAV_ACTIVE"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
            <a href="admin_vendors_active.php"><i class="fas fa-user-tie"></i> Vendors <span id="ITALIC">(Active)</span></a>
            <a href="admin_vendors_approval.php"><i class="fas fa-user-check"></i> Vendors <span id="ITALIC">(Pending)</span></a>
            <a href="admin_vendors_denied.php"><i class="fas fa-user-times"></i> <span>Vendors <span id="ITALIC">(Denied)</span></span></a>
            <a href="admin_clients.php"><i class="fas fa-users"></i> Clients</a>
            <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
            
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="admin_profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Main Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <!-- Dashboard Header -->
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Admin Dashboard</h1>
                </div>
                <div class="RIGHT_UPPER">
                    <div class="ACCOUNT">
                        <span class="HELLO"><?php echo $greeting; ?></span>
                        <a href="admin_profile.php">
                            <img src="<?php echo htmlspecialchars($admin['profile_picture'] ?? 'assets/images/default_profile.jpg'); ?>" alt="Profile Picture" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</span>             
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
                                        <th><i class="fas fa-image"></i> Profile</th>
                                        <th><i class="fas fa-user"></i> Name</th>
                                        <th><i class="fas fa-envelope"></i> Email</th>
                                        <th><i class="fas fa-phone"></i> Mobile Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recent_clients->num_rows > 0): ?>
                                        <?php while ($client = $recent_clients->fetch_assoc()): ?>
                                            <tr>
                                                <td><img src="<?php echo htmlspecialchars($client['profile_picture']); ?>" alt="" class="CLIENT_PROFILE_PIC"></td>
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
                    
                    <!-- To-Do List Widget -->
                    <div class="TODO_LIST">
                        <div class="TODO_HEADER">
                            <h2>Admin Tasks</h2>
                            <form method="POST" class="ADD_TASK_FORM">
                                <input type="text" name="admin_task" placeholder="Add admin task..." required>
                                <button type="submit" name="add_admin_task" class="ADD_TASK">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </form>
                        </div>
                        
                        <?php if (!empty($admin_todo_error)): ?>
                            <div class="alert alert-error"><?php echo $admin_todo_error; ?></div>
                        <?php endif; ?>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($admin_todos)): ?>
                                    <?php foreach ($admin_todos as $todo): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($todo['task']); ?></td>
                                            <td>
                                                <a href="admin_dashboard.php?delete_admin_task=<?php echo $todo['id']; ?>" 
                                                class="DELETE_TASK"
                                                onclick="return confirm('Delete this task?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2">No admin tasks yet</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>