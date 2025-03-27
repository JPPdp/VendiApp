<?php
session_start();

// Check if the user is coming from the sign-up form
if (!isset($_SESSION['registration_data'])) {
    header("Location: register1.php"); // Redirect to sign-up if accessed directly
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $business_category = htmlspecialchars(trim($_POST['business_category']));
    $business_description = htmlspecialchars(trim($_POST['business_description']));
    $features1 = htmlspecialchars(trim($_POST['features1']));
    $features2 = htmlspecialchars(trim($_POST['features2']));
    $features3 = htmlspecialchars(trim($_POST['features3']));
    $business_document = $_FILES['business_document'];

    // Check if the file was uploaded without errors
    if ($business_document['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/'; // Make sure this directory exists and is writable
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $upload_file = $upload_dir . basename($business_document['name']);

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($business_document['tmp_name'], $upload_file)) {
            // Read the file content
            $file_content = file_get_contents($upload_file);

            // Update session data with new inputs
            $_SESSION['registration_data']['business_category'] = $business_category;
            $_SESSION['registration_data']['business_description'] = $business_description;
            $_SESSION['registration_data']['features1'] = $features1;
            $_SESSION['registration_data']['features2'] = $features2;
            $_SESSION['registration_data']['features3'] = $features3;
            $_SESSION['registration_data']['business_documents'] = $file_content;
            header("Location: register3.php");
            exit();
        } else {
            $error_message = "Failed to upload business document. Please try again.";
        }
    } else {
        $error_message = "Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Validation | Vendi</title>
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

        <?php if (!empty($error_message)): ?>
            <div class="RED_ALERT"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

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

        <!-- Business Validation Form -->
        <form id="BUSINESS_VALIDATION_FORM" class="LOGIN_FORM" method="post" action="" enctype="multipart/form-data">
            <h2>BUSINESS INFORMATION</h2>
            <p>Kindly submit your proof of business document and select the services you offer to proceed.</p>

            <!-- Services Offered -->
            <label for="CATEGORY">Business Category <span id="REQUIRED">*</span></label>
            <select id="CATEGORY" name="CATEGORY" required>
                <option value="" disabled selected>What does your business primarily offer?</option>
                <option value="FOOD">Food</option>
                <option value="BEVERAGES">Beverages</option>
                <option value="ENTERTAINMENT">Entertainment</option>
            </select>

            <!-- Description and Features Section -->
            <div class="BESIDE_FIELDS">
                <div class="BESIDE_FIELD">
                    <!-- Package Description -->
                    <div class="FORM_GROUP">
                        <label for="PACKAGE_DESCRIPTION">Description <span id="REQUIRED">*</span></label>
                        <textarea id="PACKAGE_DESCRIPTION" name="PACKAGE_DESCRIPTION" maxlength="400" placeholder="Enter package description" required></textarea>
                    </div>
                </div>

                <div class="BESIDE_FIELD">
                    <!-- Package Features -->
                    <div class="FORM_GROUP">
                        <label>Features <span id="REQUIRED">*</span></label>
                        <div id="PACKAGE_FEATURES">
                            <div class="FEATURE_ITEM">
                                <select name="feature1" required>
                                    <option value="" disabled selected>Select Feature 1</option>
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
                                    <option value="Kid-Friendly">Arts & Crafts</option>
                                    <option value="Games & Activities">Games & Activities</option>
                                    <option value="Photobooth">Photobooth</option>
                                </select>
                            </div>
                            <div class="FEATURE_ITEM">
                                <select name="feature2" required>
                                    <option value="" disabled selected>Select Feature 2</option>
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
                                    <option value="Kid-Friendly">Arts & Crafts</option>
                                    <option value="Games & Activities">Games & Activities</option>
                                    <option value="Photobooth">Photobooth</option>
                                </select>
                            </div>
                            <div class="FEATURE_ITEM">
                                <select name="feature3" required>
                                    <option value="" disabled selected>Select Feature 3</option>
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
                                    <option value="Kid-Friendly">Arts & Crafts</option>
                                    <option value="Games & Activities">Games & Activities</option>
                                    <option value="Photobooth">Photobooth</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Document Upload -->
            <label for="BUSINESS_DOCUMENT">Upload Business Document <span id="FILES">(PDF, JPEG, PNG, max 5MB)</span> <span id="REQUIRED">*</span></label>
            <input type="file" id="BUSINESS_DOCUMENT" name="business_document" accept=".pdf,.jpg,.jpeg,.png" required>

            <!-- Submit Button -->
            <button type="submit">Submit</button>

            <!-- Optional Back Link -->
            <div class="LOGIN">Back to Registration?</div>
            <div class="LOGIN_LINK">
                <a href="register1.php">Go Back</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>