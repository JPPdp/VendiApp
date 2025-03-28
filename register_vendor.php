<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $business_name = $_POST['business_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $mobile_number = $_POST['mobile_number'];
    $address = $_POST['address'];
    $service_option = $_POST['service_option'];
    $business_description_short = $_POST['business_description_short'];
    $business_description_long = $_POST['business_description_long'];

    // Handle Business Document Upload
    $target_dir = "uploads/vendors/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $business_document = $target_dir . basename($_FILES["business_document"]["name"]);
    move_uploaded_file($_FILES["business_document"]["tmp_name"], $business_document);

    // Insert Vendor into Database (Status set to Pending)
    $sql = "INSERT INTO vendors (business_name, email, password, mobile_number, address, service_option, business_description_short, business_description_long, business_document, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $business_name, $email, $password, $mobile_number, $address, $service_option, $business_description_short, $business_description_long, $business_document);

    if ($stmt->execute()) {
        echo "<script>alert('Vendor Registered Successfully! Waiting for Admin Approval.');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Registration</title>
</head>
<body>
    <h2>Register as Vendor</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Business Name:</label>
        <input type="text" name="business_name" required><br>

        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <label>Mobile Number:</label>
        <input type="text" name="mobile_number" required><br>

        <label>Address:</label>
        <textarea name="address" required></textarea><br>

        <label>Service Option:</label>
        <select name="service_option">
            <option value="Food">Food</option>
            <option value="Beverages">Beverages</option>
            <option value="Entertainment">Entertainment</option>
        </select><br>

        <label>Short Description:</label>
        <input type="text" name="business_description_short" required><br>

        <label>Long Description:</label>
        <textarea name="business_description_long" required></textarea><br>

        <label>Business Document (PDF or Image):</label>
        <input type="file" name="business_document" accept=".pdf,.jpg,.jpeg,.png" required><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
