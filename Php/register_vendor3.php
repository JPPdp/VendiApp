<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if complete registration data exists
if (!isset($_SESSION['complete_reg_data'])) {
    header("Location: register_vendor1.php");
    exit();
}

// Validate category_id
if (!isset($_SESSION['complete_reg_data']['category_id']) || !is_numeric($_SESSION['complete_reg_data']['category_id'])) {
    $error_message = "Error: category_id is missing or invalid.";
} else {
    include 'db_connect.php';

    $reg_data = $_SESSION['complete_reg_data'];

    $sql = "INSERT INTO vendors (category_id, business_name, email, password, mobile_number, address, business_description_short, business_description_long, business_document, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssss", 
        $reg_data['category_id'],
        $reg_data['business_name'], 
        $reg_data['email'], 
        $reg_data['password'], 
        $reg_data['mobile_number'], 
        $reg_data['address'],
        $reg_data['business_description_short'],
        $reg_data['business_description_long'],
        $reg_data['business_document']
    );

    if ($stmt->execute()) {
        // Clear session data after successful registration
        unset($_SESSION['complete_reg_data']);
    } else {
        $error_message = "Registration failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approval | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
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
            <div class="STEP">
                <span>Account Information</span>
            </div>
            <div class="STEP">
                <span>Business Information</span>
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
    </div>
</div>
</body>
</html>
