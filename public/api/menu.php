<?php
// ============================================================================
// Menu API Endpoint
// ============================================================================
header('Content-Type: application/json');

require_once __DIR__ . '/../../src/models/MenuItem.php';

// DB Connection
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=restaurant;charset=utf8mb4";
$pdo = new PDO($dsn, 'restaurant_app', 'app_secret_123', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

$model = new MenuItem($pdo);

// GET: Return all menu items grouped by category
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $items = $model->getAll();
    
    // Group by category
    $categories = [];
    foreach ($items as $item) {
        $cat = $item['category'];
        if (!isset($categories[$cat])) {
            $categories[$cat] = ['name' => $cat, 'items' => []];
        }
        $categories[$cat]['items'][] = $item;
    }
    
    echo json_encode([
        'success' => true,
        'data' => array_values($categories)
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
