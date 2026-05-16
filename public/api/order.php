<?php
// ============================================================================
// Order API Endpoint
// ============================================================================
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) @session_start();
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to order']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=restaurant;charset=utf8mb4";
$pdo = new PDO($dsn, 'restaurant_app', 'app_secret_123', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    
    if (($data['action'] ?? '') !== 'place_order') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }

    $cartItems = $data['items'] ?? [];
    $notes = $data['notes'] ?? '';

    if (empty($cartItems)) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        exit;
    }

    // Extract IDs
    $ids = array_map('intval', array_column($cartItems, 'id'));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // Validate items and get prices server-side
    $stmt = $pdo->prepare("SELECT id, price FROM menu_items WHERE id IN ($placeholders) AND available = 1");
    $stmt->execute($ids);
    $validItems = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Calculate total
    $total = 0;
    foreach ($cartItems as $ci) {
        $id = (int)$ci['id'];
        $qty = (int)$ci['qty'];
        if (!isset($validItems[$id]) || $qty < 1) {
            echo json_encode(['success' => false, 'message' => 'Invalid item or unavailable']);
            exit;
        }
        $total += $validItems[$id] * $qty;
    }

    // Insert Order
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, status, total, notes) VALUES (?, 'pending', ?, ?)");
        $stmt->execute([$userId, $total, $notes]);
        $orderId = $pdo->lastInsertId();

        // Insert Order Items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        foreach ($cartItems as $ci) {
            $id = (int)$ci['id'];
            $qty = (int)$ci['qty'];
            $stmt->execute([$orderId, $id, $qty, $validItems[$id]]);
        }
        
        $pdo->commit();
        echo json_encode(['success' => true, 'order_id' => $orderId, 'total' => $total]);
        exit;

    } catch (\Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to place order']);
        exit;
    }
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
