<?php
session_start();

if (!isset($_SESSION['businessname'])) {
    header("Location: dashboard.php");
    exit();
}

// Simulated customer data
$customers = [
    1 => [ // Customer ID 1
        'profile_pic' => 'assets/images/tiara.png',
        'client_name' => 'John Marston',
        'client_email' => 'marston@gmail.com',
        'mobile_number' => '+1234567890',
        'booking_history' => [
            ['date' => '2023-10-01', 'service' => 'Basic Package', 'status' => 'Completed'],
            ['date' => '2023-10-05', 'service' => 'Basic Package', 'status' => 'Scheduled'],
        ],
    ],
    2 => [ // Customer ID 2
        'profile_pic' => 'assets/images/tiara.png',
        'client_name' => 'Arthur Morgan',
        'client_email' => 'arthur@gmail.com',
        'mobile_number' => '+0987654321',
        'booking_history' => [
            ['date' => '2023-09-28', 'service' => 'Basic Package', 'status' => 'Completed'],
            ['date' => '2023-10-10', 'service' => 'Basic Package', 'status' => 'Pending'],
        ],
    ],
];

// Get the customer ID from the URL
$customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if the customer exists
if (!isset($customers[$customerId])) {
    header("Location: customers.php");
    exit();
}

$customer = $customers[$customerId];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer History | Vendi</title>
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="notifications.css">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="customers.css?v=<?php echo time(); ?>">
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
                    <a href="bookings.php"><i class="fa fa-fw fa-calendar"></i> Bookings</a>
                    <a href="customers.php" class="NAV_ACTIVE"><i class="fa fa-fw fa-users"></i> <span> Customers</span></a>
            <div class="MENU_HEADER">SETTINGS</div>
                    <a href="profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
                    <a href="help.php"><i class="fa fa-fw fa-question-circle"></i> Help</a>
                    <a href="logout.php" class="LOGOUT"><i class="fa fa-fw fa-sign-out-alt"></i> Log Out</a>
        </div>
        
        <!-- Dashboard Content -->
        <div class="DASHBOARD" id="DASHBOARD">
            <div class="UPPER">
                <div class="LEFT_UPPER">
                    <h1 class="DASHBOARD_TITLE"><a href="customers.php" id="BREADCRUMB">Customers /</a> History</h1>
                </div>

                <div class="RIGHT_UPPER">
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

            <!-- Customer Booking History -->
                <div class="CUSTOMERS_CONTAINER">
                    <header class="CUSTOMERS_HEADER">
                        <h2>Booking History: <?php echo $customer['client_name']; ?></h2>
                        <a href="customers.php" id="GO_BACK"><i class="fas fa-arrow-left"></i> Go Back</a>
                    </header>
                </div> 
            
                <div class="CUSTOMER_TABLE">
                    <table>
                        <thead>
                            <tr>
                                <th><i class="fa fa-calendar-alt"></i> Date</th>
                                <th><i class="fa fa-box"></i> Selected Package</th>
                                <th><i class="fa fa-info-circle"></i> Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customer['booking_history'] as $booking): ?>
                                <tr>
                                    <td><?php echo $booking['date']; ?></td>
                                    <td><?php echo $booking['service']; ?></td>
                                    <td><?php echo $booking['status']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>