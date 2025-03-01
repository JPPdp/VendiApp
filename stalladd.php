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

    $event_profile = sanitize_input($data['event_profile'] ?? '');
    $event_items = sanitize_input($data['event_items'] ?? '');
    $event_description = sanitize_input($data['item_description'] ?? '');
    $event_prices = sanitize_input($data['item_prices'] ?? '');

    // Validate inputs
    if (empty($event_profile) || empty($event_items) || empty($event_description) || empty($event_prices)) {
        echo json_encode(["success" => false, "message" => "Item, Description, Price are required"]);
        exit;
    }

    if (!is_numeric($price)) {
        echo json_encode(["success" => false, "message" => "Price must be a number"]);
        exit;
    }

    // Insert data into the database
    $stmt = $pdo->prepare("INSERT INTO stalls (name, description, price) VALUES (:name, :description, :price)");
    $stmt->bindParam(':event_profile', $event_profile);
    $stmt->bindParam(':event_items', $event_items);
    $stmt->bindParam(':item_description', $item_description);
    $stmt->bindParam(':item_prices', $item_prices);

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