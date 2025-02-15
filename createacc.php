<?php
require_once 'vendor/autoload.php'; // Include JWT library

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$host = 'localhost';
$dbname = 'vendi_db';
$username = 'root';
$password = '';

// Secret key for JWT
$secret_key = "2169b56560cdbff74b4c9050c2db773ee0747800b27a78781a4e84aceb10a4450ff8049c25bb276a077c1835c862922aaa799138c2b2bcfeec028954cb12540c8f7d654fd6d18816497884937bee07c58e07b971eccce646af12557ee488a30a"; // Replace with a strong, random key.  Store securely!

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo json_encode(["success" => 1, "message" => "Welcome to Glutton"]);
} catch (PDOException $e) {
    echo json_encode(["success" => 0, "message" => "Failed To Connect The Server" . $e->getMessage()]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null) {
        echo json_encode(["success" => 0, "message" => "Invalid JSON"]);
        exit;
    }

    $errors = [];

    // Validate inputs
    if (empty($data['firstname'])) {
        $errors[] = "firstname is required";
    }
    if (empty($data['lastname'])) {
        $errors[] = "lastname is required";
    }
    if (empty($data['mobilenumber'])) {
        $errors[] = "mobilenumber is required";
    }
    if (empty($data['email'])) {
        $errors[] = "Email is required";
    }
    if (empty($data['password'])) {
        $errors[] = "Password is required";
    }
    if (empty($data['confirmPassword'])) {
        $errors[] = "Confirm Password is required";
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }

    if (strlen($data['password']) < 8 && strlen($data['password']) > 16) {
        $errors[] = "Password must be at least 6 characters long";
    }

    if ($data['password'] !== $data['confirmPassword']) {
        $errors[] = "Passwords do not match";
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE email = :email");
        $stmt->bindParam(':email', $data['email']);
        $stmt->execute();
        $emailCount = $stmt->fetchColumn();

        if ($emailCount > 0) {
            $errors[] = "Email is already in use";
        }
    }

    // If no errors, proceed to insert the player
    if (empty($errors)) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO clients (firstname, lastname, mobilenumber, email, password) VALUES (:firstname, :lastname, :mobilenumber, :email, :password)");
        $stmt->bindParam(':firstname', $data['firstname']);
        $stmt->bindParam(':lastname', $data['lastname']);
        $stmt->bindParam(':mobilenumber', $data['mobilenumber']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);

        // Check if the insert was successful
        try {
            if ($stmt->execute()) {

                // Generate JWT Token
                $payload = array(
                    "iss" => "your_domain.com", // Replace with your domain
                    "aud" => "your_domain.com", // Replace with your domain
                    "iat" => time(),
                    "nbf" => time(),
                    "exp" => time() + (60 * 60), // Token valid for 1 hour
                    "data" => array(
                        "username" => $data['username'],
                        "firstname" => $data['firstname'],
                        "lastname" => $data['lastname'],
                        "email" => $data['email']
                    )
                );

                $jwt = JWT::encode($payload, $secret_key, 'HS256');

                echo json_encode(["success" => 1, "message" => "Player Created Successfully", "token" => $jwt]);
            } else {
                echo json_encode(["success" => 0, "message" => "Failed to create player"]);
            }
        } catch (PDOException $e) {
            // Log the error message for debugging
            error_log("SQL Error: " . $e->getMessage());
            echo json_encode(["success" => 0, "message" => "Error executing query: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => 0, "errors" => $errors]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => 0, "message" => "Method not allowed"]);
}
?>
