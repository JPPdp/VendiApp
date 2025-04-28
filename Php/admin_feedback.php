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
$currentHour = date('H');

// Greeting logic
if ($currentHour >= 1 && $currentHour < 4) {
    $greeting = '🌄 Good Dawn,';
} elseif ($currentHour >= 16 && $currentHour < 18.5) {
    $greeting = '🌅 Good Dusk,';
} elseif ($currentHour < 12) {
    $greeting = '☀️ Good Morning,';
} elseif ($currentHour < 18) {
    $greeting = '🌤️ Good Afternoon,';
} else {
    $greeting = '🌙 Good Evening,';
}

// Handle message actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['mark_resolved'])) {
        // Update message status if you have that column
        $message_id = $_POST['message_id'];
        $sql = "UPDATE messages SET status = 'Resolved' WHERE message_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $message_id);
        if ($stmt->execute()) {
            $message = "Message marked as resolved!";
        } else {
            $error = "Error updating message: " . $conn->error;
        }
    } 
    elseif (isset($_POST['delete_message'])) {
        $message_id = $_POST['message_id'];
        $sql = "DELETE FROM messages WHERE message_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $message_id);
        if ($stmt->execute()) {
            $message = "Message deleted successfully!";
        } else {
            $error = "Error deleting message: " . $conn->error;
        }
    }
}

// Get messages data
$messages_data = [];
try {
    $sql = "SELECT m.*, v.business_name, v.email 
            FROM messages m
            JOIN vendors v ON m.vendor_id = v.vendor_id
            WHERE m.admin_id = ? OR m.admin_id IS NULL
            ORDER BY m.sent_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $messages_data = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch (mysqli_sql_exception $e) {
    $error = "Messages system not available. Please try again later.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback | Vendi</title>
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
            <a href="admin_clients.php"><i class="fas fa-users"></i> Clients</a>
            <a href="admin_feedback.php" class="NAV_ACTIVE"><i class="fas fa-comment-dots"></i> <span> Feedback</span></a>
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="admin_profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
    </div>
    
    <!-- Dashboard Content -->
    <div class="DASHBOARD" id="DASHBOARD">
        <div class="UPPER">
            <div class="LEFT_UPPER">
                <h1 class="DASHBOARD_TITLE">Feedback</h1>
            </div>

            <?php if (isset($message)): ?>
                <div class="ALERT_MESSAGE success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="ALERT_MESSAGE error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="RIGHT_UPPER">
                <div class="ACCOUNT">
                    <span class="HELLO"><?php echo $greeting; ?></span>
                    <a href="admin_profile.php">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture" class="PROFILE_PIC">
                    </a>    
                    <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</span>             
                </div>
            </div>
        </div>
        
        <!-- Messages Table -->
        <div class="BOOKINGS_CONTAINER">
            <header class="BOOKINGS_HEADER">
                <h2>Vendor Messages</h2>
            </header>
        </div>

            <?php if (!empty($messages_data)): ?>
                <div class="BOOKING_TABLE">
                    <table>
                        <thead>
                            <tr>
                                <th>Business Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Sent At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages_data as $msg): ?>
                                <tr>
                                    <td><b><?php echo htmlspecialchars($msg['business_name']); ?></b></td>
                                    <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                    <td><?php echo htmlspecialchars($msg['message']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($msg['sent_at'])); ?></td>
                                    <td class="SENT_AT">
                                        <?php echo date('h:i A', strtotime($msg['sent_at'])); ?>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="message_id" value="<?php echo $msg['message_id']; ?>">
                                            <button type="submit" name="delete_message" class="ACTION_BUTTON DELETE_BUTTON" 
                                                    onclick="return confirm('Are you sure you want to delete this message?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="BOOKING_TABLE">
                    <p>No messages received yet.</p>
                </div>
            <?php endif; ?>
    </div>
</div>

<script src="dashboard.js"></script>
</body>
</html>