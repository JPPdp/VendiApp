<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';


// Check if step 1 data exists in session
if (!isset($_SESSION['reg_data'])) {
    header("Location: register_vendor1.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from session and current form
    $reg_data = $_SESSION['reg_data'];
    
    // Handle Business Document Upload
    $target_dir = "uploads/vendors/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $business_document = $target_dir . basename($_FILES["business_document"]["name"]);
    move_uploaded_file($_FILES["business_document"]["tmp_name"], $business_document);

    // Combine multiple business_description_short values into a single string
    $business_description_short = implode(', ', $_POST['business_description_short']);
    
    // Store all data in session for database insertion
    $_SESSION['complete_reg_data'] = [
        'business_name' => $reg_data['business_name'],
        'email' => $reg_data['email'],
        'password' => $reg_data['password'],
        'mobile_number' => $reg_data['mobile_number'],
        'address' => $reg_data['address'],
        'service_option' => $_POST['service_option'],
        'business_description_short' => $business_description_short,
        'business_description_long' => $_POST['business_description_long'],
        'business_document' => $business_document
    ];
    
    // Redirect to the confirmation page
    header("Location: register_vendor3.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Vendi</title>
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
            <div class="STEP active">
                <span>Account Information</span>
            </div>
            <div class="STEP active">
                <span>Business Information</span>
            </div>
            <div class="STEP">
                <span>Confirmation</span>
            </div>
        </div>
        
        <form id="VENDOR_FORM" class="LOGIN_FORM" method="post" action="" enctype="multipart/form-data">
            <h2>BUSINESS INFORMATION</h2>
            <p>Please provide details about your business to help event organizers find you.</p>

            <!-- Display error message if any -->
            <?php if (!empty($error_message)): ?>
                <div class="RED_ALERT"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div class="BESIDE_FIELDS">
                <div class="BESIDE_FIELD">
                    <label>Service Option <span id="REQUIRED">*</span></label>
                    <select name="service_option" required>
                        <option value="Food">Food</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Entertainment">Entertainment</option>
                    </select>
                </div>
            </div>

            <!-- Modified section with 3 selects -->
            <label>Business Features <span id="REQUIRED">*</span></label>
            <div class="SELECT_CONTAINER">
                <select name="business_description_short[]" required>
                <option value="" disabled selected>Select Feature 1 <span id="REQUIRED">*</span></option>
                                    <option value="" disabled>&#128197; Event Type</option>
                                    <option value="Birthday">Birthday</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="Wedding">Wedding</option>
                                    <option value="" disabled>&#127838; Food Options</option>
                                    <option value="Desserts">Desserts</option>
                                    <option value="Fast Food">Fast Food</option>
                                    <option value="Vegan">Vegan</option>
                                    <option value="" disabled>&#127866; Beverage Options</option>
                                    <option value="Alcoholic">Alcoholic</option>
                                    <option value="Coffee & Tea">Coffee & Tea</option>
                                    <option value="Refreshments">Refreshments</option>
                                    <option value="" disabled>&#127909; Entertainment Options</option>
                                    <option value="Arts & Crafts">Arts & Crafts</option>
                                    <option value="Games & Activities">Games & Activities</option>
                                    <option value="Photobooth">Photobooth</option>
                </select>
                <select name="business_description_short[]" required>
                    <option value="" disabled selected>Select Feature 2 <span id="REQUIRED">*</span></option>
                    <option value="" disabled>&#128197; Event Type</option>
                                    <option value="Birthday">Birthday</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="Wedding">Wedding</option>
                                    <option value="" disabled>&#127838; Food Options</option>
                                    <option value="Desserts">Desserts</option>
                                    <option value="Fast Food">Fast Food</option>
                                    <option value="Vegan">Vegan</option>
                                    <option value="" disabled>&#127866; Beverage Options</option>
                                    <option value="Alcoholic">Alcoholic</option>
                                    <option value="Coffee & Tea">Coffee & Tea</option>
                                    <option value="Refreshments">Refreshments</option>
                                    <option value="" disabled>&#127909; Entertainment Options</option>
                                    <option value="Arts & Crafts">Arts & Crafts</option>
                                    <option value="Games & Activities">Games & Activities</option>
                                    <option value="Photobooth">Photobooth</option>
                </select>
                <select name="business_description_short[]" required>
                    <option value="" disabled selected>Select Feature 3 <span id="REQUIRED">*</span></option>
                    <option value="" disabled>&#128197; Event Type</option>
                                    <option value="Birthday">Birthday</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="Wedding">Wedding</option>
                                    <option value="" disabled>&#127838; Food Options</option>
                                    <option value="Desserts">Desserts</option>
                                    <option value="Fast Food">Fast Food</option>
                                    <option value="Vegan">Vegan</option>
                                    <option value="" disabled>&#127866; Beverage Options</option>
                                    <option value="Alcoholic">Alcoholic</option>
                                    <option value="Coffee & Tea">Coffee & Tea</option>
                                    <option value="Refreshments">Refreshments</option>
                                    <option value="" disabled>&#127909; Entertainment Options</option>
                                    <option value="Arts & Crafts">Arts & Crafts</option>
                                    <option value="Games & Activities">Games & Activities</option>
                                    <option value="Photobooth">Photobooth</option>
                </select>
            </div>

            <div class="FORM_GROUP">
            <label class="FORM_GROUP">Business Description Summary <span id="REQUIRED">*</span></label>
            <textarea name="business_description_long" placeholder="Tell us more about your business" id="BUSINESS_DESCRIPTION" required></textarea>
            </div>

            <label>Business Document <span id="FILES">(PDF, JPEG, PNG, max 5MB)</span> <span id="REQUIRED">*</span></label>
            <input type="file" name="business_document" accept=".pdf,.jpg,.jpeg,.png" required>

            <button type="submit" name="register">Register</button>
            
            <div class="LOGIN">Back to Previous Step</div>
            <div class="LOGIN_LINK">
                <a href="register_vendor1.php">Go Back</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
