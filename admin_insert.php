<?php
// ============================================================
// admin_insert.php — Add Product (Admin Panel Backend)
// Phone & Accessory Price Lookup System
// ============================================================

header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

// ── Read & sanitise inputs ────────────────────────────────────
$category = trim($_POST['category'] ?? '');
$brand    = trim($_POST['brand']    ?? '');
$series   = trim($_POST['series']   ?? '') ?: null;
$type     = trim($_POST['type']     ?? '') ?: null;
$variant  = trim($_POST['variant']  ?? '') ?: null;
$price    = trim($_POST['price']    ?? '');
$quantity = (int)($_POST['quantity'] ?? 0);
$keywords = trim($_POST['keywords'] ?? '') ?: null;

// ── Validation ────────────────────────────────────────────────
$errors = [];

$validCategories = ['iPhone', 'Android', 'Accessories'];
if (!in_array($category, $validCategories, true)) {
    $errors[] = 'Invalid category.';
}
if ($brand === '') {
    $errors[] = 'Brand is required.';
}
if (!is_numeric($price) || (float)$price <= 0) {
    $errors[] = 'A valid price is required.';
}

// For phones: series is mandatory. For accessories: type is mandatory.
if (in_array($category, ['iPhone', 'Android'], true) && empty($series)) {
    $errors[] = 'Series is required for phone products.';
}
if ($category === 'Accessories' && empty($type)) {
    $errors[] = 'Type is required for accessories.';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}

// ── AUTO-GENERATE full_name (mandatory, never typed manually) ─
// Phones:      brand + series + variant
// Accessories: brand + type   + variant
$middle    = ($category !== 'Accessories') ? $series : $type;
$full_name = trim($brand . ' ' . $middle . ' ' . $variant);

// ── Insert into database ──────────────────────────────────────
$sql = "
    INSERT INTO products
        (category, brand, series, type, variant, full_name, price, quantity, keywords)
    VALUES
        (:category, :brand, :series, :type, :variant, :full_name, :price, :quantity, :keywords)
";

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':category' => $category,
        ':brand'    => $brand,
        ':series'   => $series,
        ':type'     => $type,
        ':variant'  => $variant,
        ':full_name'=> $full_name,
        ':price'    => (float)$price,
        ':quantity' => $quantity,
        ':keywords' => $keywords,
    ]);

    $newId = $pdo->lastInsertId();

    echo json_encode([
        'success'   => true,
        'id'        => $newId,
        'full_name' => $full_name,
        'message'   => "Product '{$full_name}' added successfully.",
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error. Could not save product.']);
}
