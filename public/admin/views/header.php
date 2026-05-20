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
$userEmail = $_SESSION['email'] ?? '';
$initials = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $userName), 0, 2));
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
  <link rel="stylesheet" href="/css/components.css">
  <link rel="stylesheet" href="/css/dropdown.css">
  <link rel="stylesheet" href="/css/dropdown.css">
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
      </nav>
    </aside>
    <main class="main">
      <header class="topbar">
        <div class="breadcrumb">Dashboard <span>/</span> <span><?= $breadcrumb ?></span></div>
        <div class="profile-dropdown-container">
          <button class="profile-trigger" aria-haspopup="true" aria-expanded="false">
            <span class="profile-avatar-sm"><?= $initials ?></span>
            <span><?= htmlspecialchars($userName) ?></span>
            <i class="ti ti-chevron-down"></i>
          </button>
          <div class="profile-dropdown" role="menu">
            <div class="dropdown-header">
              <div class="avatar-lg"><?= $initials ?></div>
              <div class="info">
                <p class="name"><?= htmlspecialchars($userName) ?></p>
                <p class="email"><?= htmlspecialchars($userEmail) ?></p>
                <span class="role-badge">Administrator</span>
              </div>
            </div>
            <ul class="dropdown-menu">
              <li><a href="/profile.php"><i class="ti ti-user"></i> My Profile</a></li>
              <li><a href="/profile.php#settings"><i class="ti ti-settings"></i> Account Settings</a></li>
              <div class="dropdown-divider"></div>
              <li><a href="#" class="logout dropdown-logout"><i class="ti ti-logout"></i> Log Out</a></li>
            </ul>
          </div>
        </div>
      </header>
      <div class="content">
