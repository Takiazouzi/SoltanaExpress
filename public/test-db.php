<?php declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
try {
    $db = Database::getInstance()->connection();
    echo json_encode(['connected' => true, 'server' => $db->getAttribute(\PDO::ATTR_SERVER_VERSION)]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['connected' => false, 'error' => $e->getMessage()]);
}
