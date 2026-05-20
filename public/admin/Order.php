<?php
// ============================================================================
// Orders API - public/admin/Order.php
// Handles: list, detail, update_status
// ============================================================================
require_once __DIR__ . '/../../config/env.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit;
}

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', 
    getenv('DB_HOST') ?: '127.0.0.1', 
    getenv('DB_PORT') ?: '3306', 
    getenv('DB_NAME') ?: 'restaurant');
$pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

header('Content-Type: application/json');
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    // LIST orders with optional status filter
    if ($action === 'list') {
        $status = $_GET['status'] ?? null;
        $sql = 'SELECT o.id, o.status, o.total, o.created_at, o.notes,
                       u.name as customer_name, u.email as customer_email,
                       COUNT(oi.id) as items_count
                FROM orders o
                JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id';
        if ($status) {
            $sql .= ' WHERE o.status = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$status]);
        } else {
            $stmt = $pdo->query($sql . ' GROUP BY o.id ORDER BY o.created_at DESC');
        }
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        exit;
    }

    // GET order detail with items
    if ($action === 'detail') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) throw new InvalidArgumentException('Invalid order ID');
        
        // Order info
        $stmt = $pdo->prepare('SELECT o.*, u.name as customer_name, u.email as customer_email 
                               FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?');
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if (!$order) throw new Exception('Order not found');
        
        // Order items
        $stmt = $pdo->prepare('SELECT oi.quantity, oi.unit_price, m.name as item_name, m.description
                               FROM order_items oi
                               JOIN menu_items m ON oi.menu_item_id = m.id
                               WHERE oi.order_id = ?');
        $stmt->execute([$id]);
        $order['items'] = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'data' => $order]);
        exit;
    }

    // UPDATE status
    if ($action === 'update_status') {
        $id = (int)($_POST['id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? '');
        $validStatuses = ['pending','confirmed','preparing','ready','delivered','cancelled'];
        
        if ($id <= 0 || !in_array($newStatus, $validStatuses)) {
            throw new InvalidArgumentException('Invalid order ID or status');
        }
        
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$newStatus, $id]);
        
        if ($stmt->rowCount() === 0) throw new Exception('Order not found or unchanged');
        
        echo json_encode(['success' => true, 'message' => 'Status updated', 'status' => $newStatus]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);

} catch (Exception $e) {
    http_response_code($e instanceof InvalidArgumentException ? 400 : 500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
