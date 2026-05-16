<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) @session_start();
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'message'=>'Not authenticated','redirect'=>'/login.php']);
    exit;
}
$userId = (int)$_SESSION['user_id'];
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=restaurant;charset=utf8mb4";
$pdo = new PDO($dsn, 'restaurant_app', 'app_secret_123', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT id,name,email,created_at FROM users WHERE id=?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'User not found']); exit; }
    
    $orders = [];
    $stmt = $pdo->prepare('SELECT o.id,o.status,o.total,o.created_at,COUNT(oi.id) as items_count FROM orders o LEFT JOIN order_items oi ON o.id=oi.order_id WHERE o.user_id=? GROUP BY o.id ORDER BY o.created_at DESC LIMIT 10');
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();
    
    $reservations = [];
    $stmt = $pdo->prepare('SELECT id,date,time,guests,status,special_requests FROM reservations WHERE user_id=? ORDER BY date DESC LIMIT 10');
    $stmt->execute([$userId]);
    $reservations = $stmt->fetchAll();
    
    $formattedReservations = array_map(function($r) {
        $dt = new DateTime($r['date'].' '.$r['time']);
        $now = new DateTime();
        return [
            'id'=>$r['id'], 'date'=>$r['date'], 'time'=>$r['time'],
            'formatted_date'=>$dt->format('l, F j'), 'formatted_time'=>$dt->format('g:i A'),
            'guests'=>$r['guests'], 'status'=>$r['status'], 'special_requests'=>$r['special_requests'],
            'is_upcoming'=>($dt > $now && $r['status']!='cancelled'),
            'can_cancel'=>($dt > $now && $r['status']=='pending')
        ];
    }, $reservations);
    
    echo json_encode([
        'success'=>true,
        'user'=>['id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email'],'member_since'=>date('F Y',strtotime($user['created_at']))],
        'orders'=>$orders,
        'reservations'=>$formattedReservations
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $action = $data['action'] ?? '';
    
    if ($action === 'update_account') {
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        
        if (empty($name) || strlen($name) < 2) { echo json_encode(['success'=>false,'message'=>'Name must be at least 2 characters']); exit; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { echo json_encode(['success'=>false,'message'=>'Invalid email format']); exit; }
        
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email=? AND id!=?');
        $stmt->execute([$email, $userId]);
        if ($stmt->fetch()) { echo json_encode(['success'=>false,'message'=>'Email already in use']); exit; }
        
        if (!empty($password)) {
            if (strlen($password) < 6) { echo json_encode(['success'=>false,'message'=>'Password must be at least 6 characters']); exit; }
            if ($password !== ($data['password_confirm'] ?? '')) { echo json_encode(['success'=>false,'message'=>'Passwords do not match']); exit; }
        }
        
        try {
            $stmt = $pdo->prepare('UPDATE users SET name=?, email=? WHERE id=?');
            $stmt->execute([$name, $email, $userId]);
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]);
                $stmt = $pdo->prepare('UPDATE users SET password=? WHERE id=?');
                $stmt->execute([$hash, $userId]);
            }
            $_SESSION['name'] = $name;
            echo json_encode(['success'=>true,'message'=>'Account updated','user'=>['name'=>$name,'email'=>$email]]);
            exit;
        } catch (PDOException $e) { echo json_encode(['success'=>false,'message'=>'Failed to update account']); exit; }
    }
    
    if ($action === 'update_preferences') {
        $newsletter = !empty($data['newsletter']);
        $sms = !empty($data['sms']);
        echo json_encode(['success'=>true,'message'=>'Preferences saved','preferences'=>['newsletter'=>$newsletter,'sms'=>$sms]]);
        exit;
    }
    
    if ($action === 'delete_account') {
        $confirm = $data['confirm'] ?? '';
        if ($confirm !== 'DELETE') { echo json_encode(['success'=>false,'message'=>'Confirmation required']); exit; }
        try {
            $pdo->prepare('DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE user_id=?)')->execute([$userId]);
            $pdo->prepare('DELETE FROM orders WHERE user_id=?')->execute([$userId]);
            $pdo->prepare('DELETE FROM reservations WHERE user_id=?')->execute([$userId]);
            $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$userId]);
            $_SESSION = []; @session_destroy();
            echo json_encode(['success'=>true,'message'=>'Account deleted','redirect'=>'/login.php']);
            exit;
        } catch (PDOException $e) { echo json_encode(['success'=>false,'message'=>'Failed to delete account']); exit; }
    }
    
    if ($action === 'cancel_reservation') {
        $resId = (int)($data['reservation_id'] ?? 0);
        if ($resId > 0) {
            $stmt = $pdo->prepare('UPDATE reservations SET status="cancelled" WHERE id=? AND user_id=? AND status!="cancelled"');
            $stmt->execute([$resId, $userId]);
            echo json_encode(['success'=>$stmt->rowCount()>0,'message'=>$stmt->rowCount()>0?'Cancelled':'Not found']);
            exit;
        }
    }
    
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit;
}

http_response_code(405);
echo json_encode(['success'=>false,'message'=>'Method not allowed']);
exit;
