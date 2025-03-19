<?php
session_start();

if (!isset($_SESSION['businessname'])) {
    header("Location: dashboard.php");
    exit();
}

$status = "Pending"; // This could be retrieved from a database

// Conditional class or ID based on status
if ($status == "Pending") {
    $statusClass = "STATUS_PENDING";
    $statusText = "Pending";
} elseif ($status == "Accepted") {
    $statusClass = "STATUS_ACCEPTED";
    $statusText = "Accepted";
} elseif ($status == "Rejected") {
    $statusClass = "STATUS_REJECTED";
    $statusText = "Rejected";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="notifications.css">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="bookings.css?v=<?php echo time(); ?>">
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
                    <a href="listings.php"><i class="fa fa-fw fa-store"></i> Listings</a>
                    <a href="bookings.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-calendar"></i> <span> Bookings</span></a>
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
                    <h1 class="DASHBOARD_TITLE">Bookings</h1>
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

            <!-- Booking Details Table -->
            <div class="MAIN_CONTAINER">
                <div class="BOOKING_TABLE">
                    <h2>Booking Details</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Reference ID</th>
                                <th>Client Name</th>
                                <th>Client Email</th>
                                <th>Mobile Number</th>
                                <th>Event Date</th>
                                <th>Event Location</th>
                                <th>Package</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Example -->
                            <tr>
                                <td class="REFERENCE_ID">#12345</td>
                                <td class="CLIENT_NAME">John Marston</td>
                                <td class="CLIENT_EMAIL">marston@gmail.com</td>
                                <td class="MOBILE_NUMBER">+1234567890</td>
                                <td class="EVENT_DATE">11/1/25<br> <small>4:00 PM</small> </td>
                                <td class="EVENT_LOCATION">Dagupan Convention Center</td>
                                <td class="PACKAGE">Basic Package</td>
                                <td class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></td>
                                <td class="ACTION_BUTTONS">
                                    <button class="DECLINE_BUTTON">Decline</button>
                                    <button class="ACCEPT_BUTTON">Accept</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="REFERENCE_ID">#12345</td>
                                <td class="CLIENT_NAME">John Marston</td>
                                <td class="CLIENT_EMAIL">marston@gmail.com</td>
                                <td class="MOBILE_NUMBER">+1234567890</td>
                                <td class="EVENT_DATE">11/1/25<br> <small>4:00 PM</small> </td>
                                <td class="EVENT_LOCATION">Dagupan Convention Center</td>
                                <td class="PACKAGE">Basic Package</td>
                                <td class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></td>
                                <td class="ACTION_BUTTONS">
                                    <button class="DECLINE_BUTTON">Decline</button>
                                    <button class="ACCEPT_BUTTON">Accept</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="dashboard.js"></script>
    
</body>
</html>