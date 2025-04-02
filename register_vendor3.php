<?php
session_start();

// Check if complete registration data exists
if (!isset($_SESSION['complete_reg_data'])) {
    header("Location: register_vendor1.php");
    exit();
}

// Insert data into database (this could be moved to a separate processing script if needed)
include 'db_connect.php';

$reg_data = $_SESSION['complete_reg_data'];

$sql = "INSERT INTO vendors (business_name, email, password, mobile_number, address, service_option, business_description_short, business_description_long, business_document, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", 
    $reg_data['business_name'], 
    $reg_data['email'], 
    $reg_data['password'], 
    $reg_data['mobile_number'], 
    $reg_data['address'],
    $reg_data['service_option'],
    $reg_data['business_description_short'],
    $reg_data['business_description_long'],
    $reg_data['business_document']
);

if ($stmt->execute()) {
    // Clear session data after successful insertion
    unset($_SESSION['reg_data']);
    unset($_SESSION['complete_reg_data']);
} else {
    $error_message = "Registration failed. Please try again.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approval | Vendi</title>
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
        <div class="REGISTRATION_STEPS">
            <div class="STEP active">
                <span> <i class="fas fa-check"></i> Account Information</span>
            </div>
            <div class="STEP active">
                <span> <i class="fas fa-check"></i> Business Information</span>
            </div>
            <div class="STEP active">
                <span>Confirmation</span>
            </div>
        </div>

        <div class="LOGIN_FORM">
            <h2>THANK YOU FOR YOUR SUBMISSION!</h2>
            <p><span id="VENDI">Vendi</span> is currently reviewing your registration. 
                Please wait a short while, and you will be able to access your dashboard soon.</p>
            <p>Try logging in again later to access your dashboard once it's ready.</p>  

            <?php if (!empty($error_message)): ?>
                <div class="RED_ALERT"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div class="LOGIN_LINK">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
        <footer class="DASHBOARD_FOOTER">
            <div class="FOOTER_CONTENT">
                <span class="COPYRIGHT">&copy; 2025 GitRat</span>
            </div>
        </footer>
    </div>
</div>
</body>
</html>