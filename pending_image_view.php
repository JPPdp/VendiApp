<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['businessname'])) {
    header("Location: dashboard.php");
    exit();
}
date_default_timezone_set('Asia/Manila');

$currentHour = date('H');

// Determine the greeting based on the time
if ($currentHour >= 1 && $currentHour < 4) {
    $greeting = '🌄 Good Dawn!';
} elseif ($currentHour >= 16 && $currentHour < 18.5) {
    $greeting = '🌅 Good Dusk!';
} elseif ($currentHour < 12) {
    $greeting = '☀️ Good Morning!';
} elseif ($currentHour < 18) {
    $greeting = '🌤️ Good Afternoon!';
} else {
    $greeting = '🌙 Good Evening!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Package | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="notifications.css">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="listings_add_package.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="NAV_CONTAINER">
        <!-- Navigation Bar -->
        <div class="NAVIGATION_BAR">
            <div class="LOGO">
                <div class="LOGO_NAME">Vendi
                    <span>DASHBOARD</span>
                </div>
            </div>
            <div class="MENU_HEADER">MANAGEMENT</div>
            <a href="dashboard.php"><i class="fas fa-stream"></i> Dashboard</a>
            <a href="listings.html" class="NAV_ACTIVE"><i class="fa fa-fw fa-store"></i><span> Listings</span></a>
            <a href="bookings.php"><i class="fa fa-fw fa-calendar"></i> Bookings</a>
            <a href="customers.php"><i class="fa fa-fw fa-users"></i> Customers</a>
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="help.php"><i class="fa fa-fw fa-question-circle"></i> Help</a>
            <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE"><a href="listings.php" id="BREADCRUMB">Listings /</a> Add Package</h1>
                </div>
                <div class="RIGHT_UPPER">
                    <div class="ACCOUNT">
                        <span class="HELLO"><?php echo $greeting; ?></span>
                        <a href="profile.php">
                            <img src="<?php echo htmlspecialchars($_SESSION['vendors_profile']); ?>" alt="Profile Picture" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['businessname']); ?></span>             
                    </div>
                </div>
            </div>

            <!-- Add Package Form -->
            <div class="LISTINGS_CONTAINER">
                <header class="LISTINGS_HEADER">
                    <h2>Add New Package</h2>
                    <a href="listings.php" id="ADD_PACKAGE"><i class="fas fa-arrow-left"></i> Go Back</a>
                </header>
            </div>


                <div class="MAIN_CONTAINER">
                    <div class="LEFT_MAIN">
                        <form class="PACKAGE_FORM" action="submit_package.php" method="POST" enctype="multipart/form-data" >
                                <!-- Package Name -->
                                <div class="FORM_GROUP">
                                    <label for="PACKAGE_NAME"><i class="fas fa-box"></i> Package Name</label>
                                    <input type="text" id="PACKAGE_NAME" name="PACKAGE_NAME" placeholder="Enter package name" required>
                                </div>

                                <div class="BESIDE_FIELDS">
                                    <div class="BESIDE_FIELD">
                                        <!-- Package Description -->
                                        <div class="FORM_GROUP">
                                            <label for="PACKAGE_DESCRIPTION"><i class="fas fa-info-circle"></i> Description</label>
                                            <textarea id="PACKAGE_DESCRIPTION" name="PACKAGE_DESCRIPTION" maxlength="400" placeholder="Enter package description" required></textarea>
                                        </div>
                                    </div>

                                    <div class="BESIDE_FIELD">
                                            <!-- Starting Price -->
                                        <div class="FORM_GROUP">
                                            <label for="STARTING_PRICE"><i class="fas fa-tag"></i> Starting Price</label>
                                            <input type="number" id="STARTING_PRICE" name="STARTING_PRICE" placeholder="Enter starting price" required>
                                        </div>

                                        <!-- Capacity -->
                                        <div class="FORM_GROUP" id="CAPACITY">
                                            <label for="CAPACITY"><i class="fas fa-users"></i> Capacity</label>
                                            <input type="number" id="CAPACITY" name="CAPACITY" placeholder="Enter guest capacity (e.g., 50)" required>
                                        </div>
                                    </div>
                                </div>


                    </div>

                        <div class="RIGHT_MAIN">
                            <!-- Package Thumbnail -->
                            <div class="FORM_GROUP">
                                <label for="PACKAGE_THUMBNAIL"><i class="fas fa-image"></i> Package Image</label>
                                <div class="THUMBNAIL_PREVIEW_CONTAINER">
                                    <img id="thumbnailPreview" src="#" alt="Preview">
                                    <div class="PLACEHOLDER_TEXT">
                                        <i class="fas fa-image"></i>
                                        <span>Package Image</span>
                                    </div>
                                </div>
                                <div class="FILE_INPUT_CONTAINER">
                                    <input type="file" id="PACKAGE_THUMBNAIL" name="PACKAGE_THUMBNAIL" accept="image/*" required>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="FORM_GROUP_SUBMIT">
                                <div class="LEFT_BUTTON">
                                    <a href="listings.php" id="CANCEL_PACKAGE" class="CANCEL_BUTTON"><i class="fas fa-times"></i> Cancel</a>
                                </div>
                                <div class="RIGHT_BUTTONS">
                                    <button type="reset" id="RESET_PACKAGE"><i class="fas fa-undo"></i> Reset</button>
                                    <button type="submit" id="SUBMIT_PACKAGE"><i class="fas fa-upload"></i> Publish</button>
                                </div>
                            </div>
                        </div>


                        </form>
                </div>
        </div>
    </div>

    <script>
    document.getElementById('PACKAGE_THUMBNAIL').addEventListener('change', function(e) {
        const preview = document.getElementById('thumbnailPreview');
        const placeholder = document.querySelector('.PLACEHOLDER_TEXT');
        const file = e.target.files[0];

        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }

            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            placeholder.style.display = 'flex';
            preview.src = '#';
        }
    });
    </script>

</body>
</html>