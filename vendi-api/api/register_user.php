<?php
// Database connection details
$host = "localhost";
$user = "root";
$password = "";
$database = "vendiapp";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

// Get data from POST request
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone_number = $_POST['phone_number'];
$password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);

// Check if email or phone number already exists
$sql_check = "SELECT * FROM users WHERE email = ? OR phone_number = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ss", $email, $phone_number);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email or phone number already exists."]);
    exit();
}

// Insert new user
$sql_insert = "INSERT INTO users (full_name, email, phone_number, password_hash) VALUES (?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("ssss", $full_name, $email, $phone_number, $password_hash);

if ($stmt_insert->execute()) {
    echo json_encode(["success" => true, "message" => "User registered successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Error registering user."]);
}

// Close connection
$conn->close();
?>
