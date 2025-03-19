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
    <title>Help | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="notifications.css">
    <link rel="stylesheet" href="profile.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="help.css?v=<?php echo time(); ?>">
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
            <a href="customers.php"><i class="fa fa-fw fa-users"></i> Customers</a>

            <div class="MENU_HEADER">SETTINGS</div>
            <a href="profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="#" class="NAV_ACTIVE"><i class="fa fa-fw fa-question-circle"></i> Help</a>
            <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>

        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE">Help</h1>
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

            <!-- Help Content -->
            <div class="HELP_CONTAINER">
                <h1 class="HELP_TITLE">Need Help?</h1>
                <p class="HELP_SUBTITLE">If you have any questions or concerns, please fill out the form below to contact us. We'll get back to you as soon as possible.</p>

                <!-- Help Form -->
                <form id="HELP_FORM" action="#" method="post">
                    <label for="NAME">Business Name</label>
                    <input type="text" id="businessname" name="businessname" value="<?php echo htmlspecialchars($_SESSION['businessname']); ?>" readonly>

                    <label for="EMAIL">Email</label>
                    <input type="email" id="VENDOR_EMAIL" name="EMAIL" value="email@.email.com" readonly>

                    <label for="MESSAGE">Message</label>
                    <textarea id="MESSAGE" name="MESSAGE" rows="5" placeholder="Enter your message here..." required></textarea>

                    <button type="submit">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
