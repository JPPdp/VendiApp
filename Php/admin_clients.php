<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "admin") {
    header("Location: login.php");
    exit;
}

date_default_timezone_set('Asia/Manila');
// Get current hour for greeting
$currentHour = date('G');

// Determine the greeting based on the time
if ($currentHour >= 1 && $currentHour < 4) {
    $greeting = '🌙 Good Evening,';
} elseif ($currentHour < 12) {
    $greeting = '☀️ Good Morning,';
} elseif ($currentHour < 18) {
    $greeting = '🌤️ Good Afternoon,';
} else {
    $greeting = '🌙 Good Evening,';
}

// Handle client deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_client'])) {
    $client_id = $_POST['client_id'];
    
    $sql = "DELETE FROM clients WHERE client_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $client_id);

    if ($stmt->execute()) {
        $message = "Client has been deleted successfully.";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Get list of clients
$sql = "SELECT * FROM clients";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients | Vendi</title>
    <link rel="icon" href="/assets/images/VendiBLK_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href="admin_vendors_active.php"><i class="fas fa-user-tie"></i> Vendors <span id="ITALIC">(Active)</span></a>
        <a href="admin_vendors_approval.php"><i class="fas fa-user-check"></i> Vendors <span id="ITALIC">(Pending)</span></a>
        <a href="admin_vendors_denied.php"><i class="fas fa-user-times"></i> <span>Vendors <span id="ITALIC">(Denied)</span></span></a>
        <a href="#" class="NAV_ACTIVE"><i class="fas fa-users"></i> <span>Clients</span></a>
        <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
        <div class="MENU_HEADER">SETTINGS</div>
        <a href="admin_profile.php"><i class="fa fa-fw fa-user"></i> <span>Profile</span></a>            
        <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
    </div>
    
    <!-- Dashboard Content -->
    <div class="DASHBOARD" id="DASHBOARD">
        <div class="UPPER">
            <div class="LEFT_UPPER">
                <h1 class="DASHBOARD_TITLE">Clients</h1>
            </div>

            <?php if (isset($message)): ?>
                <div class="ALERT_MESSAGE"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="RIGHT_UPPER">
                <div class="ACCOUNT">
                    <span class="HELLO"><?php echo $greeting; ?></span>
                    <a href="profile.php">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture" class="PROFILE_PIC">
                    </a>    
                    <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</span>             
                    </div>
            </div>
        </div>
        
        <!-- Client Details Table -->
        <div class="BOOKINGS_CONTAINER">
            <header class="BOOKINGS_HEADER">
                <h2>Active Clients</h2>
            </header>
        </div>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="BOOKING_TABLE">
                    <table>
                        <thead>
                            <tr>
                                <th>Profile Picture</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($client = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($client['profile_picture']); ?>" alt="Profile Picture" class="CLIENT_PROFILE_PIC">
                                    </td>
                                    <td><b><?php echo htmlspecialchars($client['name']); ?></b></td>
                                    <td><?php echo htmlspecialchars($client['email']); ?></td>
                                    <td><?php echo htmlspecialchars($client['mobile_number']); ?></td>
                                    <td><?php echo htmlspecialchars($client['address']); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="client_id" value="<?php echo $client['client_id']; ?>">
                                            <button type="submit" name="delete_client" class="ACTION_BUTTON DELETE_BUTTON" onclick="return confirm('Are you sure you want to delete this client?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="BOOKING_TABLE">
                    <td>No active clients <span id="ITALIC">(app)</span> found.</td>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="dashboard.js"></script>
</body>
</html>