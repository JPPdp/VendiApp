<?php
include 'db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "admin") {
    header("Location: login.php");
    exit;
}

// Handle vendor approval/denial
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vendor_id = $_POST['vendor_id'];
    $action = $_POST['action']; // "Approve" or "Deny"

    if ($action == "Approve") {
        $status = "Approved";
    } else {
        $status = "Denied";
    }

    $sql = "UPDATE vendors SET status = ? WHERE vendor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $vendor_id);

    if ($stmt->execute()) {
        $message = "Vendor has been $status successfully.";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Get list of pending vendors
$sql = "SELECT * FROM vendors WHERE status = 'Pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome, Admin</h2>
    <h3>Pending Vendor Approvals</h3>

    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Business Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Address</th>
                <th>Service</th>
                <th>Business Document</th>
                <th>Action</th>
            </tr>
            <?php while ($vendor = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $vendor['business_name']; ?></td>
                    <td><?php echo $vendor['email']; ?></td>
                    <td><?php echo $vendor['mobile_number']; ?></td>
                    <td><?php echo $vendor['address']; ?></td>
                    <td><?php echo $vendor['service_option']; ?></td>
                    <td>
                        <a href="<?php echo $vendor['business_document']; ?>" target="_blank">View Document</a>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="vendor_id" value="<?php echo $vendor['vendor_id']; ?>">
                            <button type="submit" name="action" value="Approve">Approve</button>
                            <button type="submit" name="action" value="Deny">Deny</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No pending vendors.</p>
    <?php endif; ?>

    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
