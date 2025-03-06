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
    <title>Dashboard | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="db_notifications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <div class="CONTAINER">
        <!-- Navigation Bar -->
        <div class="NAVIGATION_BAR">
            <div class="LOGO">
                <div class="LOGO_ICON">
                    <img src="assets/images/VENDI_BG.png" alt="Logo">
                </div>
                <div class="LOGO_NAME">Vendi
                    <span>DASHBOARD</span>
                </div>
            </div>

            <div class="MENU_HEADER">MANAGEMENT</div>
                    <a href="#DASHBOARD" class="NAV_ACTIVE"><i class="fa fa-fw fa-chart-bar"></i><span>Dashboard</span></a>
                    <a href="db_listings.html"><i class="fa fa-fw fa-store"></i> Listings</a>
                    <a href="db_bookings.html"><i class="fa fa-fw fa-calendar"></i> Bookings</a>
            <div class="MENU_HEADER">PREFERENCES</div>
                    <a href="db_profile.html"><i class="fa fa-fw fa-user"></i> Profile</a>
                    <a href="help.html"><i class="fa fa-fw fa-question-circle"></i> Help</a>
                    <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="BUSINESS_NAME">Welcome, <?php echo htmlspecialchars($_SESSION['businessname']); ?></h1>
                </div>

                <div class="RIGHT_UPPER">
                    <div class="SEARCH_BAR">
                        <input type="text" placeholder="Search...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="ACCOUNT">
                        <div class="NOTIFICATION">
                            <i class="fas fa-bell"></i>
                            <span class="NOTIFICATION_DOT"></span> <!-- Red dot for notifications -->
                        </div>
                        <i class="fas fa-question-circle"></i> <!-- Help Icon -->
                        <a href="db_profile.html">
                            <img src="assets/images/tiara.png" alt="Profile Picture" class="PROFILE_PIC">
                        </a>                  
                    </div>
                </div>
                
            </div>

            <div class="CONTENT">
                <div class="BOX PENDING">
                    <h3>Pending</h3>
                    <p>2</p>
                    <i class="far fa-clock"></i> <!-- Icon Pending -->
                </div>
                <div class="BOX ACTIVE">
                    <h3>Active</h3>
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
            <div class="CALENDAR_TODO_CONTAINER">
                <!-- Calendar -->
                <div class="CALENDAR">
                    <h2>Calendar</h2>
                    <div class="CALENDAR_PLACEHOLDER">
                        <div class="WRAPPER">
                            <header>
                                <p class="CURRENT_DATE"></p>
                                <div class="ICONS">
                                    <span id="PREV" class="ICON_CLASS"><i class="fas fa-caret-square-left"></i></span>
                                    <span id="NEXT" class="ICON_CLASS"><i class="fas fa-caret-square-right	"></i></span>
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
        
            <div class="BOOKING_TABLE">
    <h2>Booking Summary</h2>
    <table>
        <thead>
            <tr>
                <th>Reference ID</th>
                <th>Event Date <i class="fa fa-filter"></i></th>
                <th>Client Name</th>
                <th>Client Email</th>
                <th>Mobile Number</th>
                <th>Status</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            <!-- <?php
            // Check if bookings present
            // if ($result->num_rows > 0) {
            //     // Output data for each row
            //     while ($row = $result->fetch_assoc()) {
            //         echo "<tr>";
            //         echo "<td class='REFERENCE_ID'>#" . htmlspecialchars($row['reference_id']) . "</td>";
            //         echo "<td class='EVENT_DATE'>" . htmlspecialchars($row['event_date']) . "</td>";
            //         echo "<td class='DURATION'>" . htmlspecialchars($row['duration']) . "</td>";
            //         echo "<td class='CLIENT_NAME'>" . htmlspecialchars($row['client_name']) . "</td>";
            //         echo "<td class='CLIENT_EMAIL'>" . htmlspecialchars($row['client_email']) . "</td>";
            //         echo "<td class='STATUS' id='" . htmlspecialchars($row['status']) . "'>" . htmlspecialchars($row['status']) . "</td>";
            //         echo "<td class='CREATED_DATE'>" . htmlspecialchars($row['created_date']) . "</td>";
            //         echo "</tr>";
            //     }
            // } else {
            //     // If no bookings, display message
            //     echo "<tr><td colspan='7'>No bookings found.</td></tr>";
            // }

            // Close the database connection
            // $conn->close();
            ?> -->

            <!-- Example Row -->
            <tr>
                <td class="REFERENCE_ID">#12345</td>
                <td class="EVENT_DATE">11/1/25<br> <small>4:00 PM</small> </td>
                <td class="CLIENT_NAME">John Marston</td>
                <td class="CLIENT_EMAIL">marston@gmail.com</td>
                <td class="MOBILE_NUMBER">09191290321</td>
                <td class="STATUS" id="PENDING">Pending</td>
                <td class="CREATED_DATE">10/29/25 <br> <small>12:00 PM</small></td>
            </tr>
        </tbody>
    </table>
</div>  
        </div>
    </div>

    <script src="dashboard.js"></script>
    
</body>
</html>