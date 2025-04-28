<?php
include '../db_connect.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['client_id'], $data['package_id'])) {
    $client_id = $data['client_id'];
    $package_id = $data['package_id'];
    $reference_id = uniqid('BOOK_');
    $status = 'Pending';

    // Get vendor ID from package
    $stmt = $conn->prepare("SELECT vendor_id FROM vendor_packages WHERE package_id = ?");
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $package = $result->fetch_assoc();

    if ($package) {
        $vendor_id = $package['vendor_id'];

        // Insert booking
        $stmt = $conn->prepare("INSERT INTO bookings (reference_id, client_id, vendor_id, package_id, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiis", $reference_id, $client_id, $vendor_id, $package_id, $status);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Booking successful"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error creating booking"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Package not found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}
?>
