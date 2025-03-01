<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $businessname = $_POST['businessname'];
    $password = $_POST['password'];

    $conn = new mysqli("localhost", "root", "", "vendi_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($stmt = $conn->prepare("SELECT password FROM vendors WHERE businessname = ?")) {
        $stmt->bind_param("s", $businessname);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['businessname'] = $businessname;
            header("Location: dashboard.html");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Invalid business name or password.</div>";
        }

        $stmt->close();
    }

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
        <!-- VENDOR LOGIN FORM -->
        <form id="VENDOR_FORM" class="LOGIN_FORM active" method="post" action="login.php">
            <h2>CONNECT WITH EVENT PLANNERS</h2>
            <p> Welcome back! Access your dashboard to manage your schedule and maximize your event bookings. </p>

            <label for="VENDOR_USERNAME">Businessname</label>
            <input type="text" id="businessname" name="businessname" placeholder="Enter Business Name" required>
        
            <div class="PASSWORD_CONTAINER">
                <label for="PASSWORD">Password</label>
                <input type="password" id="PASSWORD" name="password" placeholder="Enter Password" required minlength="8">
                <i class="fas fa-eye PASSWORD_TOGGLE" id="password-toggle"></i>
            </div>

            <div class="REMEMBER_FORGOT_CONTAINER">
                <label for="REMEMBER_ME" class="REMEMBER_ME">
                    <input type="checkbox" id="REMEMBER_ME" name="REMEMBER_ME">
                    <span>Remember me</span>
                </label>
                <span class="EXTRA">Forgot password? <a href="forgot_password.html">Click here</a></span>
            </div>

            <button type="submit">LOG IN</button>

            <div class="LOGIN">Not registered yet?</div>
            <div class="LOGIN_LINK">
                <a href="register.php">SIGN UP</a>
            </div>
        </form>
    </div>
</div>

<script src='script.js'></script>
</body>
</html>