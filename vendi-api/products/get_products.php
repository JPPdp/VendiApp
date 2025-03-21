<?php
include '../db_connect.php'; // Update path if necessary

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

$sql = "SELECT product_id, product_name, price, product_image FROM products WHERE category_id = ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $category_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
?>
