<?php
header('Content-Type: application/json');

// Load environment variables
require_once __DIR__ . '/../../config/env.php';

// Build DSN from env vars (force TCP)
$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    getenv('DB_HOST'),
    getenv('DB_PORT') ?: '3306',
    getenv('DB_NAME')
);

try {
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    error_log('DB Connection failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database unavailable']);
    exit;
}

require_once __DIR__ . '/../../src/models/MenuItem.php';
$model = new MenuItem($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $items = $model->getAll();
    $categories = [];
    foreach ($items as $item) {
        $cat = $item['category'];
        if (!isset($categories[$cat])) $categories[$cat] = ['name' => $cat, 'items' => []];
        $categories[$cat]['items'][] = $item;
    }
    echo json_encode(['success' => true, 'data' => array_values($categories)]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
