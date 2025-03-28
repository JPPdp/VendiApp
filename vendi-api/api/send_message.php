<?php
require_once('../config/db.php');

$chatId = $_POST['chat_id'];
$senderId = $_POST['sender_id'];
$messageText = $_POST['message_text'];

$sql = "INSERT INTO messages (chat_id, sender_id, message_text, sent_at) 
        VALUES ('$chatId', '$senderId', '$messageText', NOW())";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Message sent successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error sending message: ' . $conn->error]);
}

$conn->close();
?>
