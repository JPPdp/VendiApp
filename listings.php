<?php
session_start();

//Display name on Upper Right Nav
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
    <title>Listings | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="notifications.css">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="listings.css?v=<?php echo time(); ?>">
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
            <a href="listings.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-store"></i><span> Listings</span></a>
            <a href="bookings.php"><i class="fa fa-fw fa-calendar"></i> Bookings</a>
            <a href="reports.htm"><i class="fas fa-chart-pie"></i> Reports</a>
            <a href="customers.htm"><i class="fa fa-fw fa-users"></i> Customers</a>
            <div class="MENU_HEADER">SETTINGS</div>
            <a href="profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="help.php"><i class="fa fa-fw fa-question-circle"></i> Help</a>
            <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Listings</h1>
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

            <div class="LISTINGS_CONTAINER">
                <header class="LISTINGS_HEADER">
                    <h2>Package Management</h2>
                        <a href="listings_add_package.php" id="ADD_PACKAGE"><i class="fas fa-plus"></i> Add Package</a>
                </header>

                <!-- Example Package -->
                <div class="PACKAGE">
                    <div class="LEFT_PART">
                        <div class="PACKAGE_THUMBNAIL">
                            <img src="assets/images/2srevadilla.jpg" alt="Package Thumbnail">
                        </div>
                    </div>
                    <div class="RIGHT_PART">
                        <!-- Package Name and Status -->
                        <div class="PACKAGE_HEADER">
                            <div class="PACKAGE_NAME">
                                <h3 id="PACKAGE_NAME"><?php echo "Basic Package" ?></h3>
                            </div>

                        </div>
                        <!-- Package Description and Features -->
                        <div class="PACKAGE_CONTENT">
                            <div class="PACKAGE_DESCRIPTION">
                                <span id="PACKAGE_DESCRIPTION"><?php echo "Package Description goes here" ?></span>
                            </div>
                             <div class="PACKAGE_FEATURES">
                                <h3>Key Features</h3>
                                <ul>
                                    <li><i class="fas fa-check"></i> <?php echo "Provides for 50 guests" ?></li>
                                    <li><i class="fas fa-check"></i> <?php echo "Another feature" ?></li>
                                    <li><i class="fas fa-check"></i> <?php echo "Another feature" ?></li>
                                </ul>
                            </div>
                        </div>
                        <!-- Package Details -->
                        <div class="BESIDE_FIELDS">
                            <div class="BESIDE_FIELD">
                                <div class="PACKAGE_DETAIL">
                                    <h3 id="label"><i class="fas fa-tag"></i>Starting Price</h3>
                                    <p id="STARTING_PRICE"> ₱<?php echo "1,299" ?></p>
                                </div>
                            </div>
                            <div class="BESIDE_FIELD">
                                <div class="PACKAGE_DETAIL">
                                    <h3 id="label"><i class="fas fa-users"></i>Capacity</h3>
                                    <p id="CAPACITY"> Good for <?php echo "50" ?> pax</p>
                                </div>
                            </div>
                            <div class="BESIDE_FIELD">
                                <div class="PACKAGE_DETAIL">
                                    <h3 id="label"><i class="fas fa-calendar-alt"></i>Date Created</h3>
                                    <p id="DATE_CREATED"> <?php echo "March 17, 2025" ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Package Actions -->
                        <div class="PACKAGE_ACTIONS">
                            <button id="PACKAGE_EDIT"><i class="fas fa-edit"></i> Edit</button>
                            <button id="PACKAGE_DELETE"><i class="fas fa-trash"></i> Delete</button>
                        </div>
                    </div>
                </div>

            </div>




</body>
</html>