<?php
if (session_status() === PHP_SESSION_NONE) @session_start();
$currentPath = basename($_SERVER['PHP_SELF']);
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isLoggedIn = !empty($_SESSION['user_id']);
$userName = $_SESSION['name'] ?? 'Guest';
$userRole = $_SESSION['role'] ?? 'user';
$isAdmin = $userRole === 'admin';
$initials = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $userName), 0, 2));
?>
<link rel="stylesheet" href="/css/dropdown.css">
<nav class="navbar">
  <div class="nav-inner">
    <a class="nav-brand" href="/">
      <span class="brand-dot"></span> Savoria
    </a>
    <div class="nav-links">
      <?php if (!$isAdmin): ?>
        <a href="menu.php" class="nav-link<?= $currentPath === 'menu.php' ? ' active' : '' ?>">Menu</a>
        <a href="reservation.php" class="nav-link<?= $currentPath === 'reservation.php' ? ' active' : '' ?>">Reserve</a>
      <?php endif; ?>
      <?php if ($isAdmin): ?>
        <a href="/admin/index.php" class="nav-link<?= strpos($requestUri, '/admin/') !== false ? ' active' : '' ?>">Admin</a>
      <?php endif; ?>
      
      <?php if ($isLoggedIn): ?>
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
              <p class="email"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></p>
              <span class="role-badge"><?= $isAdmin ? 'Administrator' : 'Member' ?></span>
            </div>
          </div>
          <ul class="dropdown-menu">
            <li><a href="profile.php"><i class="ti ti-user"></i> My Profile</a></li>
            <li><a href="profile.php#settings"><i class="ti ti-settings"></i> Settings</a></li>
            <div class="dropdown-divider"></div>
            <li><a href="#" class="logout dropdown-logout"><i class="ti ti-logout"></i> Log Out</a></li>
          </ul>
        </div>
      </div>
      <?php else: ?>
        <a href="login.php" class="nav-btn-ghost">Log in</a>
        <a href="register.php" class="nav-btn-primary">Sign up</a>
      <?php endif; ?>
    </div>
    <button class="nav-hamburger" aria-label="Toggle menu">☰</button>
  </div>
  <div class="nav-drawer" id="navDrawer">
    <a href="/" class="nav-link">🏠 Home</a>
    <?php if (!$isAdmin): ?>
      <a href="menu.php" class="nav-link">🍽️ Menu</a>
      <a href="reservation.php" class="nav-link">📅 Reserve</a>
    <?php endif; ?>
    <?php if ($isAdmin): ?>
      <a href="/admin/index.php" class="nav-link">⚙️ Admin Panel</a>
    <?php endif; ?>
    <?php if ($isLoggedIn): ?>
      <a href="profile.php" class="nav-link">👤 <?= htmlspecialchars($userName) ?></a>
      <a href="#" class="nav-btn-ghost dropdown-logout">🚪 Log out</a>
    <?php else: ?>
      <a href="login.php" class="nav-btn-ghost">🔐 Log in</a>
      <a href="register.php" class="nav-btn-primary">✨ Sign up</a>
    <?php endif; ?>
  </div>
</nav>
<script>
(function(){
  var h=document.querySelector('.nav-hamburger'),d=document.getElementById('navDrawer');
  if(h&&d){h.addEventListener('click',function(){var e=this.getAttribute('aria-expanded')==='true';this.setAttribute('aria-expanded',!e);d.classList.toggle('active');});d.querySelectorAll('a').forEach(function(l){l.addEventListener('click',function(){if(window.innerWidth<=768){d.classList.remove('active');h.setAttribute('aria-expanded','false');}});});}
  var p=window.location.pathname.split('/').pop();document.querySelectorAll('.nav-link').forEach(function(l){var href=l.getAttribute('href');if(href&&p===href)l.classList.add('active');});
})();
</script>
<script src="/js/dropdown.js"></script>
