<?php
// admin_list.php — Returns all products as JSON for admin table
// Phone & Accessory Price Lookup System

header('Content-Type: application/json');
require_once 'config.php';

try {
    $pdo  = getDB();
    $stmt = $pdo->query("SELECT id, category, brand, series, type, variant, full_name, price, quantity FROM products ORDER BY created_at DESC");
    $rows = $stmt->fetchAll();
    echo json_encode($rows);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error.']);
}
