<?php
// Prevent HTML warnings from breaking JSON
error_reporting(0);
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/env.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit;
}

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', 
    getenv('DB_HOST') ?: '127.0.0.1', 
    getenv('DB_PORT') ?: '3306', 
    getenv('DB_NAME') ?: 'restaurant');

try {
    $pdo = new PDO($dsn, getenv('DB_USER') ?: 'root', getenv('DB_PASS') ?: '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $activities = [];

    // 1. Recent Orders (uses existing columns)
    $stmt = $pdo->query("
        SELECT o.id, o.status, o.total, o.created_at, u.name as user_name 
        FROM orders o JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC LIMIT 5
    ");
    foreach ($stmt->fetchAll() as $r) {
        $activities[] = [
            'id' => "order_{$r['id']}", 
            'type' => 'order', 'icon' => '📦',
            'title' => 'New Order', 
            'description' => "Order #{$r['id']} for $" . number_format($r['total'], 2),
            'user' => $r['user_name'], 
            'timestamp' => $r['created_at'],
            'link' => '/admin/orders.php', 
            'status' => $r['status']
        ];
    }

    // 2. Recent Reservations
    $stmt = $pdo->query("
        SELECT r.id, r.status, r.date, r.time, r.guests, u.name as user_name 
        FROM reservations r JOIN users u ON r.user_id = u.id 
        ORDER BY r.created_at DESC LIMIT 5
    ");
    foreach ($stmt->fetchAll() as $r) {
        $dt = new DateTime($r['date'] . ' ' . $r['time']);
        $activities[] = [
            'id' => "res_{$r['id']}", 
            'type' => 'reservation', 'icon' => '📅',
            'title' => 'New Reservation', 
            'description' => "Reservation #{$r['id']} for {$r['guests']} guests on {$dt->format('M j, g:i A')}",
            'user' => $r['user_name'], 
            'timestamp' => $r['created_at'],
            'link' => '/admin/reservations.php', 
            'status' => $r['status']
        ];
    }

    // 3. New Users
    $stmt = $pdo->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT 3");
    foreach ($stmt->fetchAll() as $r) {
        $activities[] = [
            'id' => "user_{$r['id']}", 
            'type' => 'user', 'icon' => '👤',
            'title' => 'New User', 
            'description' => "{$r['name']} ({$r['email']}) joined",
            'user' => $r['name'], 
            'timestamp' => $r['created_at'],
            'link' => '#', 
            'status' => null
        ];
    }

    // Sort by newest first & limit to 10
    usort($activities, fn($a, $b) => strtotime($b['timestamp']) - strtotime($a['timestamp']));
    echo json_encode(['success' => true, 'data' => array_slice($activities, 0, 10)]);
    
} catch (Exception $e) {
    error_log("Activity API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load activity']);
}
