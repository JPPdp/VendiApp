<?php
include 'db_connect.php';
session_start();

// Check if vendor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];
$message = "";

// Fetch vendor details
$sql = "SELECT * FROM vendors WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if (!password_verify($current_password, $vendor['password'])) {
        $message = "Current password is incorrect.";
    } elseif ($new_password != $confirm_password) {
        $message = "New passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $message = "Password must be at least 8 characters long.";
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE vendors SET password = ? WHERE vendor_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $hashed_password, $vendor_id);
        
        if ($update_stmt->execute()) {
            $message = "Password changed successfully!";
            // Clear form fields
            $_POST = array();
        } else {
            $message = "Error updating password: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | Vendi</title>
    <link rel="icon" href="assets/images/VendiEnhanced.png" type="image/icon type">
    <link rel="stylesheet" href="login.css?v=<?php echo date('his'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="CONTAINER">
    <!-- LEFT SECTION -->
    <div class="LEFT_SECTION">
        <div class="LOGO">
            <div class="LOGO_NAME">Vendi.</div>
        </div>
        <div class="VECTOR_ART">
            <img src="assets/images/Visualizing_Vector.png" alt="Vector_Art">
        </div>
        <p>Secure your account.</p>
    </div>

    <!-- RIGHT SECTION -->
    <div class="RIGHT_SECTION">
        <!-- CHANGE PASSWORD FORM -->
        <form id="CHANGE_PASSWORD_FORM" class="LOGIN_FORM" method="post" action="">
            <h2>CHANGE PASSWORD</h2>
            <p> To change your password, please enter your current password and then your new password. </p>

            <?php if ($message): ?>
                <div class="<?php echo strpos($message, 'successfully') !== false ? 'GREEN_ALERT' : 'RED_ALERT'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="BESIDE_FIELD">
                <div class="PASSWORD_CONTAINER">
                    <label for="CURRENT_PASSWORD">Current Password</label>
                    <input type="password" id="CURRENT_PASSWORD" name="current_password" placeholder="Enter current password" required>
                    <i class="fas fa-eye PASSWORD_TOGGLE" id="current-password-toggle"></i>
                </div>
            </div>
            
            <div class="BESIDE_FIELD">
                <div class="PASSWORD_CONTAINER">
                    <label for="NEW_PASSWORD">New Password</label>
                    <input type="password" id="NEW_PASSWORD" name="new_password" placeholder="Enter new password" required minlength="8">
                    <i class="fas fa-eye PASSWORD_TOGGLE" id="new-password-toggle"></i>
                </div>
            </div>
            
            <div class="BESIDE_FIELD">
                <div class="PASSWORD_CONTAINER">
                    <label for="CONFIRM_PASSWORD">Confirm New Password</label>
                    <input type="password" id="CONFIRM_PASSWORD" name="confirm_password" placeholder="Confirm new password" required minlength="8">
                    <i class="fas fa-eye PASSWORD_TOGGLE" id="confirm-password-toggle"></i>
                </div>
            </div>

            <button type="submit">Change Password</button>

            <div class="LOGIN">Back to Profile?</div>
            <div class="LOGIN_LINK">
                <a href="vendor_profile.php"> Go Back</a>
            </div>
        </form>
        
        <footer class="DASHBOARD_FOOTER">
            <div class="FOOTER_CONTENT">
                <span class="COPYRIGHT">&copy; 2025 GitRat</span>
            </div>
        </footer>
    </div>
</div>

<script src="password.js"></script>
</body>
</html>