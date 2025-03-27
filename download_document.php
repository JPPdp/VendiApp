<?php
if (isset($_GET['vendor_id'])) {
    $vendor_id = intval($_GET['vendor_id']);

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "janrich_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch the business document
    $stmt = $conn->prepare("SELECT business_documents FROM pending_vendors WHERE vendors_id = ?");
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $stmt->bind_result($business_document);
    $stmt->fetch();

    if ($business_document) {
        // Set headers to display the image in the browser
        header("Content-Type: image/"); // Change this if the image is not JPEG
        echo $business_document;
    } else {
        echo "No document found.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>