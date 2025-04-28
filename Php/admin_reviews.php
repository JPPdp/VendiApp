<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: login.php");
    exit;
}

include 'db_connect.php';

// Fetch Vendor Reviews for Admin to Manage
$reviews = $conn->query("
    SELECT r.review_id, c.name AS client_name, v.business_name AS vendor_name, r.rating, r.review, r.created_at
    FROM vendor_reviews r
    JOIN clients c ON r.client_id = c.client_id
    JOIN vendors v ON r.vendor_id = v.vendor_id
    ORDER BY r.created_at DESC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vendor Reviews</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-btn {
            color: red;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 16px;
        }
    </style>
</head>
<body>

<h2>Vendor Reviews Management</h2>

<!-- Display success/error messages -->
<?php if (isset($_SESSION['success_message'])): ?>
    <p style="color: green;"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <p style="color: red;"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Client Name</th>
            <th>Vendor Name</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $reviews->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['client_name']); ?></td>
                <td><?= htmlspecialchars($row['vendor_name']); ?></td>
                <td><?= htmlspecialchars($row['rating']); ?></td>
                <td><?= htmlspecialchars($row['review']); ?></td>
                <td><?= htmlspecialchars($row['created_at']); ?></td>
                <td>
                    <form action="delete_review.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');">
                        <input type="hidden" name="review_id" value="<?= $row['review_id']; ?>">
                        <button type="submit" class="delete-btn">🗑️ Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>
<?php
