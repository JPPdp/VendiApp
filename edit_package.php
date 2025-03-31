<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


// Check if vendor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];

// Fetch package details
if (isset($_GET['id'])) {
    $package_id = $_GET['id'];

    $sql = "SELECT * FROM vendor_packages WHERE package_id = ? AND vendor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $package_id, $vendor_id);
    $stmt->execute();
    $package = $stmt->get_result()->fetch_assoc();

    if (!$package) {
        header("Location: vendor_dashboard.php");
        exit;
    }
}

// Update package details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $package_id = $_POST['package_id'];
    $package_name = $_POST['package_name'];
    $package_size = $_POST['package_size'];
    $price = $_POST['price'];

    $sql = "UPDATE vendor_packages SET package_name = ?, package_size = ?, price = ? WHERE package_id = ? AND vendor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidii", $package_name, $package_size, $price, $package_id, $vendor_id);

    if ($stmt->execute()) {
        header("Location: vendor_dashboard.php");
        exit;
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Package</title>
</head>
<body>
    <h2>Edit Package</h2>
    
    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="package_id" value="<?php echo $package['package_id']; ?>">

        <label>Package Name:</label>
        <input type="text" name="package_name" value="<?php echo $package['package_name']; ?>" required><br>

        <label>Package Size (people):</label>
        <input type="number" name="package_size" value="<?php echo $package['package_size']; ?>" required><br>

        <label>Price ($):</label>
        <input type="number" step="0.01" name="price" value="<?php echo $package['price']; ?>" required><br>

        <button type="submit">Update Package</button>
    </form>

    <br>
    <a href="vendor_dashboard.php">Back to Dashboard</a>
</body>
</html>
