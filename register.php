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
    <link rel="stylesheet" href="login.css">
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
            <img src="assets/images/Visualizing_Vector.png" alt="Vector_Art">
        </div>
        <p>Collaborate and create.</p>
    </div>

    <div class="RIGHT_SECTION">
        <!-- VENDOR SIGN-UP FORM -->
        <form id="VENDOR_FORM" class="LOGIN_FORM active" method="post" action="">
            <h2>CONNECT WITH EVENT PLANNERS</h2>
            <p>Welcome! Create an account to manage your schedule and maximize your event bookings.</p>

            <!-- Display error message if any -->
            <?php if (!empty($error_message)): ?>
                <div class="RED_ALERT"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Business Name -->
            <label for="VENDOR_USERNAME">Business Name</label>
            <input type="text" id="businessname" name="businessname" placeholder="Enter Business Name" required>

            <!-- Email -->
            <label for="VENDOR_EMAIL">Email</label>
            <input type="email" id="VENDOR_EMAIL" name="email" placeholder="Enter Email Address" required>

            <!-- Mobile Number -->
            <label for="VENDOR_MOBILE">Mobile Number</label>
            <input type="tel" id="VENDOR_MOBILE" name="mobile" placeholder="Enter Mobile Number" required>

            <!-- Password -->
            <div class="PASSWORD_CONTAINER">
                <label for="PASSWORD">Password</label>
                <input type="password" id="PASSWORD" name="password" placeholder="Enter Password" required minlength="8">
                <i class="fas fa-eye PASSWORD_TOGGLE" id="password-toggle"></i>
            </div>

            <!-- Confirm Password -->
            <div class="PASSWORD_CONTAINER">
                <label for="CONFIRM_PASSWORD">Confirm Password</label>
                <input type="password" id="CONFIRM_PASSWORD" name="confirm_password" placeholder="Confirm Password" required minlength="8">
                <i class="fas fa-eye PASSWORD_TOGGLE" id="confirm-password-toggle"></i>
            </div>

            <!-- Submit Button -->
            <button type="submit">SIGN UP</button>

            <!-- Login Link -->
            <div class="LOGIN">Already have an account?</div>
            <div class="LOGIN_LINK">
                <a href="login.php">LOG IN</a>
            </div>
        </form>
    </div>
</div>

<script src='script.js'></script>
</body>
</html>