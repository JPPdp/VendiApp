<?php
session_start();

if (!isset($_SESSION['businessname'])) {
    header("Location: dashboard.php");
    exit();
}

// Get the current hour (24-hour format)
$currentHour = date('H');

// Determine the greeting based on the time
if ($currentHour < 12) {
    $greeting = 'Good Morning!';
} elseif ($currentHour < 18) {
    $greeting = 'Good Afternoon!';
} else {
    $greeting = 'Good Evening!';
}

// Simulated customer data
$customers = [
    [
        'id' => 1, // Unique ID for each customer
        'profile_pic' => 'assets/images/tiara.png',
        'client_name' => 'John Marston',
        'client_email' => 'marston@gmail.com',
        'mobile_number' => '+1234567890',
    ],
    [
        'id' => 2, // Unique ID for each customer
        'profile_pic' => 'assets/images/tiara.png',
        'client_name' => 'Arthur Morgan',
        'client_email' => 'arthur@gmail.com',
        'mobile_number' => '+0987654321',
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers | Vendi</title>
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
                    <h1 class="DASHBOARD_TITLE">Customers</h1>
                </div>

                <div class="RIGHT_UPPER">
                    <div class="ACCOUNT">
                        <span class="HELLO"><?php echo $greeting; ?></span>
                        <a href="profile.php">
                            <img src="assets/images/tiara.png" alt="Profile Picture" class="PROFILE_PIC">
                        </a>    
                        <span class="BUSINESS_NAME"><?php echo htmlspecialchars($_SESSION['businessname']); ?></span>             
                    </div>
                </div>
            </div>

            <!-- Customer Details Table -->
            <div class="CUSTOMERS_CONTAINER">
                    <header class="CUSTOMERS_HEADER">
                        <h2>Customers Management</h2>
                    </header>
            </div> 

                <div class="CUSTOMER_TABLE">
                    <table>
                        <thead>
                            <tr>
                                <th><i class="fas fa-image"></i> Profile Picture</th>
                                <th><i class="fas fa-user"></i> Client Name</th>
                                <th><i class="fas fa-envelope"></i> Client Email</th>
                                <th><i class="fas fa-phone"></i> Mobile Number</th>
                                <th><i class="fas fa-history"></i> History</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td class="PROFILE_PIC">
                                        <img src="<?php echo $customer['profile_pic']; ?>" alt="Profile Picture" class="PROFILE_PIC">
                                    </td>
                                    <td class="CLIENT_NAME"><?php echo $customer['client_name']; ?></td>
                                    <td class="CLIENT_EMAIL"><?php echo $customer['client_email']; ?></td>
                                    <td class="MOBILE_NUMBER"><?php echo $customer['mobile_number']; ?></td>
                                    <td class="ACTION_BUTTONS">
                                        <a href="customers_history.php?id=<?php echo $customer['id']; ?>" class="VIEW_BUTTON">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <script src="dashboard.js"></script>
</body>
</html>