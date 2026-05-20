<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/env.php';

if (session_status() === PHP_SESSION_NONE) @session_start();
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to order']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', getenv('DB_HOST'), getenv('DB_PORT')?:'3306', getenv('DB_NAME'));
$pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (($data['action'] ?? '') !== 'place_order') { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid action']); exit; }
    
    $cartItems = $data['items'] ?? [];
    if (empty($cartItems)) { echo json_encode(['success'=>false,'message'=>'Cart is empty']); exit; }
    
    $ids = array_map('intval', array_column($cartItems, 'id'));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, price FROM menu_items WHERE id IN ($placeholders) AND available = 1");
    $stmt->execute($ids);
    $validItems = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $total = 0;
    foreach ($cartItems as $ci) {
        $id = (int)$ci['id']; $qty = (int)$ci['qty'];
        if (!isset($validItems[$id]) || $qty < 1) { echo json_encode(['success'=>false,'message'=>'Invalid or unavailable item']); exit; }
        $total += $validItems[$id] * $qty;
    }
    
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, status, total, notes) VALUES (?, 'pending', ?, ?)");
        $stmt->execute([$userId, $total, $data['notes'] ?? '']);
        $orderId = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        foreach ($cartItems as $ci) $stmt->execute([$orderId, (int)$ci['id'], (int)$ci['qty'], $validItems[(int)$ci['id']]]);
        
        $pdo->commit();
        echo json_encode(['success'=>true, 'order_id'=>(int)$orderId, 'total'=>$total]);
    } catch (\Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success'=>false,'message'=>'Failed to place order']);
    }
    exit;
}
http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']);
