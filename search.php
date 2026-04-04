<?php
// ============================================================
// search.php — Live Search Endpoint (AJAX)
// Phone & Accessory Price Lookup System
// ============================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config.php';

// ── Input sanitisation ────────────────────────────────────────
$query = trim($_GET['q'] ?? '');

if ($query === '' || mb_strlen($query) < 1) {
    echo json_encode(['results' => [], 'grouped' => []]);
    exit;
}

// ── Search SQL (strict specification) ────────────────────────
$sql = "
    SELECT *
    FROM products
    WHERE LOWER(full_name) LIKE LOWER(CONCAT('%', :q1, '%'))
       OR LOWER(brand)     LIKE LOWER(CONCAT('%', :q2, '%'))
       OR LOWER(series)    LIKE LOWER(CONCAT('%', :q3, '%'))
       OR LOWER(type)      LIKE LOWER(CONCAT('%', :q4, '%'))
       OR LOWER(variant)   LIKE LOWER(CONCAT('%', :q5, '%'))
       OR LOWER(keywords)  LIKE LOWER(CONCAT('%', :q6, '%'))
    ORDER BY category, series, variant
";

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':q1' => $query,
        ':q2' => $query,
        ':q3' => $query,
        ':q4' => $query,
        ':q5' => $query,
        ':q6' => $query,
    ]);
    $rows = $stmt->fetchAll();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error. Please try again.']);
    exit;
}

// ── Group results logically ───────────────────────────────────
// Group key: for phones  → series  (e.g. "iPhone 13")
//            for accessories → type (e.g. "Power Bank")

$grouped = [];

foreach ($rows as $row) {
    if ($row['category'] === 'Accessories') {
        $groupKey   = $row['type']   ?? 'Accessories';
        $groupLabel = $row['type']   ?? 'Accessories';
        $icon       = '🔌';
    } else {
        $groupKey   = $row['series'] ?? $row['brand'];
        $groupLabel = ($row['brand'] . ' ' . $row['series']) ?? $row['brand'];
        $icon       = $row['category'] === 'iPhone' ? '📱' : '📱';
    }

    if (!isset($grouped[$groupKey])) {
        $grouped[$groupKey] = [
            'label' => $groupLabel,
            'icon'  => $icon,
            'items' => [],
        ];
    }

    $grouped[$groupKey]['items'][] = [
        'id'        => $row['id'],
        'full_name' => $row['full_name'],
        'variant'   => $row['variant'],
        'price'     => $row['price'],
        'quantity'  => $row['quantity'],
        'category'  => $row['category'],
    ];
}

echo json_encode([
    'query'   => $query,
    'count'   => count($rows),
    'grouped' => array_values($grouped),
]);
