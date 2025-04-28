<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


function validatePassword($password) {
    // Check length
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long.";
    }
    
    // Check for symbols/special characters
    if (preg_match('/[^a-zA-Z0-9]/', $password)) {
        return "Password must not contain any symbols or special characters.";
    }
    
    // Check for spaces
    if (strpos($password, ' ') !== false) {
        return "Password must not contain spaces.";
    }
    
    return true;
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $business_name = $_POST['business_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $mobile_number = $_POST['mobile_number'];
    $address = $_POST['address'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Validate business name uniqueness
        $check_business_name = $conn->query("SELECT business_name FROM vendors WHERE business_name = '$business_name'");
        if ($check_business_name->num_rows > 0) {
            $error_message = "Business name already exists. Please choose another business name.";
        } else {
            // Validate email uniqueness
            $check_email = $conn->query("SELECT email FROM vendors WHERE email = '$email'");
            if ($check_email->num_rows > 0) {
                $error_message = "Email address already exists. Please use a different email.";
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
                        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                        // Store data in session
                        $_SESSION['reg_data'] = [
                            'business_name' => $business_name,
                            'email' => $email,
                            'password' => $hashed_password,
                            'mobile_number' => $mobile_number,
                            'address' => $address
                        ];
                        
                        // Redirect to step 2
                        header("Location: register_vendor2.php");
                        exit();
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
    <title>Sign Up | Vendi</title>
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
            <a href="index.htm" class="BACK_TO_WEBSITE">
                Back to Website <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="VECTOR_ART">
            <img src="assets/images/Event_Vector.png" alt="Vector_Art">
        </div>
        <p>Collaborate with event organizers.</p>
    </div>
    
    <div class="RIGHT_SECTION">
        <div class="REGISTRATION_STEPS">
            <div class="STEP active">
                <span>Account Information</span>
            </div>
            <div class="STEP">
                <span>Business Information</span>
            </div>
            <div class="STEP">
                <span>Confirmation</span>
            </div>
        </div>
        
        <form id="VENDOR_FORM" class="LOGIN_FORM" method="post" action="">
            <h2>CONNECT WITH EVENT PLANNERS</h2>
            <p>Welcome to <span id="VENDI">Vendi</span>! Create your vendor account to showcase your products and services, 
            organize your schedule and maximize your event bookings with our dashboard.</p>

                        <!-- Display error message if any -->
            <?php if (!empty($error_message)): ?>
                <div class="RED_ALERT"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <h2>SIGN UP</h2>
            
            <!-- Business Name -->
            <label for="VENDOR_USERNAME">Business Name <span id="REQUIRED">*</span></label>
            <input type="text" id="businessname" name="business_name" placeholder="Enter Business Name" required>

            <div class="BESIDE_FIELDS">
                <div class="BESIDE_FIELD">
                    <!-- Email -->
                    <label for="VENDOR_EMAIL">Email <span id="REQUIRED">*</span></label>
                    <input type="email" id="VENDOR_EMAIL" name="email" placeholder="Enter Email Address" required>
                </div>

                <div class="BESIDE_FIELD">
                    <!-- Mobile Number -->
                    <label for="VENDOR_MOBILE">Mobile Number <span id="REQUIRED">*</span></label>
                    <span id="PHL">+63</span>
                    <input type="tel" id="VENDOR_MOBILE" name="mobile_number" placeholder="Enter Mobile Number" required minlength="10" maxlength="10">
                </div>
            </div>

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
                                <li>Must <strong>not contain any symbols or special characters</strong></li>
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

            <div class="BESIDE_FIELD">
                <label for="ADDRESS">Address <span id="REQUIRED">*</span></label> 
                <input type="text" id="ADDRESS" name="address" placeholder="Enter complete address" required>
            </div>

            <button type="submit" name="next">Next</button>

            <div class="LOGIN">Already have an account?</div>
            <div class="LOGIN_LINK">
                <a href="login.php">Log In</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>