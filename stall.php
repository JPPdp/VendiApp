<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Sanitize and validate input parameters if any
    $event_items = isset($_GET['event_items']) ? sanitize_input($_GET['event_items']) : null;

    // Prepare SQL query
    $sql = "SELECT event_profile, event_items, item_description, item_prices FROM stalls";
    if ($$event_items) {
        $sql .= " WHERE event_items = :event_items";
    }

    try {
        $stmt = $pdo->prepare($sql);
        if ($event_items) {
            $stmt->bindParam(':event_items', $event_items, PDO::PARAM_INT);
        }
        $stmt->execute();
        $stalls = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "data" => $items]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error retrieving data: " . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
}
?>