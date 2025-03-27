<?php
$conn = new mysqli("localhost", "root", "", "janrich_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$id = $_GET['vendors_id']; // Get the vendor ID
$query = "SELECT vendors_profile FROM vendors WHERE vendors_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($imageData);
$stmt->fetch();
$stmt->close();
$conn->close();
header("Content-Type: image/png"); // Change MIME type if needed
echo $imageData;
?>
