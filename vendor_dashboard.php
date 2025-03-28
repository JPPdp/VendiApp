<?php
include 'db_connect.php';
session_start();

// Check if vendor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];

// Fetch vendor details
$sql = "SELECT * FROM vendors WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

// Fetch vendor packages if approved
$packages = [];
if ($vendor['status'] == "Approved") {
    $sql = "SELECT * FROM vendor_packages WHERE vendor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $packages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $vendor['business_name']; ?></h2>
    
    <h3>Business Information</h3>
    <p><strong>Email:</strong> <?php echo $vendor['email']; ?></p>
    <p><strong>Service Type:</strong> <?php echo $vendor['service_option']; ?></p>
    <p><strong>Mobile:</strong> <?php echo $vendor['mobile_number']; ?></p>
    <p><strong>Address:</strong> <?php echo $vendor['address']; ?></p>
    <p><strong>Status:</strong> <?php echo $vendor['status']; ?></p>

    <?php if ($vendor['status'] == "Pending"): ?>
        <p>Your account is awaiting approval from the admin.</p>
    <?php elseif ($vendor['status'] == "Denied"): ?>
        <p>Your registration was denied. Contact admin for more details.</p>
    <?php else: ?>
        <h3>Your Packages</h3>
        <?php if (!empty($packages)): ?>
            <table border="1">
                <tr>
                    <th>Package Name</th>
                    <th>Size</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($packages as $package): ?>
                    <tr>
                        <td><?php echo $package['package_name']; ?></td>
                        <td><?php echo $package['package_size']; ?> people</td>
                        <td>$<?php echo $package['price']; ?></td>
                        <td>
                            <a href="edit_package.php?id=<?php echo $package['package_id']; ?>">Edit</a> | 
                            <a href="delete_package.php?id=<?php echo $package['package_id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this package?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No packages added yet.</p>
        <?php endif; ?>

        <h3>Add New Package</h3>
        <form action="add_package.php" method="POST">
            <label>Package Name:</label>
            <input type="text" name="package_name" required><br>

            <label>Package Size (people):</label>
            <input type="number" name="package_size" required><br>

            <label>Price ($):</label>
            <input type="number" step="0.01" name="price" required><br>

            <input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>">
            <button type="submit">Add Package</button>
        </form>
    <?php endif; ?>

    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
