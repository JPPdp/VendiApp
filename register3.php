<?php
session_start();
if (!isset($_SESSION['registration_data'])) {
    header("Location: register1.php");
    exit();
}

$registration_data = $_SESSION['registration_data'];

// Automatically insert data into the database when the user lands on this page
$conn = new mysqli("localhost", "root", "", "janrich_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert data into the pending_vendors table
$stmt = $conn->prepare("INSERT INTO pending_vendors (businessname, business_description, vendors_mobile, vendors_email, password, address, city_municipal, province, business_category, features1, features2, features3, business_documents) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "sssssssssssss",
    $registration_data['businessname'],
    $registration_data['business_description'],
    $registration_data['vendors_mobile'],
    $registration_data['vendors_email'],
    $registration_data['password'],
    $registration_data['address'],
    $registration_data['city_municipal'],
    $registration_data['province'],
    $registration_data['business_category'],
    $registration_data['features1'],
    $registration_data['features2'],
    $registration_data['features3'],
    $registration_data['business_documents']
);

if ($stmt->execute()) {
    // Clear session data
    unset($_SESSION['registration_data']);
    // Redirect to admin_vendors.php for approval
    header("Location: register3.php");
    exit();
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
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="login.css?v=<?php echo time(); ?>">
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
                    <span>Account Information</span>
                </div>
                <div class="STEP active">
                    <span>Business Validation</span>
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

                <!-- Display error message if any -->
                <?php if (!empty($error_message)): ?>
                    <div class="RED_ALERT"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <div class="LOGIN_LINK">
                    <a href="login.php">Back</a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>