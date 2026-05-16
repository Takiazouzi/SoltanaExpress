<?php
header('Content-Type: application/json');
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$action = $data['action'] ?? '';

// Hardcoded DB config (working credentials)
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=restaurant;charset=utf8mb4";
$pdo = new PDO($dsn, 'restaurant_app', 'app_secret_123', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

if ($action === 'register') {
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $pass = $data['password'] ?? '';
    $confirm = $data['confirm_password'] ?? '';
    
    if (!$name || !$email || !$pass || $pass !== $confirm || strlen($pass) < 6) {
        echo '{"success":false,"message":"Validation failed"}'; exit;
    }
    
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email=?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) { echo '{"success":false,"message":"Email exists"}'; exit; }
    
    $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost'=>4]);
    $stmt = $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
    $stmt->execute([$name, $email, $hash]);
    
    echo json_encode(['success'=>true,'message'=>'Registered','user_id'=>(int)$pdo->lastInsertId()]);
    exit;
}

if ($action === 'login') {
    @session_start();
    $email = trim($data['email'] ?? '');
    $pass = $data['password'] ?? '';
    
    $stmt = $pdo->prepare('SELECT id,name,role,password FROM users WHERE email=?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($pass, $user['password'])) {
        echo '{"success":false,"message":"Invalid credentials"}'; exit;
    }
    
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    
    echo '{"success":true,"message":"Logged in","redirect":"/profile.php"}';
    exit;
}

if ($action === 'logout') {
    @session_start();
    $_SESSION = []; @session_destroy();
    echo '{"success":true,"message":"Logged out"}';
    exit;
}

http_response_code(400);
echo '{"success":false,"message":"Invalid action"}';
exit;
