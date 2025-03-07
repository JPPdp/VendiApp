<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $businessname = htmlspecialchars($_POST['businessname']); // Sanitize input
    $email = htmlspecialchars($_POST['email']); // Sanitize input
    $mobile = htmlspecialchars($_POST['mobile']); // Sanitize input
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Connect to the database
        $conn = new mysqli("localhost", "root", "", "vendi_db");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the username or email already exists
        $stmt = $conn->prepare("SELECT id FROM vendors WHERE businessname = ? OR email = ?");
        $stmt->bind_param("ss", $businessname, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Username or email already exists. Please choose another.";
        } else {
            $stmt->close();
            // Insert new user into the database
            if ($stmt = $conn->prepare("INSERT INTO vendors (businessname, email, mobile, password) VALUES (?, ?, ?, ?)")) {
                $stmt->bind_param("ssss", $businessname, $email, $mobile, $hashed_password);
                if ($stmt->execute()) {
                    // Redirect to login page after successful registration
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
        <!-- VENDOR SIGN-UP FORM -->
        <form id="VENDOR_FORM" class="LOGIN_FORM" method="post" action="" enctype="multipart/form-data">
            <h2>CONNECT WITH EVENT PLANNERS</h2>
            <p>Welcome to <span id="VENDI">Vendi</span>! Create a vendor account to manage your listings, organize your schedule, and maximize your event bookings.</p>
            <!-- Display error message if any -->
            <?php if (!empty($error_message)): ?>
                <div class="RED_ALERT"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <h2>SIGN UP</h2>
            <!-- Business Name -->
            <label for="VENDOR_USERNAME">Business Name</label>
            <input type="text" id="businessname" name="businessname" placeholder="Enter Business Name" required>

            <!-- Email -->
            <label for="VENDOR_EMAIL">Email</label>
            <input type="email" id="VENDOR_EMAIL" name="email" placeholder="Enter Email Address" required>

            <!-- Mobile Number -->
            <label for="VENDOR_MOBILE">Mobile Number</label>
            <span id="PHL">+63</span>
            <input type="tel" id="VENDOR_MOBILE" name="mobile" placeholder="Enter Mobile Number" required  minlength="10" maxlength="10">
            <!-- Password -->
            <div class="BESIDE_FIELDS">
                <div class="BESIDE_FIELD">
                    <label for="PASSWORD">Password</label>
                    <div class="PASSWORD_CONTAINER">
                        <input type="password" id="PASSWORD" name="password" placeholder="Enter Password" required minlength="8">
                        <i class="fas fa-eye PASSWORD_TOGGLE" id="password-toggle"></i>
                        <div id="PASSWORD_REQUIREMENTS" class="password-requirements-dropdown">
                            Password must meet the following requirements:
                            <ul>
                                <li>At least <strong>8 characters</strong> long</li>
                                <li>Contain at least <strong>one uppercase letter</strong> (A-Z)</li>
                                <li>Contain at least <strong>one lowercase letter</strong> (a-z)</li>
                                <li>Contain at least <strong>one number</strong> (0-9)</li>
                                <li>Contain at least <strong>one special character</strong> (e.g., !@#$%^&*)</li>
                                <li>No spaces allowed</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="BESIDE_FIELD">
                    <label for="CONFIRM_PASSWORD">Confirm Password</label>
                    <div class="PASSWORD_CONTAINER">
                        <input type="password" id="CONFIRM_PASSWORD" name="confirm_password" placeholder="Re-enter Password" required minlength="8">
                        <i class="fas fa-eye PASSWORD_TOGGLE" id="confirm-password-toggle"></i>
                    </div>
                </div>
            </div>

            <!-- Business Document Upload -->
            <label for="BUSINESS_DOCUMENT">Upload Business Document <span id="FILES">(PDF, JPEG, PNG, max 5MB)</span></label>
            <input type="file" id="BUSINESS_DOCUMENT" name="business_document" accept=".pdf,.jpg,.jpeg,.png" required>

            <!-- Submit Button -->
            <button type="submit">Sign Up</button>

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