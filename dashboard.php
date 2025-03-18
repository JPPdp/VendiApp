<?php
session_start();

if (!isset($_SESSION['businessname'])) {
    header("Location: dashboard.php");
    exit();
}

$status = "Pending"; // retrieve from a database

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
    <title>Dashboard | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="bookings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="notifications.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <div class="NAV_CONTAINER">
        <!-- Navigation Bar -->
        <div class="NAVIGATION_BAR">
            <div class="LOGO">
                <!--<div class="LOGO_ICON">
                    <img src="assets/images/VENDI_BG.png" alt="Logo">
                </div>-->
                <div class="LOGO_NAME">Vendi
                    <span>DASHBOARD</span>
                </div>
            </div>

            <div class="MENU_HEADER">MANAGEMENT</div>
                    <a href="#DASHBOARD" class="NAV_ACTIVE"><i class="	fas fa-stream"></i><span> Dashboard</span></a>
                    <a href="listings.php"><i class="fa fa-fw fa-store"></i> Listings</a>
                    <a href="bookings.php"><i class="fa fa-fw fa-calendar"></i> Bookings</a>
                    <a href="reports.php"><i class="fas fa-chart-pie"></i> Reports</a>
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
                    <h1 class="DASHBOARD_TITLE">Dashboard</h1>
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

            <div class="CONTENT">
                <div class="BOX PENDING">
                    <h3>Pending</h3>
                    <p>2,232</p>
                    <i class="far fa-clock"></i> <!-- Icon Pending -->
                </div>
                <div class="BOX SCHEDULED">
                    <h3>Scheduled</h3>
                    <p>2</p>
                    <i class="far fa-check-circle"></i> <!-- Icon Active -->
                </div>
                <div class="BOX COMPLETED">
                    <h3>Completed</h3>
                    <p>40</p>
                    <i class="fas fa-check-circle"></i> <!-- Icon Completed -->
                </div>
                <div class="BOX CANCELLED">
                    <h3>Cancelled</h3>
                    <p>5</p>
                    <i class="fas fa-times-circle"></i> <!-- Icon Cancelled -->
                </div>
            </div>

            <!-- Schedule Manager -->
            <div class="MAIN_CONTAINER">
                <div class="LEFT_MAIN">
                    <!-- Package Overview Section -->
                    <div class="PACKAGE_OVERVIEW">
                        <h2>Package Overview</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>Package Image</th>
                                    <th>Package Name</th>
                                    <th>Price</th>
                                    <th>Capacity</th>
                                    <th>Times Booked</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example Row (Replace with PHP loop to fetch data from the database) -->
                                <tr>
                                    <!-- Package Image -->
                                    <td class="PACKAGE_IMAGE">
                                        <img src="assets/images/2srevadilla.jpg" alt="Package 1">
                                    </td>
                                    <!-- Package Name -->
                                    <td class="PACKAGE_NAME">Basic Package</td>
                                    <!-- Package Price -->
                                    <td class="PACKAGE_PRICE">₱5,000</td>
                                    <!-- Package Capacity -->
                                    <td class="PACKAGE_CAPACITY">50 Guests</td>
                                    <!-- Times Booked -->
                                    <td class="TIMES_BOOKED">12</td>
                                </tr>
                                <!-- Add more rows dynamically using PHP -->
                            </tbody>   

                            <tbody>
                                <!-- Example Row (Replace with PHP loop to fetch data from the database) -->
                                <tr>
                                    <!-- Package Image -->
                                    <td class="PACKAGE_IMAGE">
                                        <img src="assets/images/2srevadilla.jpg" alt="Package 1">
                                    </td>
                                    <!-- Package Name -->
                                    <td class="PACKAGE_NAME">Basic Package</td>
                                    <!-- Package Price -->
                                    <td class="PACKAGE_PRICE">₱5,000</td>
                                    <!-- Package Capacity -->
                                    <td class="PACKAGE_CAPACITY">50 Guests</td>
                                    <!-- Times Booked -->
                                    <td class="TIMES_BOOKED">12</td>
                                </tr>
                                <!-- Add more rows dynamically using PHP -->
                            </tbody>   

                            <tbody>
                                <!-- Example Row (Replace with PHP loop to fetch data from the database) -->
                                <tr>
                                    <!-- Package Image -->
                                    <td class="PACKAGE_IMAGE">
                                        <img src="assets/images/2srevadilla.jpg" alt="Package 1">
                                    </td>
                                    <!-- Package Name -->
                                    <td class="PACKAGE_NAME">Basic Package</td>
                                    <!-- Package Price -->
                                    <td class="PACKAGE_PRICE">₱5,000</td>
                                    <!-- Package Capacity -->
                                    <td class="PACKAGE_CAPACITY">50 Guests</td>
                                    <!-- Times Booked -->
                                    <td class="TIMES_BOOKED">12</td>
                                </tr>
                                <!-- Add more rows dynamically using PHP -->
                            </tbody>   

                            <tbody>
                                <!-- Example Row (Replace with PHP loop to fetch data from the database) -->
                                <tr>
                                    <!-- Package Image -->
                                    <td class="PACKAGE_IMAGE">
                                        <img src="assets/images/2srevadilla.jpg" alt="Package 1">
                                    </td>
                                    <!-- Package Name -->
                                    <td class="PACKAGE_NAME">Basic Package</td>
                                    <!-- Package Price -->
                                    <td class="PACKAGE_PRICE">₱5,000</td>
                                    <!-- Package Capacity -->
                                    <td class="PACKAGE_CAPACITY">50 Guests</td>
                                    <!-- Times Booked -->
                                    <td class="TIMES_BOOKED">12</td>
                                </tr>
                                <!-- Add more rows dynamically using PHP -->
                            </tbody>   
                        </table>
                    </div>

                    <!-- Bookings Section -->
                    <div class="BOOKING_TABLE">
                        <h2>Booking Summary</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>Reference ID</th>
                                    <th>Client Name</th>
                                    <th>Client Email</th>
                                    <th>Event Date</th>
                                    <th>Event Location</th>
                                    <th>Package</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example -->
                                <tr>
                                    <td class="REFERENCE_ID">#12345</td>
                                    <td class="CLIENT_NAME">John Marston</td>
                                    <td class="CLIENT_EMAIL">marston@gmail.com</td>
                                    <td class="EVENT_DATE">11/1/25<br> <small>4:00 PM</small> </td>
                                    <td class="EVENT_LOCATION">Dagupan Convention Center</td>
                                    <td class="PACKAGE">Basic Package</td>
                                    <td class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></td>
                                    </tr>
                            </tbody>
                        </table>
                    </div>

                </div>


                <div class="RIGHT_MAIN">
                    <!-- Calendar -->
                    <div class="CALENDAR">
                        <h2>Calendar</h2>

                        <div class="CALENDAR_PLACEHOLDER">
                            <div class="WRAPPER">
                                <header>
                                    <div class="ICONS">
                                        <span id="PREV" class="ICON_CLASS"><i class="fa fa-caret-left"></i></span>
                                        <p class="CURRENT_DATE"></p>
                                        <span id="NEXT" class="ICON_CLASS"><i class="fa fa-caret-right"></i></span>
                                    </div>
                                </header>
                                <div class="CALENDAR_BODY">
                                    <ul class="WEEKS">
                                        <li>Sun</li>
                                        <li>Mon</li>
                                        <li>Tue</li>
                                        <li>Wed</li>
                                        <li>Thu</li>
                                        <li>Fri</li>
                                        <li>Sat</li>
                                    </ul>
                                    <ul class="DAYS"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- To-Do List -->
                    <div class="TODO_LIST">
                        <div class="TODO_HEADER">
                            <h2>To-Do List</h2>
                            <span class="ADD_TASK" id="ADD_TASK"><i class="fas fa-plus"></i></span>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="TODO_BODY">
                                <!-- Texts are auto added here -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div><!-- END | MAIN_CONTAINER -->
            
            
        </div>
    </div>

    <script src="dashboard.js"></script>
    
</body>
</html>