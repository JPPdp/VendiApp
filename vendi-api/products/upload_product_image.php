<?php
include 'db_connect.php';

// Get the image as binary
$product_image = file_get_contents($_FILES['product_image']['tmp_name']);
$vendor_id = $_POST['vendor_id'];
$category_id = $_POST['category_id'];
$product_name = $_POST['product_name'];
$price = $_POST['price'];
$description = $_POST['description'];

// Prepare the SQL statement
$sql = "INSERT INTO products (vendor_id, category_id, product_name, price, description, product_image, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iisdss", $vendor_id, $category_id, $product_name, $price, $description, $product_image);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Product added with image successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to upload product image."]);
}

$stmt->close();
$conn->close();
?>
