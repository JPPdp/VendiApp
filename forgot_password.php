<?php
include 'db_connect.php';
session_start();

$message = "";
$show_otp_form = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['request_otp'])) {
        // Request OTP
        $email = trim($_POST['email']);
        $user_type = $_POST['user_type'];
        
        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_user_type'] = $user_type;
        $_SESSION['otp_expires'] = time() + 300; // 5 minutes
        
        $message = "OTP: $otp";
        $show_otp_form = true;
        
    } elseif (isset($_POST['verify_otp'])) {
        // Verify OTP and reset password
        $user_otp = $_POST['otp'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($user_otp !== $_SESSION['reset_otp']) {
            $message = "Invalid OTP.";
        } elseif (time() > $_SESSION['otp_expires']) {
            $message = "OTP expired. Request a new one.";
        } elseif ($new_password != $confirm_password) {
            $message = "Passwords don't match.";
        } elseif (strlen($new_password) < 8) {
            $message = "Password must be 8+ characters.";
        } else {
            // Update password
            $table = ($_SESSION['reset_user_type'] == "admin") ? "admins" : "vendors";
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $sql = "UPDATE $table SET password = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $hashed_password, $_SESSION['reset_email']);
            
            if ($stmt->execute()) {
                $message = "Password reset successfully! <a href='login.php'>Login</a> with your new password.";
                session_destroy();
                session_start();
            } else {
                $message = "Error resetting password.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset | Vendi</title>
    <link rel="icon" href="assets/images/VendiEnhanced.png" type="image/icon type">
    <link rel="stylesheet" href="login.css?v=<?php echo date('his'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="CONTAINER">
    <div class="LEFT_SECTION">
        <div class="LOGO">
            <div class="LOGO_NAME">Vendi.</div>
        </div>
        <div class="VECTOR_ART">
            <img src="assets/images/Visualizing_Vector.png" alt="Vector_Art">
        </div>
        <p>Reset your password.</p>
    </div>

    <div class="RIGHT_SECTION">

        <!-- REQUEST OTP FORM -->
        <form class="LOGIN_FORM" method="post">
            <h2>RESET PASSWORD</h2>
            <p>Enter your email to receive an OTP.</p>
            
            <?php if ($message): ?>
            <div class="<?= strpos($message, 'successfully') !== false ? 'GREEN_ALERT' : 'RED_ALERT' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if (!$show_otp_form): ?>

            <h2>RESET</h2>
            <label>Email</label>
            <input type="email" name="email" placeholder="Your email" required>

            <label>User Type</label>
            <select name="user_type" required>
                <option value="" disabled selected>Select account type</option>
                <option value="admin">Admin</option>
                <option value="vendor">Vendor</option>
            </select>

            <button type="submit" name="request_otp">Send OTP</button>
            
            <div class="LOGIN">Back to Login</div>
            <div class="LOGIN_LINK">
                <a href="login.php"><i class="fas fa-arrow-left"></i> Go Back</a>
            </div>
        </form>
        <?php else: ?>
        <!-- VERIFY OTP FORM -->
        <form class="LOGIN_FORM" method="post">
            <h2>ENTER OTP</h2>
            <p>Enter the 6-digit OTP and new password.</p>

            <h2>RESET</h2>
            <label>OTP Code</label>
            <input type="text" name="otp" placeholder="6-digit code" required pattern="\d{6}">

            <div class="BESIDE_FIELDS">
                <div class="BESIDE_FIELD">
                    <label>New Password</label>
                    <div class="PASSWORD_CONTAINER">
                        <input type="password" name="new_password" placeholder="New password" required minlength="8">
                        <i class="fas fa-eye PASSWORD_TOGGLE"></i>
                    </div>
                </div>

                <div class="BESIDE_FIELD">
                    <label>Confirm Password</label>
                    <div class="PASSWORD_CONTAINER">
                        <input type="password" name="confirm_password" placeholder="Confirm password" required minlength="8">
                        <i class="fas fa-eye PASSWORD_TOGGLE"></i>
                    </div>
                </div>
            </div>
            <button type="submit" name="verify_otp">Reset Password</button>
            
            <div class="LOGIN">Back to Login</div>
            <div class="LOGIN_LINK">
                <a href="login.php"><i class="fas fa-arrow-left"></i> Go Back</a>
            </div>
        </form>
        <?php endif; ?>

        <footer class="DASHBOARD_FOOTER">
            <div class="FOOTER_CONTENT">
                <span class="COPYRIGHT">&copy; 2025 GitRat</span>
            </div>
        </footer>
    </div>
</div>

<script  src="password.js"></script>
</body>
</html>