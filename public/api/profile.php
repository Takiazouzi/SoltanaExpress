<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/env.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) {
    http_response_code(401); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit;
}

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', 
    getenv('DB_HOST') ?: '127.0.0.1', getenv('DB_PORT') ?: '3306', getenv('DB_NAME') ?: 'restaurant');
$pdo = new PDO($dsn, getenv('DB_USER') ?: 'root', getenv('DB_PASS') ?: '', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

$uid = $_SESSION['user_id'];
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$action = $_GET['action'] ?? 'stats';

try {
    if ($action === 'stats') {
        if ($isAdmin) {
            $o = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
            $u = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
            $r = $pdo->query('SELECT COALESCE(SUM(total),0) FROM orders WHERE status="delivered"')->fetchColumn();
            echo json_encode(['success'=>true, 'data'=>['orders'=>$o, 'users'=>$u, 'revenue'=>number_format($r,0)]]);
        } else {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE user_id=?'); $stmt->execute([$uid]); $oc = $stmt->fetchColumn();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id=? AND status!='cancelled'"); $stmt->execute([$uid]); $rc = $stmt->fetchColumn();
            echo json_encode(['success'=>true, 'data'=>['orders'=>$oc, 'reservations'=>$rc]]);
        }
    } elseif ($action === 'orders') {
        $stmt = $pdo->prepare("SELECT o.id, o.status, o.total, o.notes, o.created_at, COUNT(oi.id) as item_count 
                               FROM orders o 
                               LEFT JOIN order_items oi ON o.id = oi.order_id 
                               WHERE o.user_id = ? 
                               GROUP BY o.id 
                               ORDER BY o.created_at DESC");
        $stmt->execute([$uid]);
        echo json_encode(['success'=>true, 'data'=>$stmt->fetchAll()]);
    } elseif ($action === 'reservations') {
        $stmt = $pdo->prepare("SELECT id, date, time, guests, status, special_requests, created_at 
                               FROM reservations 
                               WHERE user_id = ? 
                               ORDER BY date DESC, time DESC");
        $stmt->execute([$uid]);
        echo json_encode(['success'=>true, 'data'=>$stmt->fetchAll()]);
    } else {
        http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500); echo json_encode(['success'=>false,'message'=>'Server error']);
}
