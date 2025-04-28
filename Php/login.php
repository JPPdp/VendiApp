<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


$message = ""; // Store login messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type']; // Check if Admin or Vendor

    if ($user_type == "admin") {
        $sql = "SELECT * FROM admins WHERE email = ?";
    } elseif ($user_type == "vendor") {
        $sql = "SELECT * FROM vendors WHERE email = ?";
    } else {
        $message = "Invalid user type selected.";
        exit;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = ($user_type == "admin") ? $user['admin_id'] : $user['vendor_id'];
        $_SESSION['user_type'] = $user_type;
        $_SESSION['user_name'] = ($user_type == "admin") ? $user['name'] : $user['business_name']; // Name for Admin, Business Name for Vendor
        
        // Redirect based on user type
        if ($user_type == "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: vendor_dashboard.php");
        }
        exit;
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Login | Vendi </title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
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
            <img src="assets/images/Visualizing_Vector.png" alt="Vector_Art">
        </div>
        <p>Collaborate and create.</p>
    </div>

    <!-- RIGHT SECTION -->
    <div class="RIGHT_SECTION">

        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <form id="LOGIN_FORM" class="LOGIN_FORM" method="post" action="">
            <h2>CONNECT WITH EVENT PLANNERS</h2>
            <p> Welcome back to <span id="VENDI">Vendi</span>! Access your dashboard to manage your listings, organize your schedule, and maximize your event bookings. </p>

            <!-- Display error message if any -->
            <?php if (!empty($error_message)): ?>
                <div class="RED_ALERT"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <h2>LOG IN</h2>
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Enter email" required><br>

            <label>User Type</label>
            <select name="user_type">
                <option value="admin" selected disabled>Choose user type</option>
                <option value="admin">Admin</option>
                <option value="vendor">Vendor</option>
            </select>
        
            <div class="BESIDE_FIELD">
                <div class="PASSWORD_CONTAINER">
                    <label for="PASSWORD">Password</label>
                    <input type="password" id="PASSWORD" name="password" placeholder="Enter Password" required minlength="8">
                    <i class="fas fa-eye PASSWORD_TOGGLE" id="password-toggle"></i>
                </div>
            </div>
            
            <span class="EXTRA">Forgot password? <a href="forgot_password.html">Click here</a></span>

            <button type="submit">Log In</button>

            <div class="LOGIN">Not registered yet?</div>
            <div class="LOGIN_LINK">
                <a href="register_vendor.php">Sign Up</a>
            </div>
        </form>
    </div>
</div>

    <script src="password.js"></script>
</body>
</html>
