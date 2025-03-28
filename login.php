<?php
session_start();

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    // Create a new database connection
    $conn = new mysqli("localhost", "root", "", "janrich_db");

    // Check if the connection was successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Initialize error message
    $error_message = "Invalid username or password.";

    // Prepare and execute the SQL statement for vendors
    if ($stmt = $conn->prepare("SELECT password FROM vendors WHERE businessname = ?")) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Set session variable and redirect to dashboard
            $_SESSION['businessname'] = $username;
            header("Location: dashboard.php");
            exit();
        }

        // Close the statement
        $stmt->close();
    }

    // Prepare and execute the SQL statement for admin
    if ($stmt = $conn->prepare("SELECT password FROM admin_console WHERE admin_name = ?")) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Set session variables and redirect to admin_vendors.php
            $_SESSION['admin_name'] = $username;
            header("Location: admin_vendors.php");
            exit();
        }

        // Close the statement
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="login.css?v=<?php echo date('his'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="CONTAINER">
    <!-- LEFT SECTION -->
    <div class="LEFT_SECTION">
        <div class="LOGO">
        </div>
        <div class="VECTOR_ART">
            <img src="assets/images/Visualizing_Vector.png" alt="Vector_Art">
        </div>
        <p>Collaborate and create.</p>
    </div>

    <div class="RIGHT_SECTION">
        <!-- LOGIN FORM -->
        <form id="LOGIN_FORM" class="LOGIN_FORM" method="post" action="">
            <h2>CONNECT WITH EVENT PLANNERS</h2>
            <p> Welcome back to <span id="VENDI">Vendi</span>! Access your dashboard to manage your listings, organize your schedule, and maximize your event bookings. </p>

            <!-- Display error message if any -->
            <?php if (!empty($error_message)): ?>
                <div class="RED_ALERT"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <h2>LOG IN</h2>
            <label for="USERNAME">Business Name or Admin Name</label>
            <input type="text" id="username" name="username" placeholder="Enter Username" required>
        
            <div class="BESIDE_FIELD">
                <div class="PASSWORD_CONTAINER">
                    <label for="PASSWORD">Password</label>
                    <input type="password" id="PASSWORD" name="password" placeholder="Enter Password" required minlength="8">
                    <i class="fas fa-eye PASSWORD_TOGGLE" id="password-toggle"></i>
                </div>
            </div>
            
            <span class="EXTRA">Forgot password? <a href="reset_password.php">Click here</a></span>

            <button type="submit">Log In</button>

            <div class="LOGIN">Not registered yet?</div>
            <div class="LOGIN_LINK">
                <a href="register1.php">Sign Up</a>
            </div>
        </form>
    </div>
</div>

<script src='password.js'></script>
</body>
</html>
