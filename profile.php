<?php
session_start();

if (!isset($_SESSION['businessname'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="db_notifications.css">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="profile.css?v=<?php echo time(); ?>">
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
            <a href="dashboard.php"><i class="fas fa-stream"></i>Dashboard</a>
            <a href="listings.html"><i class="fa fa-fw fa-store"></i> Listings</a>
            <a href="bookings.php"><i class="fa fa-fw fa-calendar"></i> Bookings</a>
            <a href="reports.htm"><i class="fas fa-chart-pie"></i> Reports</a>
            <a href="customers.htm"><i class="fa fa-fw fa-users"></i> Customers</a>
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="db_profile.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-user"></i> <span>Profile</span> </a>
            <a href="dsb_help.html"><i class="fa fa-fw fa-question-circle"></i> Help</a>
            <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Profile</h1>
                </div>
                <div class="RIGHT_UPPER">
                    <div class="SEARCH_BAR">
                        <input type="text" placeholder="Search here...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="ACCOUNT">
                        <div class="NOTIFICATION">
                            <i class="fas fa-bell"></i>
                            <span class="NOTIFICATION_DOT"></span> <!-- Red dot for notifications -->
                        </div>
                        <a href="db_profile.html">
                            <img src="assets/images/tiara.png" alt="Profile Picture" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['businessname']); ?></span>             
                    </div>
                </div>
            </div>

            <!-- Profile Container -->
            <div class="PROFILE_CONTAINER">
                <!-- Left Profile Section -->
                <div class="LEFT_PROFILE">
                    <div class="PROFILE_PIC_CONTAINER"><!-- Profile Picture -->
                        <img src="assets/images/jacks.jpg" alt="Profile Picture" class="PROFILE_PIC2">
                        <div class="EDIT_ICON_CONTAINER">
                            <i class="EDIT_ICON fas fa-pencil-alt" aria-hidden="true"></i>
                        </div>
                        <!--<input type="file" id="VENDOR_PROFILE_PIC" name="profile_pic" accept="image/*">-->
                    </div>
                    <!-- Business Name -->
                    <h2 id="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['businessname']); ?></h2>
                    
                    <p class="SERVICE_OFFERED">Food</p><!--<?php echo $vendor['service_offered']; ?>-->

                    <!-- Business Description -->
                    <label for="VENDOR_DESCRIPTION"></label>
                    <textarea id="VENDOR_DESCRIPTION" name="BUSINESS_DESCRIPTION" placeholder="Enter a brief description of your business"></textarea><!--<?php echo htmlspecialchars($_SESSION['business_description']); ?>-->

                    <div class="BUTTON_CONTAINER"> <!-- Button -->
                        <button type="submit" class="SUBMIT_BUTTON">Submit</button>
                    </div>
                </div>



                <!-- Right Profile Section -->
                <div class="RIGHT_PROFILE">
                    <h2>Vendor Profile</h2>

                    <!-- 1st Stack: Contact Information -->
                    <div class="STACK">
                        <div class="BESIDE_FIELDS">
                            <div class="BESIDE_FIELD">
                                <!-- Email -->
                                <label>Email</label>
                                <input type="email" id="VENDOR_EMAIL" name="email" value="email@email.com" readonly><!--<?php echo htmlspecialchars($_SESSION['email']); ?>-->
                            </div>

                            <div class="BESIDE_FIELD">
                                <!-- Mobile Number -->
                                <label>Mobile Number</label>
                                <input type="tel" id="VENDOR_MOBILE" name="mobile"required minlength="10" maxlength="10" value="9172717281" readonly><!--<?php echo htmlspecialchars($_SESSION['mobile']); ?>-->
                            </div>
                        </div>
                    </div>

                    <!-- 2nd Stack: Address Information -->
                    <div class="STACK">
                        <div class="BESIDE_FIELDS">
                            <div class="BESIDE_FIELD">
                                <label>Address</label>
                                <input type="text" id="ADDRESS" name="ADDRESS" value="RS Building, Arellano" readonly><!--<?php echo $vendor['address']; ?>-->
                            </div>

                            <div class="BESIDE_FIELD">
                                <label>City</label>
                                <input type="text" id="CITY" name="CITY" value="Dagupan City" readonly>
                                </div>

                            <div class="BESIDE_FIELD">
                                <label>Province</label>
                                <input type="text" id="PROVINCE" name="PROVINCE" value="Pangasinan" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- 3rd Stack: Additional Information -->
                    <div class="STACK1">
                        <label>Business Documents</label>
                        <!-- Button to view the document -->
                        <a href="#IMAGE_VIEW" class="VIEW_BUTTON">View Image</a>
                    </div>

                    <!-- Container for Expanded Image -->
                    <div id="IMAGE_VIEW" class="EXPAND">
                        <a href="#" class="CLOSE_BUTTON">&times;</a>
                        <img class="EXPANDED_IMAGE" src="assets/images/sample_document.png" alt="Expanded Business Document">
                    </div>

                    <!-- Change Password Link -->
                    <div class="LOGIN">Forgot Password?</div>
                    <div class="LOGIN_LINK">
                        <a href="change_password.php">Change password</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="password.js"></script>
</body>
</html>
