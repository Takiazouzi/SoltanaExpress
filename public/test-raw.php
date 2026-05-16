<?php declare(strict_types=1);
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Hardcoded credentials - NO .env, NO classes
$host = '127.0.0.1';
$port = '3306';
$dbname = 'restaurant';
$user = 'restaurant_app';
$pass = 'app_secret_123';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

echo json_encode(['step' => 'attempting', 'dsn' => $dsn, 'user' => $user]) . "\n";
flush();

try {
    $pdo = new \PDO($dsn, $user, $pass, [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]);
    echo json_encode(['step' => 'connected', 'version' => $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION)]) . "\n";
    
    // Test a real query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch()['count'];
    echo json_encode(['step' => 'queried', 'users_count' => $count]) . "\n";
    
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'step' => 'failed',
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]) . "\n";
}
exit;
