<?php
// admin_delete.php — Delete a product by ID
// Phone & Accessory Price Lookup System

header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

$id = (int)trim($_POST['id'] ?? 0);

if ($id <= 0) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid product ID.']);
    exit;
}

try {
    $pdo = getDB();

    // Get product name before deleting (for confirmation message)
    $stmt = $pdo->prepare("SELECT full_name FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found.']);
        exit;
    }

    // Delete
    $del = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $del->execute([':id' => $id]);

    echo json_encode([
        'success' => true,
        'message' => "'{$product['full_name']}' deleted successfully.",
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error. Could not delete product.']);
}
