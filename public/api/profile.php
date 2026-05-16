<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) @session_start();
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated', 'redirect' => '/login.php']);
    exit;
}
$userId = (int)$_SESSION['user_id'];
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=restaurant;charset=utf8mb4";
$pdo = new PDO($dsn, 'restaurant_app', 'app_secret_123', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// GET: Return user, orders, reservations
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT id, name, email, created_at FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user) { http_response_code(404); echo json_encode(['success' => false, 'message' => 'User not found']); exit; }

    $stmt = $pdo->prepare('SELECT o.id, o.status, o.total, o.created_at, COUNT(oi.id) as items_count FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? GROUP BY o.id ORDER BY o.created_at DESC');
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();

    $stmt = $pdo->prepare('SELECT id, date, time, guests, status, special_requests FROM reservations WHERE user_id = ? ORDER BY date DESC');
    $stmt->execute([$userId]);
    $rawRes = $stmt->fetchAll();

    $reservations = array_map(function($r) {
        $dt = new DateTime($r['date'] . ' ' . $r['time']);
        $now = new DateTime();
        return [
            'id' => $r['id'], 'date' => $r['date'], 'time' => $r['time'],
            'formatted_date' => $dt->format('l, F j'), 'formatted_time' => $dt->format('g:i A'),
            'guests' => (int)$r['guests'], 'status' => $r['status'], 'special_requests' => $r['special_requests'],
            'is_upcoming' => $dt > $now && $r['status'] !== 'cancelled',
            'can_cancel' => $dt > $now && $r['status'] === 'pending'
        ];
    }, $rawRes);

    echo json_encode([
        'success' => true,
        'user' => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'member_since' => date('F Y', strtotime($user['created_at']))],
        'orders' => $orders,
        'reservations' => $reservations
    ]);
    exit;
}

// POST: Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $action = $data['action'] ?? '';

    if ($action === 'update_name') {
        $name = trim($data['name'] ?? '');
        if (empty($name) || strlen($name) < 2) { echo json_encode(['success' => false, 'message' => 'Name must be at least 2 characters']); exit; }
        $stmt = $pdo->prepare('UPDATE users SET name = ? WHERE id = ?');
        $stmt->execute([$name, $userId]);
        if ($stmt->rowCount() > 0) $_SESSION['name'] = $name;
        echo json_encode(['success' => true, 'message' => 'Name updated', 'user' => ['name' => $name]]);
        exit;
    }

    if ($action === 'cancel_reservation') {
        $resId = (int)($data['reservation_id'] ?? 0);
        if ($resId <= 0) { echo json_encode(['success' => false, 'message' => 'Invalid reservation ID']); exit; }
        $stmt = $pdo->prepare('UPDATE reservations SET status = "cancelled" WHERE id = ? AND user_id = ? AND status != "cancelled"');
        $stmt->execute([$resId, $userId]);
        echo json_encode(['success' => $stmt->rowCount() > 0, 'message' => $stmt->rowCount() > 0 ? 'Reservation cancelled' : 'Could not cancel']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
