<?php
include 'db_connect.php';
session_start();

// Check if vendor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
    header("Location: login.php");
    exit;
}

if ($_SESSION['user_type'] != "vendor") {
    header("Location: unauthorized.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];

// Handle final deletion confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_delete'])) {
    try {
        // Begin transaction
        $conn->begin_transaction();
        
        // 1. Delete all bookings for this vendor
        $stmt = $conn->prepare("DELETE FROM bookings WHERE vendor_id = ?");
        $stmt->bind_param("i", $vendor_id);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting bookings: " . $stmt->error);
        }
        $stmt->close();
        
        // 2. Delete all packages for this vendor
        $stmt = $conn->prepare("DELETE FROM vendor_packages WHERE vendor_id = ?");
        $stmt->bind_param("i", $vendor_id);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting packages: " . $stmt->error);
        }
        $stmt->close();
        
        // 3. Delete any vendor todos
        $stmt = $conn->prepare("DELETE FROM vendor_todos WHERE vendor_id = ?");
        $stmt->bind_param("i", $vendor_id);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting todos: " . $stmt->error);
        }
        $stmt->close();
        
        // 4. Delete the vendor account itself
        $stmt = $conn->prepare("DELETE FROM vendors WHERE vendor_id = ?");
        $stmt->bind_param("i", $vendor_id);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting vendor account: " . $stmt->error);
        }
        
        if ($stmt->affected_rows > 0) {
            $conn->commit();
            
            // Destroy the session
            session_unset();
            session_destroy();
            
            // Redirect to goodbye page
            header("Location: deleted_account.php");
            exit;
        } else {
            throw new Exception("No rows affected - vendor account not found");
        }
        
        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Account deletion failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Deletion | Vendi</title>
    <link rel="icon" href="assets/images/VendiEnhanced.png" type="image/icon type">
    <link rel="stylesheet" href="login.css?v=<?php echo time(); ?>">
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
            <img src="assets/images/Volcano_Vector.png" alt="Goodbye Art">
        </div>
        <p>Goodbye and farewell.</p>
    </div>

    <div class="RIGHT_SECTION">
        <div class="LOGIN_FORM">
            <h2>CONFIRM ACCOUNT DELETION</h2>
            <p><span id="VENDI">Vendi</span> will miss you! Are you sure you want to leave?</p>
            
            <div class="RED_ALERT">
                <i class="fas fa-exclamation-circle"></i> All your data will be permanently deleted.
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="RED_ALERT"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="delete_account.php">
                <div class="BUTTON_GROUP">
                    <button type="submit" name="confirm_delete" class="BUTTON_CONFIRM">
                        <i class="fas fa-trash-alt"></i> DELETE ACCOUNT
                    </button>
                </div>
                <div class="LOGIN">Made a mistake?</div>
                <div class="LOGIN_LINK">
                    <a href="vendor_profile.php">Go Back</a>
                </div>
            </form>
            
            <footer class="DASHBOARD_FOOTER">
                <div class="FOOTER_CONTENT">
                    <span class="COPYRIGHT">&copy; 2025 GitRat</span>
                </div>
            </footer>
        </div>
    </div>
</div>
</body>
</html>