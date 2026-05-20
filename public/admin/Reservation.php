<?php
// ============================================================================
// Reservations API - public/admin/Reservation.php
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
    // LIST reservations with optional filters
    if ($action === 'list') {
        $status = $_GET['status'] ?? null;
        $date = $_GET['date'] ?? null;
        
        $sql = 'SELECT r.*, u.name as customer_name, u.email as customer_email
                FROM reservations r
                JOIN users u ON r.user_id = u.id';
        $params = [];
        
        if ($status) {
            $sql .= ' WHERE r.status = ?';
            $params[] = $status;
        }
        if ($date) {
            $sql .= $status ? ' AND' : ' WHERE';
            $sql .= ' r.date = ?';
            $params[] = $date;
        }
        $sql .= ' ORDER BY r.date DESC, r.time ASC';
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        exit;
    }

    // GET reservation detail
    if ($action === 'detail') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) throw new InvalidArgumentException('Invalid reservation ID');
        
        $stmt = $pdo->prepare('SELECT r.*, u.name as customer_name, u.email as customer_email
                               FROM reservations r
                               JOIN users u ON r.user_id = u.id
                               WHERE r.id = ?');
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        if (!$res) throw new Exception('Reservation not found');
        
        echo json_encode(['success' => true, 'data' => $res]);
        exit;
    }

    // UPDATE status
    if ($action === 'update_status') {
        $id = (int)($_POST['id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? '');
        $validStatuses = ['pending','confirmed','cancelled'];
        
        if ($id <= 0 || !in_array($newStatus, $validStatuses)) {
            throw new InvalidArgumentException('Invalid reservation ID or status');
        }
        
        $stmt = $pdo->prepare('UPDATE reservations SET status = ? WHERE id = ?');
        $stmt->execute([$newStatus, $id]);
        
        if ($stmt->rowCount() === 0) throw new Exception('Reservation not found or unchanged');
        
        echo json_encode(['success' => true, 'message' => 'Status updated', 'status' => $newStatus]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);

} catch (Exception $e) {
    http_response_code($e instanceof InvalidArgumentException ? 400 : 500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
