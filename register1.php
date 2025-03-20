<?php
// Password validation function
function validatePassword($password) {
    // Check if the password is at least 8 characters long
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long.";
    }

    // Check if the password contains any symbols or special characters
    if (preg_match('/[\W_]/', $password)) { // \W matches any non-word character, including symbols
        return "Password must not contain any symbols or special characters.";
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
    $businessname = htmlspecialchars($_POST['businessname']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $mobile = htmlspecialchars($_POST['mobile']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = htmlspecialchars($_POST['address']);
    $city_municipal = htmlspecialchars($_POST['city_municipal']);
    $province = htmlspecialchars($_POST['province']);

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

                // Store data in session and redirect to register2.php
                session_start();
                $_SESSION['registration_data'] = [
                    'businessname' => $businessname,
                    'email' => $email,
                    'mobile' => $mobile,
                    'password' => $hashed_password,
                    'address' => $address,
                    'city_municipal' => $city_municipal,
                    'province' => $province
                ];
                header("Location: register2.php");
                exit();
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
        <!-- VENDOR SIGN-UP FORM -->
        <form id="VENDOR_FORM" class="LOGIN_FORM" method="post" action="" enctype="multipart/form-data">
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
            <input type="text" id="businessname" name="businessname" placeholder="Enter Business Name" required>


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
                    <input type="tel" id="VENDOR_MOBILE" name="mobile" placeholder="Enter Mobile Number" required minlength="10" maxlength="10">
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

            <div class="BESIDE_FIELDS">
                <div class="BESIDE_FIELD">
                    <label for="ADDRESS">Address <span id="REQUIRED">*</span></label> 
                    <input type="text" id="ADDRESS" name="ADDRESS" placeholder="Enter complete address" required>
                </div>

                <div class="BESIDE_FIELD">
                    <!-- City/Municipality Dropdown -->
                    <label for="CITY">City/Municipality <span id="REQUIRED">*</span></label>
                    <select id="CITY" name="CITY" required>
                        <option value="" disabled selected>Select City/Municipality</option>
                        <option value="Dagupan">Dagupan</option>
                    </select>
                </div>

                <div class="BESIDE_FIELD">
                    <!-- Province Dropdown -->
                    <label for="PROVINCE">Province <span id="REQUIRED">*</span></label>
                    <select id="PROVINCE" name="PROVINCE" required>
                        <option value="" disabled selected>Select Province</option>
                        <option value="Pangasinan">Pangasinan</option>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit">Next</button>

            <!-- Login Link -->
            <div class="LOGIN">Already have an account?</div>
            <div class="LOGIN_LINK">
                <a href="login.php">Log In</a>
            </div>
        </form>
    </div>
</div>

<script src='password.js'></script>

</body>
</html>
