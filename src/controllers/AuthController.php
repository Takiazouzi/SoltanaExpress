<?php
// NO strict_types, NO buffering issues
error_reporting(0);
@ob_end_clean();
@ob_start();
header('Content-Type: application/json');

// Hardcoded DB config
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=restaurant;charset=utf8mb4";
$dbUser = 'restaurant_app';
$dbPass = 'app_secret_123';

@session_start(); // Suppress warnings

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$action = $data['action'] ?? '';

// Direct PDO connection
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>'DB:'.$e->getMessage()]);
    exit;
}

// Inline User logic (no separate file to avoid include issues)
if ($action === 'register') {
    $name = trim($data['name']??'');
    $email = trim($data['email']??'');
    $pass = $data['password']??'';
    $confirm = $data['confirm_password']??'';
    
    if (!$name || !$email || !$pass) {
        echo json_encode(['success'=>false,'message'=>'Missing fields']); exit;
    }
    if ($pass !== $confirm) {
        echo json_encode(['success'=>false,'message'=>'Passwords mismatch']); exit;
    }
    
    // Check duplicate
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email=?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success'=>false,'message'=>'Email exists']); exit;
    }
    
    // Insert
    $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost'=>4]);
    $stmt = $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
    $stmt->execute([$name,$email,$hash]);
    
    echo json_encode(['success'=>true,'message'=>'Registered','user_id'=>(int)$pdo->lastInsertId()]);
    exit;
}

if ($action === 'login') {
    $email = trim($data['email']??'');
    $pass = $data['password']??'';
    
    $stmt = $pdo->prepare('SELECT id,name,role,password FROM users WHERE email=?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($pass, $user['password'])) {
        echo json_encode(['success'=>false,'message'=>'Invalid credentials']); exit;
    }
    
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    
    echo json_encode(['success'=>true,'message'=>'Logged in','redirect'=>'/profile.php']);
    exit;
}

if ($action === 'logout') {
    $_SESSION = [];
    @session_destroy();
    echo json_encode(['success'=>true,'message'=>'Logged out']);
    exit;
}

// Fallback
http_response_code(400);
echo json_encode(['success'=>false,'message'=>'Invalid action']);
exit;
