<?php
include '../db_connect.php';

header("Content-Type: application/json");

// Get all approved vendor packages
$sql = "SELECT v.business_name, p.package_id, p.package_name, p.package_size, p.price 
        FROM vendor_packages p 
        JOIN vendors v ON p.vendor_id = v.vendor_id 
        WHERE v.status = 'Approved'";
$result = $conn->query($sql);

$packages = [];
while ($row = $result->fetch_assoc()) {
    $packages[] = $row;
}

echo json_encode($packages);
?>
