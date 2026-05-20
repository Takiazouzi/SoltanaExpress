<?php
if (session_status() === PHP_SESSION_NONE) @session_start();
$currentPath = basename($_SERVER['PHP_SELF']);
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isLoggedIn = !empty($_SESSION['user_id']);
$userName = $_SESSION['name'] ?? 'Guest';
$userRole = $_SESSION['role'] ?? 'user';
$isAdmin = $userRole === 'admin';
?>
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
        <a href="profile.php" class="nav-link<?= $currentPath === 'profile.php' ? ' active' : '' ?>">My Account</a>
        <a href="#" class="nav-btn-ghost" id="nav-logout">Log out</a>
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
      <a href="#" class="nav-btn-ghost" id="nav-logout-mobile">🚪 Log out</a>
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
  function doLogout(e){if(e)e.preventDefault();if(!confirm('Log out?'))return;fetch('/api/auth.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'logout'})}).then(function(r){return r.json();}).then(function(data){if(data.success)window.location.href=data.redirect||'/login.php';}).catch(function(){window.location.href='/login.php';});}
  document.getElementById('nav-logout')?.addEventListener('click',doLogout);
  document.getElementById('nav-logout-mobile')?.addEventListener('click',doLogout);
})();
</script>
