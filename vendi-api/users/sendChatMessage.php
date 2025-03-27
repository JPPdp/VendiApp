<?php
// sendChatMessage.php
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->sender) && isset($data->receiver) && isset($data->message) && isset($data->timestamp)) {
    $sender = $data->sender;
    $receiver = $data->receiver;
    $message = $data->message;
    $timestamp = $data->timestamp;

    $query = "INSERT INTO chat_messages (sender, receiver, message, timestamp) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $sender, $receiver, $message, $timestamp);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Message sent successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to send message"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid input data"]);
}
?>
