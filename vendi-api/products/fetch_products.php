<?php
include 'db_connect.php';

$category_id = $_GET['category_id'];
$limit = $_GET['limit'] ?? 10;

$sql = "SELECT product_id, product_name, price, image_url FROM products WHERE category_id = ? ORDER BY RAND() LIMIT ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $category_id, $limit);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
?>
