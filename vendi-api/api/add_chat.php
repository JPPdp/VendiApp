<?php
// /api/add_chat.php
include_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['sender']) && !empty($data['receiver']) && !empty($data['message'])) {
    $sender = $conn->real_escape_string($data['sender']);
    $receiver = $conn->real_escape_string($data['receiver']);
    $message = $conn->real_escape_string($data['message']);
    $timestamp = date("Y-m-d H:i:s");

    $query = "INSERT INTO chat_messages (sender, receiver, message, timestamp) 
            VALUES ('$sender', '$receiver', '$message', '$timestamp')";

    if ($conn->query($query)) {
        echo json_encode(["success" => true, "message" => "Message sent successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to send message: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
}
?>
