<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || !empty($_REQUEST['action'])) {
        header('Content-Type: application/json'); http_response_code(403);
        echo json_encode(['success'=>false, 'message'=>'Admin access required']); exit;
    }
    header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI'])); exit;
}
$pageTitle = $pageTitle ?? 'Savoria Admin';
$breadcrumb = $breadcrumb ?? 'Overview';
$userName = $_SESSION['name'] ?? 'Admin User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/admin/css/admin.css">
</head>
<body>
  <div class="admin-layout">
    <aside class="sidebar">
      <a href="index.php" class="sidebar-brand">Savoria Admin</a>
      <nav class="nav-menu">
        <a href="index.php" class="nav-link"><i class="ti ti-dashboard"></i> Dashboard</a>
        <a href="menu-items.php" class="nav-link"><i class="ti ti-menu-2"></i> Menu Items</a>
        <a href="orders.php" class="nav-link"><i class="ti ti-shopping-bag"></i> Orders</a>
        <a href="reservations.php" class="nav-link"><i class="ti ti-calendar-stats"></i> Reservations</a>
        <a href="/" class="nav-link"><i class="ti ti-world"></i> View Site</a>
        <a href="#" class="nav-link logout" id="logout-btn"><i class="ti ti-logout"></i> Log out</a>
      </nav>
    </aside>
    <main class="main">
      <header class="topbar">
        <div class="breadcrumb">Dashboard <span>/</span> <span><?= $breadcrumb ?></span></div>
        <div class="user-info">
          <span><?= htmlspecialchars($userName) ?></span>
          <div class="avatar"><?= strtoupper(substr($userName, 0, 2)) ?></div>
        </div>
      </header>
      <div class="content">
