<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['client_id'])) {
    session_start();

    $client_id = $_POST['client_id'];

    // Fetch client details before deletion
    $clientQuery = $conn->prepare("SELECT name FROM clients WHERE client_id = ?");
    $clientQuery->bind_param("i", $client_id);
    $clientQuery->execute();
    $clientResult = $clientQuery->get_result();
    $client = $clientResult->fetch_assoc();
    $clientQuery->close();

    if (!$client) {
        echo json_encode(["status" => "error", "message" => "Client not found."]);
        exit;
    }

    // Delete the client
    $stmt = $conn->prepare("DELETE FROM clients WHERE client_id = ?");
    $stmt->bind_param("i", $client_id);

    if ($stmt->execute()) {
        // Insert a notification for the admin
        $message = "Client <b>{$client['name']}</b> has been deleted.";
        $notifStmt = $conn->prepare("INSERT INTO notifications (user_type, admin_id, message, status) VALUES ('Admin', ?, ?, 'Unread')");
        $notifStmt->bind_param("is", $_SESSION['user_id'], $message);
        $notifStmt->execute();
        $notifStmt->close();

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete client."]);
    }

    $stmt->close();
    $conn->close();
}
?>
