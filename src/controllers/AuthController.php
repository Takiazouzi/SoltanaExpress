<?php
header('Content-Type: application/json');
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (($data['action']??'') !== 'register') { http_response_code(400); echo '{"error":"wrong action"}'; exit; }

$dsn = "mysql:host=127.0.0.1;port=3306;dbname=restaurant;charset=utf8mb4";
$pdo = new PDO($dsn, 'restaurant_app', 'app_secret_123', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

// Check duplicate
$stmt = $pdo->prepare('SELECT id FROM users WHERE email=?');
$stmt->execute([$data['email']]);
if ($stmt->fetch()) { echo '{"success":false,"message":"exists"}'; exit; }

// Insert
$hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost'=>4]);
$stmt = $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
$stmt->execute([$data['name'], $data['email'], $hash]);

echo json_encode(['success'=>true,'id'=>(int)$pdo->lastInsertId()]);
exit;
