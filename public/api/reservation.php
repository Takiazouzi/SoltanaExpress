<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/env.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to make a reservation']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$date = trim($data['date'] ?? '');
$time = trim($data['time'] ?? '');
$guests = (int)($data['guests'] ?? 1);
$notes = trim($data['notes'] ?? '');

// Validation
if (!$date || !$time || $guests < 1 || $guests > 20) {
    echo json_encode(['success' => false, 'message' => 'Invalid date, time, or guest count (1-20)']); exit;
}
if (!strtotime($date) || !strtotime($time)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date or time format']); exit;
}
if (strtotime($date) < strtotime(date('Y-m-d'))) {
    echo json_encode(['success' => false, 'message' => 'Reservations must be for future dates']); exit;
}

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', 
    getenv('DB_HOST') ?: '127.0.0.1', getenv('DB_PORT') ?: '3306', getenv('DB_NAME') ?: 'restaurant');
try {
    $pdo = new PDO($dsn, getenv('DB_USER') ?: 'root', getenv('DB_PASS') ?: '', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(['success'=>false,'message'=>'Database unavailable']); exit;
}

// Check for double-booking (exact date & time)
$stmt = $pdo->prepare("SELECT id FROM reservations WHERE date = ? AND time = ? AND status != 'cancelled'");
$stmt->execute([$date, $time]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'This time slot is already booked. Please choose another.']); exit;
}

// Insert reservation
$stmt = $pdo->prepare("INSERT INTO reservations (user_id, date, time, guests, special_requests, status) VALUES (?, ?, ?, ?, ?, 'pending')");
$stmt->execute([$_SESSION['user_id'], $date, $time, $guests, $notes]);

echo json_encode(['success' => true, 'message' => 'Reservation confirmed!', 'id' => (int)$pdo->lastInsertId()]);
