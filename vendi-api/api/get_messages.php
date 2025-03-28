<?php
require_once('../config/db.php');

$chatId = $_GET['chat_id'];

$sql = "SELECT sender_id, message_text, sent_at FROM messages WHERE chat_id='$chatId' ORDER BY sent_at ASC";
$result = $conn->query($sql);

$messages = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    echo json_encode($messages);
} else {
    echo json_encode([]);
}

$conn->close();
?>
