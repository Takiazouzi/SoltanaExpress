<?php
// Navbar component - include at top of every public page
if (session_status() === PHP_SESSION_NONE) @session_start();
$currentPath = basename($_SERVER['PHP_SELF']);
$isLoggedIn = !empty($_SESSION['user_id']);
$userName = $_SESSION['name'] ?? 'Guest';
?>
<nav class="navbar">
  <div class="nav-inner">
    <!-- Fixed: Use "/" for root since -t public sets doc root to /public -->
    <a class="nav-brand" href="/">
      <span class="brand-dot"></span> Savoria
    </a>
    <div class="nav-links">
      <a href="menu.php" class="nav-link<?= $currentPath === 'menu.php' ? ' active' : '' ?>">Menu</a>
      <a href="reservation.php" class="nav-link<?= $currentPath === 'reservation.php' ? ' active' : '' ?>">Reserve</a>
      <?php if ($isLoggedIn): ?>
        <a href="profile.php" class="nav-link<?= $currentPath === 'profile.php' ? ' active' : '' ?>">My Account</a>
        <a href="logout" class="nav-btn-ghost">Log out</a>
      <?php else: ?>
        <a href="login.php" class="nav-btn-ghost">Log in</a>
        <a href="register.php" class="nav-btn-primary">Sign up</a>
      <?php endif; ?>
    </div>
    <button class="nav-hamburger" aria-label="Toggle menu">☰</button>
  </div>
  <div class="nav-drawer" id="navDrawer">
    <a href="/" class="nav-link">🏠 Home</a>
    <a href="menu.php" class="nav-link">🍽️ Menu</a>
    <a href="reservation.php" class="nav-link">📅 Reserve</a>
    <?php if ($isLoggedIn): ?>
      <a href="profile.php" class="nav-link">👤 <?= htmlspecialchars($userName) ?></a>
      <a href="logout" class="nav-btn-ghost">🚪 Log out</a>
    <?php else: ?>
      <a href="login.php" class="nav-btn-ghost">🔐 Log in</a>
      <a href="register.php" class="nav-btn-primary">✨ Sign up</a>
    <?php endif; ?>
  </div>
</nav>
<script>
(function(){var h=document.querySelector('.nav-hamburger'),d=document.getElementById('navDrawer');if(h&&d){h.addEventListener('click',function(){var e=this.getAttribute('aria-expanded')==='true';this.setAttribute('aria-expanded',!e);d.classList.toggle('active');});d.querySelectorAll('a').forEach(function(l){l.addEventListener('click',function(){if(window.innerWidth<=768){d.classList.remove('active');h.setAttribute('aria-expanded','false');}});});}var p=window.location.pathname.split('/').pop();document.querySelectorAll('.nav-link').forEach(function(l){var href=l.getAttribute('href');if(href&&p===href)l.classList.add('active');});})();
</script>
