<?php
// Password validation function (simplified for admin)
function validatePassword($password) {
    // Check if the password is at least 8 characters long
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long.";
    }

    // Check for spaces
    if (preg_match('/\s/', $password)) {
        return "Password cannot contain spaces.";
    }

    // If all checks pass
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $adminname = htmlspecialchars($_POST['adminname']);
    
    // Sanitize and validate email
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    
    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

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

                // Connect to the database
                $conn = new mysqli("localhost", "root", "", "janrich_db");

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Check if the adminname or email already exists
                $stmt = $conn->prepare("SELECT admin_id FROM admin_console WHERE admin_name = ? OR admin_email = ?");
                $stmt->bind_param("ss", $adminname, $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error_message = "Admin name or email already exists. Please choose another.";
                } else {
                    $stmt->close();
                    // Insert new admin into the database
                    if ($stmt = $conn->prepare("INSERT INTO admin_console (admin_name, admin_email, password) VALUES (?, ?, ?)")) {
                        $stmt->bind_param("sss", $adminname, $email, $hashed_password);
                        if ($stmt->execute()) {
                            // Redirect to admin dashboard after successful registration
                            header("Location: login.php?registration=success");
                            exit();
                        } else {
                            $error_message = "Registration failed. Please try again.";
                        }
                    }
                }

                $stmt->close();
                $conn->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="login.css?v=<?php echo time(); ?>">
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
            <img src="assets/images/Teamwork_Vector.png" alt="Vector_Art">
        </div>
        <p>Manage and oversee.</p>
    </div>

    <div class="RIGHT_SECTION">
        <!-- ADMIN SIGN-UP FORM -->
        <form id="ADMIN_FORM" class="LOGIN_FORM" method="post" action="">
            <h2>ADMINISTRATOR SIGN UP</h2>
            <p>Welcome to <span id="VENDI">Vendi</span>! Create your admin account to manage vendors, monitor events, and oversee the platform.</p>

            <!-- Display error message if any -->
            <?php if (!empty($error_message)): ?>
                <div class="RED_ALERT"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <h2>SIGN UP</h2>
            
            <!-- Admin Name -->
            <label for="ADMIN_USERNAME">Admin Name <span id="REQUIRED">*</span></label>
            <input type="text" id="adminname" name="adminname" placeholder="Enter Admin Name" required>

            <!-- Email -->
            <label for="ADMIN_EMAIL">Email <span id="REQUIRED">*</span></label>
            <input type="email" id="ADMIN_EMAIL" name="email" placeholder="Enter Email Address" required>

            <!-- Password -->
            <div class="BESIDE_FIELDS">
                <div class="BESIDE_FIELD">
                    <label for="PASSWORD">Password <span id="REQUIRED">*</span></label>
                    <div class="PASSWORD_CONTAINER">
                        <input type="password" id="PASSWORD" name="password" placeholder="Enter Password" required minlength="8">
                        <i class="fas fa-eye PASSWORD_TOGGLE" id="password-toggle"></i>
                        <div id="PASSWORD_REQUIREMENTS" class="PASSWORD_REQUIREMENTS_DROPDOWN">
                            <span>Password must meet the following requirements:</span>
                            <ul>
                                <li>At least <strong>8 characters</strong> long</li>
                                <li>No spaces allowed</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="BESIDE_FIELD">
                    <label for="CONFIRM_PASSWORD">Confirm Password <span id="REQUIRED">*</span></label>
                    <div class="PASSWORD_CONTAINER">
                        <input type="password" id="CONFIRM_PASSWORD" name="confirm_password" placeholder="Re-enter Password" required minlength="8">
                        <i class="fas fa-eye PASSWORD_TOGGLE" id="confirm-password-toggle"></i>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit">Sign Up</button>

            <!-- Login Link -->
            <div class="LOGIN">Already have an account?</div>
            <div class="LOGIN_LINK">
                <a href="admin_login.php">Log In</a>
            </div>
        </form>
    </div>
</div>

<script src='password.js'></script>

</body>
</html>