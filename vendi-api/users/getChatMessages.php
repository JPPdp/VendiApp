<?php
// getChatMessages.php
include 'db_connect.php';

$sender = $_GET['sender'];
$receiver = $_GET['receiver'];

$query = "SELECT sender, receiver, message, timestamp FROM chat_messages WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?) ORDER BY timestamp ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $sender, $receiver, $receiver, $sender);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>
