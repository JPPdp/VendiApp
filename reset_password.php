<?php
// Password validation function (same as original)
function validatePassword($password) {
    if (strlen($password) < 8 || strlen($password) > 30) {
        return "Password must be Minimum of 8 characters and Maximum of 30 characters long.";
    }
    if (preg_match('/\s/', $password)) {
        return "Password cannot contain spaces.";
    }
    return true;
}

// Database connection details
$servername = "localhost"; // Replace with your server name
$username = "root";     // Replace with your database username
$password_db = ""; // Replace with your database password
$dbname = "janrich_db";       // Replace with your database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Validate password match
        if ($password !== $confirm_password) {
            $error_message = "Passwords do not match.";
        } else {
            // Validate password strength
            $passwordValidation = validatePassword($password);
            if ($passwordValidation !== true) {
                $error_message = $passwordValidation;
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Check if email exists in vendors table
                $sql_vendors = "SELECT vendors_email FROM vendors WHERE vendors_email = :email";
                $stmt_vendors = $conn->prepare($sql_vendors);
                $stmt_vendors->bindParam(':email', $email);
                $stmt_vendors->execute();

                // Check if email exists in admin_console table
                $sql_admin = "SELECT admin_email FROM admin_console WHERE admin_email = :email";
                $stmt_admin = $conn->prepare($sql_admin);
                $stmt_admin->bindParam(':email', $email);
                $stmt_admin->execute();

                if ($stmt_vendors->rowCount() > 0) {
                    // Update password in vendors table
                    $sql_update = "UPDATE vendors SET password = :password WHERE vendors_email = :email";
                } elseif ($stmt_admin->rowCount() > 0) {
                    // Update password in admin_console table
                    $sql_update = "UPDATE admin_console SET password = :password WHERE admin_email = :email";
                } else {
                    $error_message = "Email not found.";
                }

                if (empty($error_message)) {
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bindParam(':password', $hashed_password);
                    $stmt_update->bindParam(':email', $email);

                    if ($stmt_update->execute()) {
                        header("Location: login.php?password_reset=success");
                        exit();
                    } else {
                        $error_message = "Failed to update password. Please try again.";
                    }
                }
            }
        }
    }
}

// Close the connection
$conn = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reset Password | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="login.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="CONTAINER">
    <div class="LEFT_SECTION">
        <div class="LOGO">
            <div class="LOGO_NAME">Vendi.</div>
        </div>
        <div class="VECTOR_ART">
            <img src="assets/images/Event_Vector.png" alt="Vector_Art">
        </div>
        <p>Collaborate with event organizers.</p>
    </div>

    <div class="RIGHT_SECTION">
        <form id="VENDOR_FORM" class="LOGIN_FORM" method="post" action="" enctype="multipart/form-data">
            <h2>RESET PASSWORD</h2>
            <p>Enter your email address to reset your password</p>

            <?php if (!empty($error_message)): ?>
                <div class="RED_ALERT"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <label for="VENDOR_EMAIL">Email <span id="REQUIRED">*</span></label>
            <input type="email" id="VENDOR_EMAIL" name="email" placeholder="Enter Email Address" required>

            <div class="BESIDE_FIELDS">
                <div class="BESIDE_FIELD">
                    <label for="PASSWORD">New Password <span id="REQUIRED">*</span></label>
                    <div class="PASSWORD_CONTAINER">
                        <input type="password" id="PASSWORD" name="password" placeholder="Enter New Password" required minlength="8">
                        <i class="fas fa-eye PASSWORD_TOGGLE" id="password-toggle"></i>
                        <div id="PASSWORD_REQUIREMENTS" class="PASSWORD_REQUIREMENTS_DROPDOWN">
                            <span>Password must meet the following requirements:</span>
                            <ul>
                                <li>At least <strong>8 characters</strong> long and <strong>30 Characters</strong> long</li>
                                <li>No spaces allowed</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="BESIDE_FIELD">
                    <label for="CONFIRM_PASSWORD">Confirm Password <span id="REQUIRED">*</span></label>
                    <div class="PASSWORD_CONTAINER">
                        <input type="password" id="CONFIRM_PASSWORD" name="confirm_password" placeholder="Re-enter Password" required minlength="8">
                        <i class="fas fa-eye PASSWORD_TOGGLE" id="confirm-password-toggle"></i>
                    </div>
                </div>
            </div>

            <button type="submit">Reset Password</button>

            <div class="LOGIN">Remember your password?</div>
            <div class="LOGIN_LINK">
                <a href="login.php">Log In</a>
            </div>
        </form>
    </div>
</div>

<script src='password.js'></script>

</body>
</html>