<?php declare(strict_types=1);
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Hardcoded DB config
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=restaurant;charset=utf8mb4";
$dbUser = 'restaurant_app';
$dbPass = 'app_secret_123';

if (session_status() === PHP_SESSION_NONE) @session_start();

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
$action = $data['action'] ?? '';

echo json_encode(['debug' => 'received', 'action' => $action, 'raw' => substr($rawInput, 0, 100)]) . "\n";
flush();

if ($action === 'register') {
    try {
        $pdo = new \PDO($dsn, $dbUser, $dbPass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
        
        $email = trim($data['email'] ?? '');
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email exists']) . "\n";
            exit;
        }
        
        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 4]); // cost 4 for speed in dev
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$data['name'], $email, $hash]);
        
        echo json_encode(['success' => true, 'user_id' => (int)$pdo->lastInsertId()]) . "\n";
        
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage(), 'line' => $e->getLine()]) . "\n";
    }
}
exit;
