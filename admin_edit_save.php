<?php
// admin_edit_save.php — Save edited product to database
// Phone & Accessory Price Lookup System

header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

// ── Read & sanitise ───────────────────────────────────────────
$id       = (int)trim($_POST['id']       ?? 0);
$category = trim($_POST['category']      ?? '');
$brand    = trim($_POST['brand']         ?? '');
$series   = trim($_POST['series']        ?? '') ?: null;
$type     = trim($_POST['type']          ?? '') ?: null;
$variant  = trim($_POST['variant']       ?? '') ?: null;
$price    = trim($_POST['price']         ?? '');
$quantity = (int)($_POST['quantity']     ?? 0);
$keywords = trim($_POST['keywords']      ?? '') ?: null;

// ── Validation ────────────────────────────────────────────────
$errors = [];

if ($id <= 0) $errors[] = 'Invalid product ID.';

$validCategories = ['iPhone', 'Android', 'Accessories'];
if (!in_array($category, $validCategories, true)) $errors[] = 'Invalid category.';
if ($brand === '') $errors[] = 'Brand is required.';
if (!is_numeric($price) || (float)$price <= 0) $errors[] = 'A valid price is required.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}

// ── Auto-regenerate full_name ─────────────────────────────────
// Phones:      brand + series + variant
// Accessories: brand + type   + variant
$middle    = ($category !== 'Accessories') ? $series : $type;
$full_name = trim($brand . ' ' . $middle . ' ' . $variant);

// ── Update database ───────────────────────────────────────────
$sql = "
    UPDATE products SET
        category  = :category,
        brand     = :brand,
        series    = :series,
        type      = :type,
        variant   = :variant,
        full_name = :full_name,
        price     = :price,
        quantity  = :quantity,
        keywords  = :keywords
    WHERE id = :id
";

try {
    $pdo  = getDB();

    // Confirm product exists first
    $check = $pdo->prepare("SELECT id FROM products WHERE id = :id");
    $check->execute([':id' => $id]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found.']);
        exit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':category'  => $category,
        ':brand'     => $brand,
        ':series'    => $series,
        ':type'      => $type,
        ':variant'   => $variant,
        ':full_name' => $full_name,
        ':price'     => (float)$price,
        ':quantity'  => $quantity,
        ':keywords'  => $keywords,
        ':id'        => $id,
    ]);

    echo json_encode([
        'success'   => true,
        'id'        => $id,
        'full_name' => $full_name,
        'price'     => (float)$price,
        'message'   => "'{$full_name}' updated successfully.",
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error. Could not save changes.']);
}
