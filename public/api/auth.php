<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$action = $data['action'] ?? $_GET['action'] ?? '';

// DB Connection via .env
require_once __DIR__ . '/../../config/env.php';
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', 
    getenv('DB_HOST'), getenv('DB_PORT') ?: '3306', getenv('DB_NAME'));

try {
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database unavailable']);
    exit;
}

// ============================================================================
// LOGIN
// ============================================================================
if ($action === 'login') {
    $email = trim($data['email'] ?? '');
    $pass = $data['password'] ?? '';
    
    if (!$email || !$pass) {
        echo json_encode(['success' => false, 'message' => 'Email and password required']);
        exit;
    }
    
    $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($pass, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    
    // Admins go to admin panel, users go to profile
    $redirect = ($user['role'] === 'admin') ? '/admin/index.php' : '/profile.php';
    
    echo json_encode(['success' => true, 'message' => 'Logged in', 'redirect' => $redirect, 'role' => $user['role']]);
    exit;
}

// ============================================================================
// REGISTER
// ============================================================================
if ($action === 'register') {
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $pass = $data['password'] ?? '';
    $confirm = $data['confirm_password'] ?? '';
    
    if (!$name || !$email || !$pass) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    if ($pass !== $confirm) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit;
    }
    if (strlen($pass) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }
    
    // Check duplicate email
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    
    $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    $stmt->execute([$name, $email, $hash]);
    
    echo json_encode(['success' => true, 'message' => 'Account created', 'user_id' => (int)$pdo->lastInsertId()]);
    exit;
}

// ============================================================================
// LOGOUT (Works for both JSON API and direct link)
// ============================================================================
if ($action === 'logout') {
    // Clear all session data
    $_SESSION = [];
    
    // Delete session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'] ?? false, $params['httponly'] ?? true);
    }
    
    // Destroy session
    session_destroy();
    
    // Return JSON for AJAX calls
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strpos($_SERVER['CONTENT_TYPE'] ?? '', 'json') !== false) {
        echo json_encode(['success' => true, 'message' => 'Logged out', 'redirect' => '/login.php']);
    } else {
        // Redirect for direct link clicks
        header('Location: /login.php');
    }
    exit;
}

// ============================================================================
// CHECK SESSION (For frontend to verify login state)
// ============================================================================
if ($action === 'check') {
    echo json_encode([
        'loggedIn' => !empty($_SESSION['user_id']),
        'user' => !empty($_SESSION['user_id']) ? [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['name'] ?? '',
            'role' => $_SESSION['role'] ?? 'user'
        ] : null
    ]);
    exit;
}

// Fallback
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
