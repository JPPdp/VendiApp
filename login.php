<?php
include 'db_connect.php';
session_start();

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
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <label>Login as:</label>
        <select name="user_type">
            <option value="admin">Admin</option>
            <option value="vendor">Vendor</option>
        </select><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
