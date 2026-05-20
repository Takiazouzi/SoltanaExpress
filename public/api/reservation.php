<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/env.php';

if (session_status() === PHP_SESSION_NONE) @session_start();
if (empty($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Please log in']); exit; }

$userId = (int)$_SESSION['user_id'];
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', getenv('DB_HOST'), getenv('DB_PORT')?:'3306', getenv('DB_NAME'));
$pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

require_once __DIR__.'/../../src/models/Reservation.php';
$model = new Reservation($pdo);

if ($_SERVER['REQUEST_METHOD']==='GET' && ($_GET['action']??'')==='available_slots') {
    $date = $_GET['date']??'';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date)) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid date']); exit; }
    echo json_encode(['success'=>true,'slots'=>$model->getAvailableSlots($date)]); exit;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $raw=file_get_contents('php://input'); $data=json_decode($raw,true); $action=$data['action']??'';
    if ($action==='create') { echo json_encode($model->create($userId,$data['date']??'',$data['time']??'',(int)$data['guests']??0,trim($data['notes']??''))); exit; }
    if ($action==='cancel') { 
        $stmt=$pdo->prepare('UPDATE reservations SET status="cancelled" WHERE id=? AND user_id=? AND status!="cancelled"'); 
        $stmt->execute([(int)$data['id']??0,$userId]); 
        echo json_encode(['success'=>$stmt->rowCount()>0,'message'=>$stmt->rowCount()>0?'Cancelled':'Not found']); exit; 
    }
}
http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid request']);
