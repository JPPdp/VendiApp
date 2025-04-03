<?php
include 'db_connect.php';
session_start();

function validatePassword($password) {
    // Check length
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long.";
    }
    
    // Check for at least one special character and its position
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        return "Password must contain at least one special character.";
    } elseif (preg_match('/^[^a-zA-Z0-9]/', $password)) {
        return "Special characters are not allowed at the beginning of the password.";
    }
    
    // Check for spaces
    if (strpos($password, ' ') !== false) {
        return "Password must not contain spaces.";
    }
    
    return true;
}

$message = ""; // Variable to store messages

$sql = "SELECT * FROM admins WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($admin) {
    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['profile_picture'] = $admin['profile_picture'] ?: 'assets/images/default_profile.jpg';
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_email'] = $admin['email'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // Validate email uniqueness
        $stmt = $conn->prepare("SELECT email FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $check_email = $stmt->get_result();
        if ($check_email->num_rows > 0) {
            $message = "Email address already exists. Please use a different email.";
        } else {
            // Validate password match
            if ($password !== $confirm_password) {
                $message = "Passwords do not match.";
            } else {
                // Validate password strength
                $passwordValidation = validatePassword($password);
                if ($passwordValidation !== true) {
                    $message = $passwordValidation;
                } else {
                    // Hash the password
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                    // Insert Admin into Database
                    $sql = "INSERT INTO admins (name, email, password) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sss", $name, $email, $hashed_password);

                    if ($stmt->execute()) {
                        $message = "Admin Registered Successfully!";
                    } else {
                        $message = "Error: " . $conn->error;
                    }
                }
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
    <title> Admin Sign Up | Vendi </title>
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
            <img src="assets/images/Meeting_Vector.png" alt="Vector_Art">
        </div>
        <p>Manage and oversee.</p>
    </div>

    <!-- RIGHT SECTION -->
    <div class="RIGHT_SECTION">

        <!-- LOGIN FORM -->
        <form id="LOGIN_FORM" class="LOGIN_FORM" method="post" action="">
            <h2>ADMIN REGISTRATION</h2>
            <p> Welcome to <span id="VENDI">Vendi</span>! Register as an admin to manage the platform effectively. </p>

        <?php if ($message): ?>
            <div class="RED_ALERT"><?php echo $message; ?></div>
        <?php endif; ?>
        
            <h2>SIGN UP</h2>
            <label>Name <span id="REQUIRED">*</span></label>
            <input type="text" name="name" placeholder="Enter your name" required><br>

            <label for="email">Email <span id="REQUIRED">*</span></label>
            <input type="email" name="email" placeholder="Enter email" required><br>
        
            <div class="BESIDE_FIELDS">
                <div class="BESIDE_FIELD">
                    <label for="PASSWORD">Password <span id="REQUIRED">*</span></label>
                    <div class="PASSWORD_CONTAINER">
                        <input type="password" id="PASSWORD" name="password" placeholder="Enter Password" required minlength="8">
                        <i class="fas fa-eye PASSWORD_TOGGLE" id="password-toggle"></i>
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

            <button type="submit">Register</button>

            <div class="LOGIN">Already have an account?</div>
            <div class="LOGIN_LINK">
                <a href="login.php">Back to Log In</a>
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