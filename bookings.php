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
} elseif ($status == "Cancelled") {
    $statusClass = "STATUS_CANCELLED";
    $statusText = "Cancelled";
}

// Get the current hour (24-hour format)
$currentHour = date('H');

// Determine the greeting based on the time
if ($currentHour < 12) {
    $greeting = 'Good Morning,';
} elseif ($currentHour < 18) {
    $greeting = 'Good Afternoon,';
} else {
    $greeting = 'Good Evening,';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the status updates from the form
    $statusUpdates = $_POST['status'];

    // Example: Loop through the status updates and process them
    foreach ($statusUpdates as $referenceId => $newStatus) {
        // Here, you would typically update the database
        // Example SQL: UPDATE bookings SET status = '$newStatus' WHERE reference_id = '$referenceId';
        echo "Updated booking $referenceId to status: $newStatus<br>";
    }

    // Redirect back to the bookings page
    header("Location: bookings.php");
    exit();
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
                    <div class="ACCOUNT">
                        <span class="HELLO"><?php echo $greeting; ?></span>
                        <a href="profile.php">
                            <img src="assets/images/tiara.png" alt="Profile Picture" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['businessname']); ?> !</span>             
                    </div>
                </div>
            </div>

            <!-- Booking Details Table -->
            <div class="BOOKINGS_CONTAINER">
                <header class="BOOKINGS_HEADER">
                    <h2>Booking Details</h2>
                </header>
            </div>

                <div class="BOOKING_TABLE">
                    <form action="update_bookings.php" method="POST">
                        <table>
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> Reference ID</th>
                                    <th><i class="fas fa-user"></i> Client Name</th>
                                    <th><i class="fas fa-envelope"></i> Client Email</th>
                                    <th><i class="fas fa-phone"></i> Mobile Number</th>
                                    <th><i class="fas fa-calendar-alt"></i> Event Date</th>
                                    <th><i class="fas fa-map-marker-alt"></i> Event Location</th>
                                    <th><i class="fas fa-box"></i> Package</th>
                                    <th><i class="fas fa-info-circle"></i> Status</th>
                                    <th><i class="fas fa-cogs"></i> Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example Row 1 -->
                                <tr>
                                    <td class="REFERENCE_ID">#12345</td>
                                    <td class="CLIENT_NAME">John Marston</td>
                                    <td class="CLIENT_EMAIL">marston@gmail.com</td>
                                    <td class="MOBILE_NUMBER">+1234567890</td>
                                    <td class="EVENT_DATE">11/1/25<br> <small>4:00 PM</small></td>
                                    <td class="EVENT_LOCATION">Dagupan Convention Center</td>
                                    <td class="PACKAGE">Basic Package</td>
                                    <td class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></td>
                                    <td class="ACTION_BUTTONS">
                                        <select name="status[12345]" class="STATUS_DROPDOWN">
                                            <option value="Pending" <?php echo ($status == "Pending") ? "selected" : ""; ?>>Pending</option>
                                            <option value="Approved" <?php echo ($status == "Approved") ? "selected" : ""; ?>>Approve</option>
                                            <option value="Cancelled" <?php echo ($status == "Cancelled") ? "selected" : ""; ?>>Cancel</option>
                                        </select>
                                    </td>
                                </tr>
                                <!-- Example Row 2 -->
                                <tr>
                                    <td class="REFERENCE_ID">#67890</td>
                                    <td class="CLIENT_NAME">Arthur Morgan</td>
                                    <td class="CLIENT_EMAIL">arthur@gmail.com</td>
                                    <td class="MOBILE_NUMBER">+0987654321</td>
                                    <td class="EVENT_DATE">12/1/25<br> <small>5:00 PM</small></td>
                                    <td class="EVENT_LOCATION">Dagupan City Plaza</td>
                                    <td class="PACKAGE">Premium Package</td>
                                    <td class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></td>
                                    <td class="ACTION_BUTTONS">
                                        <select name="status[67890]" class="STATUS_DROPDOWN">
                                            <option value="Pending" <?php echo ($status == "Pending") ? "selected" : ""; ?>>Pending</option>
                                            <option value="Approved" <?php echo ($status == "Approved") ? "selected" : ""; ?>>Approve</option>
                                            <option value="Cancelled" <?php echo ($status == "Cancelled") ? "selected" : ""; ?>>Cancel</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="SUBMIT_BUTTON_CONTAINER">
                            <span id="SUBMIT_TEXT">Update booking status?</span>
                            <button type="submit" class="SUBMIT_BUTTON">Submit</button>
                        </div>                    
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="dashboard.js"></script>
    
</body>
</html>