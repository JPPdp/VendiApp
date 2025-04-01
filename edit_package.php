<?php
include 'db_connect.php';
session_start();

// Check if vendor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "vendor") {
    header("Location: login.php");
    exit;
}

// Timezone and Greeting Setup
date_default_timezone_set('Asia/Manila');
$currentHour = date('H');

if ($currentHour < 12) {
    $greeting = '☀️ Good Morning,';
} elseif ($currentHour < 18) {
    $greeting = '🌤️ Good Afternoon,';
} else {
    $greeting = '🌙 Good Evening,';
}

$vendor_id = $_SESSION['user_id'];

// Fetch vendor details
$sql = "SELECT * FROM vendors WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

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
    $package_description = $_POST['package_description'];
    $package_size = $_POST['package_size'];
    $price = $_POST['price'];
    
    // Handle image upload if a new one is provided
    $image_path = $package['package_image']; // Keep existing image by default
    
    if (isset($_FILES['package_image']) && $_FILES['package_image']['error'] == UPLOAD_ERR_OK) {
        $packageThumbnail = $_FILES['package_image'];
        $thumbnailPath = 'uploads/' . uniqid() . '_' . basename($packageThumbnail['name']);
        
        if (move_uploaded_file($packageThumbnail['tmp_name'], $thumbnailPath)) {
            $image_path = $thumbnailPath;
            // Optionally delete the old image file here
        }
    }

    $sql = "UPDATE vendor_packages SET 
            package_name = ?, 
            package_description = ?, 
            package_size = ?, 
            price = ?,
            package_image = ?
            WHERE package_id = ? AND vendor_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssidsii", 
        $package_name, 
        $package_description, 
        $package_size, 
        $price,
        $image_path,
        $package_id, 
        $vendor_id
    );

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Package updated successfully!";
        header("Location: vendor_package.php");
        exit;
    } else {
        $error_message = "Error updating package: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package | Vendi</title>
    <link rel="icon" href="assets/images/VendiEnhanced.png" type="image/icon type">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="listings_add_package.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="listings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="NAV_CONTAINER">
        <!-- Navigation Bar -->
        <div class="NAVIGATION_BAR">
            <div class="LOGO">
                <img src="assets/images/Vendi_Icon.png" alt="Logo Icon" class="LOGO_ICON">
                <div class="LOGO_NAME">Vendi
                <span id="VENDORS">VENDORS</span>
                </div>
            </div>
            
            <div class="MENU_HEADER">MANAGEMENT</div>
            <a href="vendor_dashboard.php"><i class="fas fa-stream"></i> Dashboard</a>
            
            <!-- Bookings Dropdown -->
            <div class="NAV_DROPDOWN">
                <a class="NAV_DROPDOWN_TOGGLE" href="#">
                    <i class="fa fa-fw fa-calendar"></i> Bookings <i class="fas fa-chevron-down NAV_DROPDOWN_ICON"></i>
                </a>
                <div class="NAV_DROPDOWN_CONTENT">
                    <a href="vendor_bookings_approval.php"><i class="fas fa-calendar-alt"></i> <span id="ITALIC">Pending Bookings</span></a>
                    <a href="vendor_bookings_active.php"><i class="far fa-calendar-check"></i> <span id="ITALIC">Scheduled Bookings</span></a>
                    <a href="vendor_bookings_completed.php"><i class="fas fa-calendar-check"></i> <span id="ITALIC">Completed Bookings</span></a>
                    <a href="vendor_bookings_cancelled.php"><i class="fas fa-calendar-times"></i> <span id="ITALIC">Cancelled Bookings</span></a>
                </div>
            </div>
            
            <a href="vendor_package.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-store"></i> <span>Packages</span></a>
            
            <a href="vendor_clients.php"><i class="fas fa-users"></i> Clients</a>
            
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="vendor_profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="vendor_help.php"><i class="fas fa-question-circle"></i> Help</a>
            <label for="LOGOUT_MODAL_TOGGLE" class="LOGOUT">
                <i class="fa fa-fw fa-sign-out-alt"></i> Log Out
            </label>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD2" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Edit Package</h1>
                </div>
                <div class="RIGHT_UPPER">
                    <div class="ACCOUNT">
                        <div class="GREETING"><?php echo htmlspecialchars($greeting); ?></div>
                        <a href="vendor_profile.php">
                            <img src="<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? $vendor['profile_picture'] ?? 'assets/images/default_profile.jpg'); ?>" alt="Profile" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($vendor['business_name']); ?>!</span>             
                    </div>
                </div>
            </div>

            <!-- Edit Package Form -->
            <div class="LISTINGS_CONTAINER">
                <header class="LISTINGS_HEADER">
                    <h2>Edit Package Details</h2>
                    <a href="vendor_package.php" id="ADD_PACKAGE"><i class="fas fa-arrow-left"></i> Go Back</a>
                </header>
            </div>

            <div class="MAIN_CONTAINER">
                <div class="RIGHT_MAIN">
                    <form class="PACKAGE_FORM" action="" method="POST" enctype="multipart/form-data">
                        <!-- Package Thumbnail -->
                        <div class="FORM_GROUP">
                            <label for="package_image"><i class="fas fa-image"></i> Package Image <span id="REQUIRED">*</span></label>
                            <div class="THUMBNAIL_PREVIEW_CONTAINER">
                                <?php if (!empty($package['package_image'])): ?>
                                    <img id="thumbnailPreview" src="<?php echo htmlspecialchars($package['package_image']); ?>" alt="Current Package Image">
                                    <div class="PLACEHOLDER_TEXT" style="display: none;">
                                        <i class="fas fa-image"></i>
                                        <span>Package Image</span>
                                    </div>
                                <?php else: ?>
                                    <img id="thumbnailPreview" src="#" alt="Preview" style="display: none;">
                                    <div class="PLACEHOLDER_TEXT">
                                        <i class="fas fa-image"></i>
                                        <span>Package Image</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="FILE_INPUT_CONTAINER">
                                <input type="file" id="PACKAGE_THUMBNAIL" name="package_image" accept="image/*">

                            </div>
                        </div>
                </div>

                <div class="LEFT_MAIN">
                    <!-- Package Name -->
                    <div class="FORM_GROUP">
                        <label for="PACKAGE_NAME"><i class="fas fa-box"></i> Package Name <span id="REQUIRED">*</span></label>
                        <input type="text" id="PACKAGE_NAME" name="package_name" 
                               value="<?php echo htmlspecialchars($package['package_name']); ?>" 
                               placeholder="Enter package name" required>
                    </div>

                    <div class="BESIDE_FIELDS">
                        <div class="BESIDE_FIELD">
                            <!-- Package Description -->
                            <div class="FORM_GROUP" id="DESC">
                                <label for="PACKAGE_DESCRIPTION"><i class="fas fa-info-circle"></i> Description <span id="REQUIRED">*</span></label>
                                <textarea id="PACKAGE_DESCRIPTION" name="package_description" 
                                          maxlength="400" placeholder="Enter package description" 
                                          required><?php echo htmlspecialchars($package['package_description']); ?></textarea>
                            </div>
                        </div>

                        <div class="BESIDE_FIELD">
                            <!-- Starting Price -->
                            <div class="FORM_GROUP" id="PRAYS">
                                <label for="STARTING_PRICE"><i class="fas fa-tag"></i> Starting Price <span id="REQUIRED">*</span></label>
                                    <input type="number" id="STARTING_PRICE" name="price" 
                                           value="<?php echo htmlspecialchars($package['price']); ?>" 
                                           placeholder="0.00" min="0" step="0.01" required>
                            </div>

                            <!-- Capacity -->
                            <div class="FORM_GROUP" id="CAPACITEE">
                                <label for="CAPACITY"><i class="fas fa-users"></i> Capacity <span id="REQUIRED">*</span></label>
                                <input type="number" id="CAPACITY" name="package_size" 
                                       value="<?php echo htmlspecialchars($package['package_size']); ?>" 
                                       placeholder="Enter guest capacity (e.g., 50)" min="1" required>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($package['package_id']); ?>">

                    <!-- Submit Button -->
                    <div class="FORM_GROUP_SUBMIT">
                        <div class="LEFT_BUTTON">
                        <a href="vendor_package.php" id="CANCEL_PACKAGE" class="CANCEL_BUTTON"><i class="fas fa-times"></i> Cancel</a>
                        </div>
                        <div class="RIGHT_BUTTON">
                        <button type="submit" id="SUBMIT_PACKAGE"><i class="fas fa-save"></i> Save Changes</button>
                        </div>
                    </div>
                </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
// Thumbnail Preview
document.getElementById('PACKAGE_THUMBNAIL').addEventListener('change', function(e) {
    const preview = document.getElementById('thumbnailPreview');
    const placeholder = document.querySelector('.PLACEHOLDER_TEXT');
    const file = e.target.files[0];

    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        }

        reader.readAsDataURL(file);
    } else {
        // If no file selected but there's an existing image, keep showing it
        const currentImage = "<?php echo !empty($package['package_image']) ? $package['package_image'] : '' ?>";
        if (currentImage) {
            preview.src = currentImage;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        } else {
            preview.style.display = 'none';
            if (placeholder) placeholder.style.display = 'flex';
        }
    }
});
    </script>

<input type="checkbox" id="LOGOUT_MODAL_TOGGLE" class="MODAL_TOGGLE">
    <div class="LOGOUT_MODAL">
        <div class="LOGOUT_MODAL_CONTENT">
            <h3>CONFIRM LOGOUT</h3>
            <p>Are you sure you want to log out?</p>
            <div class="BUTTON_ACTIONS">
                <a href="logout.php" class="BUTTON_CONFIRM">
                    <i class="fas fa-sign-out-alt"></i> LOG OUT
                </a>
                <label for="LOGOUT_MODAL_TOGGLE" class="BUTTON_CANCEL">
                    <i class="fas fa-times"></i> CANCEL
                </label>
            </div>
        </div>
    </div>

</body>
</html>