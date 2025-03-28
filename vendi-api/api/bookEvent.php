<?php
require_once('../config/db.php');

$data = json_decode(file_get_contents('php://input'), true);

$eventId = $data['event_id'];
$vendorId = $data['vendor_id'];

$sql = "INSERT INTO event_bookings (event_id, vendor_id, booked_at) 
        VALUES ('$eventId', '$vendorId', NOW())";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Event booked successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error booking event: ' . $conn->error]);
}

$conn->close();
?>
