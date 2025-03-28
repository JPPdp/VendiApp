<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $business_name = $_POST['business_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $mobile_number = $_POST['mobile_number'];
    $address = $_POST['address'];
    $service_option = $_POST['service_option'];
    $business_description_short = $_POST['business_description_short'];
    $business_description_long = $_POST['business_description_long'];

    // Handle Business Document Upload
    $target_dir = "uploads/vendors/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $business_document = $target_dir . basename($_FILES["business_document"]["name"]);
    move_uploaded_file($_FILES["business_document"]["tmp_name"], $business_document);

    // Insert Vendor into Database (Status set to Pending)
    $sql = "INSERT INTO vendors (business_name, email, password, mobile_number, address, service_option, business_description_short, business_description_long, business_document, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $business_name, $email, $password, $mobile_number, $address, $service_option, $business_description_short, $business_description_long, $business_document);

    if ($stmt->execute()) {
        echo "<script>alert('Vendor Registered Successfully! Waiting for Admin Approval.');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
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
            <div class="STEP">
                <span>Business Information</span>
            </div>
            <div class="STEP">
                <span>Confirmation</span>
            </div>
        </div>
        <!-- VENDOR SIGN-UP FORM -->
        <form id="VENDOR_FORM" class="LOGIN_FORM" method="post" action="" enctype="multipart/form-data">
            <h2>CONNECT WITH EVENT PLANNERS</h2>
            <p>Welcome to <span id="VENDI">Vendi</span>! Create your vendor account to showcase your products and services, 
            organize your schedule and maximize your event bookings with our dashboard.</p>

            <!-- Display error message if any -->
            <?php if (!empty($error_message)): ?>
                <div class="RED_ALERT"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <h2>SIGN UP</h2>
            
            <!-- Business Name -->
            <label for="VENDOR_USERNAME">Business Name <span id="REQUIRED">*</span></label>
            <input type="text" id="businessname" name="business_name" placeholder="Enter Business Name" required>


            <div class="BESIDE_FIELDS">
                <div class="BESIDE_FIELD">
                    <!-- Email -->
                    <label for="VENDOR_EMAIL">Email <span id="REQUIRED">*</span></label>
                    <input type="email" id="VENDOR_EMAIL" name="email" placeholder="Enter Email Address" required>
                </div>

                <div class="BESIDE_FIELD">
                    <!-- Mobile Number -->
                    <label for="VENDOR_MOBILE">Mobile Number <span id="REQUIRED">*</span></label>
                    <span id="PHL">+63</span>
                    <input type="tel" id="VENDOR_MOBILE" name="mobile_number" placeholder="Enter Mobile Number" required minlength="10" maxlength="10">
                </div>
            </div>


            <!-- Password -->
            <div class="BESIDE_FIELDS">
                <div class="BESIDE_FIELD">
                    <label for="PASSWORD">Password <span id="REQUIRED">*</span></label>
                    <div class="PASSWORD_CONTAINER">
                        <input type="password" id="PASSWORD" name="password" placeholder="Enter Password" required minlength="8">
                        <i class="fas fa-eye PASSWORD_TOGGLE" id="password-toggle"></i>
                        <div id="PASSWORD_REQUIREMENTS" class="PASSWORD_REQUIREMENTS_DROPDOWN">
                            <span>Password must meet the following requirements:</span>
                            <ul>
                                <li>At least <strong>8 characters</strong> long</li>
                                <li>Must <strong>not contain any symbols or special characters</strong></li>
                                <li>No spaces allowed</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="BESIDE_FIELD">
                    <label for="CONFIRM_PASSWORD">Confirm Password <span id="REQUIRED">*</span></label>
                    <div class="PASSWORD_CONTAINER">
                        <input type="password" id="CONFIRM_PASSWORD" name="confirm_password" placeholder="Re-enter Password" required minlength="8">
                        <i class="fas fa-eye PASSWORD_TOGGLE" id="confirm-password-toggle"></i>
                    </div>
                </div>
            </div>

            <div class="BESIDE_FIELD">
                    <label for="ADDRESS">Address <span id="REQUIRED">*</span></label> 
                    <input type="text" id="ADDRESS" name="address" placeholder="Enter complete address" required>
                </div>

            <div class="BESIDE_FIELDS">
                <div class="BESIDE_FIELD">
                    <label>Service Option:</label>
                    <select name="service_option">
                        <option value="Food">Food</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Entertainment">Entertainment</option>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit">Next</button>

            <!-- Login Link -->
            <div class="LOGIN">Already have an account?</div>
            <div class="LOGIN_LINK">
                <a href="login.php">Log In</a>
            </div>
        </form>
    </div>
</div>




    <h2>Register as Vendor</h2>
        <label>Business Name:</label>
        <input type="text" name="business_name" required><br>

        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <label>Mobile Number:</label>
        <input type="text" name="mobile_number" required><br>

        <label>Address:</label>
        <textarea name="address" required></textarea><br>

        <label>Service Option:</label>
        <select name="service_option">
            <option value="Food">Food</option>
            <option value="Beverages">Beverages</option>
            <option value="Entertainment">Entertainment</option>
        </select><br>

        <label>Short Description:</label>
        <input type="text" name="business_description_short" required><br>

        <label>Long Description:</label>
        <textarea name="business_description_long" required></textarea><br>

        <label>Business Document (PDF or Image):</label>
        <input type="file" name="business_document" accept=".pdf,.jpg,.jpeg,.png" required><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
