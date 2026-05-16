<?php declare(strict_types=1);
// ============================================================================
// Auth Controller - Minimal (Hardcoded DB, No .env)
// ============================================================================

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Hardcoded DB config (matches test-raw.php)
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=restaurant;charset=utf8mb4";
$dbUser = 'restaurant_app';
$dbPass = 'app_secret_123';

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Read and parse JSON input
$rawInput = file_get_contents('php://input');
if (empty($rawInput)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Empty request body']);
    exit;
}

$data = json_decode($rawInput, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON: ' . json_last_error_msg()]);
    exit;
}

$action = $data['action'] ?? '';

// Create PDO connection directly
try {
    $pdo = new \PDO($dsn, $dbUser, $dbPass, [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connection failed: ' . $e->getMessage()]);
    exit;
}

// Load User model
require_once __DIR__ . '/../models/User.php';
$userModel = new User($pdo);

// Route by action
try {
    switch ($action) {
        case 'register':
            $result = $userModel->register(
                trim($data['name'] ?? ''),
                trim($data['email'] ?? ''),
                $data['password'] ?? '',
                $data['confirm_password'] ?? ''
            );
            break;
            
        case 'login':
            $result = $userModel->login(
                trim($data['email'] ?? ''),
                $data['password'] ?? ''
            );
            break;
            
        case 'logout':
            User::logout();
            $result = ['success' => true, 'message' => 'Logged out successfully.'];
            break;
            
        default:
            http_response_code(400);
            $result = ['success' => false, 'message' => 'Invalid or missing action.'];
    }
    echo json_encode($result);
    
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}
exit;
