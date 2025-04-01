<?php
// Database and Session Initialization
include 'db_connect.php';
session_start();

// Authentication Check
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

// Vendor Data Fetch
$sql = "SELECT * FROM vendors WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

// Dashboard Statistics - Booking Statistics
$stats = [];
$sql = "SELECT COUNT(*) as count FROM bookings WHERE vendor_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$stats['pending_bookings'] = $result->fetch_assoc()['count'];

$sql = "SELECT COUNT(*) as count FROM bookings WHERE vendor_id = ? AND status = 'Approved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$stats['scheduled_bookings'] = $result->fetch_assoc()['count'];

$sql = "SELECT COUNT(*) as count FROM bookings WHERE vendor_id = ? AND status = 'Completed'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$stats['completed_bookings'] = $result->fetch_assoc()['count'];

$sql = "SELECT COUNT(*) as count FROM bookings WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$stats['total_bookings'] = $result->fetch_assoc()['count'];

// Recent Bookings Data
$sql = "SELECT b.*, c.name as client_name, c.profile_picture as client_picture, 
               vp.package_name, vp.price
        FROM bookings b
        JOIN clients c ON b.client_id = c.client_id
        JOIN vendor_packages vp ON b.package_id = vp.package_id
        WHERE b.vendor_id = ?
        ORDER BY b.booking_id DESC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$recent_bookings = $stmt->get_result();

// Booking Status Breakdown
$status_counts = [
    'Pending' => 0,
    'Approved' => 0,
    'Completed' => 0,
    'Cancelled' => 0
];

$sql = "SELECT status, COUNT(*) as count FROM bookings WHERE vendor_id = ? GROUP BY status";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (array_key_exists($row['status'], $status_counts)) {
            $status_counts[$row['status']] = (int)$row['count'];
        }
    }
}

$total_bookings = array_sum($status_counts);

// Active Bookings Data (Pending and Approved)
$sql = "SELECT b.*, c.name as client_name, c.email, c.mobile_number, 
               vp.package_name, vp.price
        FROM bookings b
        JOIN clients c ON b.client_id = c.client_id
        JOIN vendor_packages vp ON b.package_id = vp.package_id
        WHERE b.vendor_id = ? AND b.status IN ('Approved', 'Pending')
        ORDER BY b.booking_id DESC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$active_bookings = $stmt->get_result();

// Handle vendor task submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_vendor_task'])) {
    $task = trim($_POST['vendor_task']);
    if (!empty($task)) {
        $stmt = $conn->prepare("INSERT INTO vendor_todos (vendor_id, task) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['user_id'], $task);
        
        if ($stmt->execute()) {
            header("Location: vendor_dashboard.php");
            exit();
        } else {
            $vendor_todo_error = "Error adding task: " . $conn->error;
        }
    } else {
        $vendor_todo_error = "Task cannot be empty";
    }
}

// Handle vendor task deletion
if (isset($_GET['delete_vendor_task'])) {
    $task_id = (int)$_GET['delete_vendor_task'];
    $stmt = $conn->prepare("DELETE FROM vendor_todos WHERE id = ? AND vendor_id = ?");
    $stmt->bind_param("ii", $task_id, $_SESSION['user_id']);
    $stmt->execute();
    header("Location: vendor_dashboard.php");
    exit();
}

// Fetch vendor's tasks
$vendor_todos = [];
$sql = "SELECT * FROM vendor_todos WHERE vendor_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$vendor_todos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard | Vendi</title>
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
            <!-- Logo and Menu Items -->
            <div class="LOGO">
                <img src="assets/images/Vendi_Icon.png" alt="Logo Icon" class="LOGO_ICON">
                <div class="LOGO_NAME">Vendi
                <span id="VENDORS">VENDORS</span>
                </div>
            </div>

            <div class="MENU_HEADER">MANAGEMENT</div>
            <a href="#DASHBOARD" class="NAV_ACTIVE"><i class="fas fa-stream"></i> <span>Dashboard</span></a>
            
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
            <a href="vendor_clients.php"><i class="fas fa-users"></i> Clients</a>
            
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="vendor_profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="vendor_help.php"><i class="fas fa-question-circle"></i> Help</a>
            <label for="LOGOUT_MODAL_TOGGLE" class="LOGOUT">
                <i class="fa fa-fw fa-sign-out-alt"></i> Log Out
            </label>
        </div>
        
        <!-- Main Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <!-- Dashboard Header -->
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Vendor Dashboard</h1>
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
            
            <!-- Stats Boxes -->
            <div class="CONTENT">
                <div class="BOX PENDING">
                    <h3><i class="fas fa-calendar-alt"></i> Pending Bookings</h3>
                    <p><?php echo $stats['pending_bookings']; ?></p>
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="BOX SCHEDULED">
                    <h3><i class="far fa-calendar-check"></i> Scheduled Bookings</h3>
                    <p><?php echo $stats['scheduled_bookings']; ?></p>
                    <i class="far fa-calendar-check"></i>
                </div>
                <div class="BOX COMPLETED">
                    <h3><i class="fas fa-calendar-check"></i> Completed Bookings</h3>
                    <p><?php echo $stats['completed_bookings']; ?></p>
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="BOX CANCELLED">
                    <h3><i class="fas fa-calendar-times"></i> Total Bookings</h3>
                    <p><?php echo $stats['total_bookings']; ?></p>
                    <i class="fas fa-calendar-times"></i>
                </div>
            </div>

            <!-- Main Content Container -->
            <div class="MAIN_CONTAINER">
                <!-- Left Column -->
                <div class="LEFT_MAIN">
                    <!-- Recent Bookings and Booking Chart -->
                    <div class="FLEX_CONTAINER">
                        <!-- Recent Bookings Table -->
                        <div class="PACKAGE_OVERVIEW">
                            <h2>Recent Bookings 
                            <a href="vendor_bookings_approval.php"><i class="DIRECT fas fa-angle-right"></i></a>
                            </h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-user"></i> Client</th>
                                        <th><i class="fas fa-box"></i> Package</th>
                                        <th><i class="fas fa-calendar-alt"></i> Date</th>
                                        <th><i class="fas fa-peso-sign"></i> Price</th>
                                        <th><i class="fas fa-info-circle"></i> Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recent_bookings->num_rows > 0): ?>
                                        <?php while ($booking = $recent_bookings->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <b><?php echo htmlspecialchars($booking['client_name']); ?></b>
                                                </td>
                                                <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                                                <td>
                                                    <?php echo date('M d, Y', strtotime($booking['service_date'])); ?> <br>
                                                    <small><?php echo date('h:i A', strtotime($booking['service_time'])); ?></small>
                                                </td>
                                                
                                                <td>₱<?php echo number_format($booking['price'], 2); ?></td>
                                                <td>
                                                    <span class="status-badge <?php echo strtolower(htmlspecialchars($booking['status'])); ?>">
                                                        <?php echo htmlspecialchars($booking['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5">No recent bookings found</td></tr>
                                    <?php endif; ?>
                                </tbody>   
                            </table>  
                        </div>

                        <!-- Booking Status Chart -->
                        <div class="VENDOR_CHART">
                            <h2>Bookings Overview</h2>
                            <div class="CHART_CONTAINER">
                                <?php foreach ($status_counts as $status => $count): 
                                    $percentage = $total_bookings > 0 ? ($count / $total_bookings) * 100 : 0;
                                    $gradient = '';
                                    switch($status) {
                                        case 'Pending': 
                                            $gradient = 'linear-gradient(to top, #f7b500, #f77b00)'; 
                                            break;
                                        case 'Approved': 
                                            $gradient = 'linear-gradient(to top, #00f70c, #009c31)'; 
                                            break;
                                        case 'Completed': 
                                            $gradient = 'linear-gradient(to top, #00b4f7, #0055f7)'; 
                                            break;
                                        case 'Cancelled': 
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
                                <div class="legend-item"><span class="legend-color" style="background: var(--gradient_Blue) "></span><span class="legend-text">Completed</span></div>
                                <div class="legend-item"><span class="legend-color" style="background: var(--gradient_Red);"></span><span class="legend-text">Cancelled</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Bookings Summary Table -->
                    <div class="BOOKING_TABLE">
                        <h2>Active Bookings<a href="vendor_bookings_active.php"><i class="DIRECT fas fa-angle-right"></i></a></h2>
                        <table>
                            <thead>
                                <tr>
                                    <th><i class="fas fa-user"></i> Client</th>
                                    <th><i class="fas fa-envelope"></i> Email</th>
                                    <th><i class="fas fa-phone"></i> Contact</th>
                                    <th><i class="fas fa-box"></i> Package</th>
                                    <th><i class="fas fa-calendar-alt"></i> Date & Time</th>
                                    <th><i class="fas fa-map-marker-alt"></i> Location</th>
                                    <th><i class="fas fa-info-circle"></i> Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($active_bookings->num_rows > 0): ?>
                                    <?php while ($booking = $active_bookings->fetch_assoc()): ?>
                                        <tr>
                                            <td><b><?php echo htmlspecialchars($booking['client_name']); ?></b></td>
                                            <td><?php echo htmlspecialchars($booking['email']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['mobile_number']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                                            <td>
                                                <?php echo date('M d, Y', strtotime($booking['service_date'])); ?><br>
                                                <small><?php echo date('h:i A', strtotime($booking['service_time'])); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($booking['booking_location']); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo strtolower(htmlspecialchars($booking['status'])); ?>">
                                                    <?php echo htmlspecialchars($booking['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="7">No active bookings found</td></tr>
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
                            <h2>Vendor Tasks</h2>
                            <button class="ADD_TASK_BTN" onclick="document.getElementById('todoModal').style.display='block'">
                                <i class="fas fa-plus"></i> Add Task
                            </button>
                        </div>
                        
                        <?php if (!empty($vendor_todo_error)): ?>
                            <div class="alert alert-error"><?php echo $vendor_todo_error; ?></div>
                        <?php endif; ?>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th class="ACTION_WIDTH">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($vendor_todos)): ?>
                                    <?php foreach ($vendor_todos as $todo): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($todo['task']); ?></td>
                                            <td id="TODO_ACTIONS" class="ACTION_WIDTH">
                                                <a href="vendor_dashboard.php?delete_vendor_task=<?php echo $todo['id']; ?>" 
                                                class="DELETE_TASK"
                                                onclick="return confirm('Delete this task?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2">No vendor tasks yet</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Todo Modal Popup -->
                <div id="todoModal" class="MODAL">
                    <div class="MODAL_CONTENT">
                        <div class="MODAL_HEADER">
                            <h3>Add New Task</h3>
                            <span class="MODAL_CLOSE" onclick="document.getElementById('todoModal').style.display='none'">&times;</span>
                        </div>
                        <form method="POST" class="MODAL_BODY">
                            <textarea name="vendor_task" placeholder="Enter your task..." required id="VENDOR_TASK"></textarea>
                            <button type="submit" name="add_vendor_task" class="MODAL_SUBMIT">
                                <i class="fas fa-plus"></i> Add Task
                            </button>
                        </form>
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