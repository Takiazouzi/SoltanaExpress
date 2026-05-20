<?php
// ============================================================================
// API Router (Exits before HTML if action is present)
// ============================================================================
require_once __DIR__ . '/../../config/env.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Content-Type: application/json'); http_response_code(403);
    echo json_encode(['success'=>false, 'message'=>'Admin access required']); exit;
}
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', getenv('DB_HOST')?:'127.0.0.1', getenv('DB_PORT')?:'3306', getenv('DB_NAME')?:'restaurant');
try { $pdo = new PDO($dsn, getenv('DB_USER')?:'root', getenv('DB_PASS')?:'', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]); }
catch (PDOException $e) { header('Content-Type: application/json'); http_response_code(500); echo json_encode(['success'=>false,'message'=>'DB failed']); exit; }

$action = $_REQUEST['action'] ?? '';
if (!empty($action)) {
    header('Content-Type: application/json');
    try {
        if ($action === 'list') { echo json_encode(['success'=>true, 'data'=>$pdo->query('SELECT * FROM menu_items ORDER BY category, name')->fetchAll()]); exit; }
        if ($action === 'create') {
            $pdo->prepare('INSERT INTO menu_items (name,description,price,category,available,image_path) VALUES (?,?,?,?,?,?)')
                ->execute([trim($_POST['name']??''), trim($_POST['description']??''), (float)($_POST['price']??0), trim($_POST['category']??''), !empty($_POST['available'])?1:0, $_POST['image_path']??null]);
            echo json_encode(['success'=>true, 'id'=>(int)$pdo->lastInsertId(), 'message'=>'Created']); exit;
        }
        if ($action === 'update') {
            $pdo->prepare('UPDATE menu_items SET name=?,description=?,price=?,category=?,available=?,image_path=? WHERE id=?')
                ->execute([trim($_POST['name']??''), trim($_POST['description']??''), (float)($_POST['price']??0), trim($_POST['category']??''), !empty($_POST['available'])?1:0, $_POST['image_path']??null, (int)($_POST['id']??0)]);
            echo json_encode(['success'=>true, 'message'=>'Updated']); exit;
        }
        if ($action === 'delete') { $pdo->prepare('UPDATE menu_items SET available=0 WHERE id=?')->execute([(int)($_REQUEST['id']??0)]); echo json_encode(['success'=>true]); exit; }
        if ($action === 'upload_image') {
            $id = (int)($_POST['id']??0);
            if (empty($_FILES['image']['name'])) throw new Exception('No file');
            $finfo = finfo_open(FILEINFO_MIME_TYPE); $mime = finfo_file($finfo, $_FILES['image']['tmp_name']); finfo_close($finfo);
            if (!in_array($mime, ['image/jpeg','image/png','image/webp'])) throw new Exception('Invalid type');
            if ($_FILES['image']['size'] > 2097152) throw new Exception('Max 2MB');
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION); $fname = 'item_' . uniqid() . '.' . $ext;
            $dest = __DIR__ . '/../../uploads/' . $fname;
            if (!is_dir(__DIR__ . '/../../uploads')) mkdir(__DIR__ . '/../../uploads', 0755, true);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $pdo->prepare('UPDATE menu_items SET image_path=? WHERE id=?')->execute(['/uploads/'.$fname, $id]);
                echo json_encode(['success'=>true, 'url'=>'/uploads/'.$fname]);
            } else throw new Exception('Upload failed');
            exit;
        }
        http_response_code(400); echo json_encode(['success'=>false, 'message'=>'Invalid action']);
    } catch (Exception $e) { http_response_code(400); echo json_encode(['success'=>false, 'message'=>$e->getMessage()]); }
    exit;
}

// ============================================================================
// UI Render
// ============================================================================
$pageTitle = 'Menu Items | Savoria Admin';
$breadcrumb = 'Menu Items';
$pageScripts = '<script src="/admin/js/menu.js"></script>';
require __DIR__ . '/views/header.php';
?>

<div class="page-header">
  <h1>Menu Items</h1>
  <div class="controls">
    <input type="text" id="search-input" class="input" placeholder="Search name...">
    <select id="filter-cat" class="select">
      <option value="">All Categories</option><option>Starters</option><option>Mains</option><option>Desserts</option>
    </select>
    <button class="btn btn-primary" id="btn-add"><i class="ti ti-plus"></i> Add Item</button>
  </div>
</div>

<div class="table-wrapper">
  <table class="data-table">
    <thead><tr>
      <th style="width:50px">ID</th><th style="width:70px">Image</th><th>Name</th><th>Category</th>
      <th style="width:80px;text-align:right">Price</th><th style="width:100px">Status</th>
      <th style="width:100px" class="actions">Actions</th>
    </tr></thead>
    <tbody id="table-body"><tr><td colspan="7" style="text-align:center;padding:24px;color:var(--text-muted)">Loading...</td></tr></tbody>
  </table>
</div>

<?php require __DIR__ . '/views/footer.php'; ?>
