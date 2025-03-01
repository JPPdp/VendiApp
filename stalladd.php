<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

error_reporting(E_ALL);
ini_set('display_errors', 1);


// Database connection parameters
$host = 'localhost';
$dbname = 'vendi_db';
$username = 'root';
$password = '';

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null) {
        echo json_encode(["success" => false, "message" => "Invalid JSON"]);
        exit;
    }

    $name = sanitize_input($data['name'] ?? '');
    $description = sanitize_input($data['description'] ?? '');
    $price = sanitize_input($data['price'] ?? '');

    // Validate inputs
    if (empty($name) || empty($description) || empty($price)) {
        echo json_encode(["success" => false, "message" => "Name, description, and price are required"]);
        exit;
    }

    if (!is_numeric($price)) {
        echo json_encode(["success" => false, "message" => "Price must be a number"]);
        exit;
    }

    // Insert data into the database
    $stmt = $pdo->prepare("INSERT INTO items (name, description, price) VALUES (:name, :description, :price)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Item added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add item"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
}
?>