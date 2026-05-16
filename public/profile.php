<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile | Restaurant App</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/components.css">
</head>
<body>
  <div class="profile-layout">
    <!-- Sidebar -->
    <aside class="profile-sidebar">
      <div class="avatar" id="user-avatar">👤</div>
      <div class="user-info">
        <p class="user-name" id="user-name">Loading...</p>
        <p class="user-email" id="user-email"></p>
        <p class="member-since" id="member-since"></p>
      </div>
      <nav class="sidebar-nav">
        <a href="/menu.php" class="sidebar-link">🍽️ Browse Menu</a>
        <a href="/reservation.php" class="sidebar-link">📅 New Reservation</a>
        <a href="#" class="sidebar-link logout" id="logout-link">🚪 Logout</a>
      </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="profile-main">
      <!-- Tab Navigation -->
      <div class="tab-bar">
        <button class="tab-btn active" data-tab="orders">My Orders</button>
        <button class="tab-btn" data-tab="reservations">My Reservations</button>
      </div>
      
      <!-- Orders Tab -->
      <div id="orders-tab" class="tab-content active">
        <div id="orders-list"></div>
      </div>
      
      <!-- Reservations Tab -->
      <div id="reservations-tab" class="tab-content">
        <div id="reservations-list"></div>
      </div>
    </main>
  </div>
  
  <script type="module" src="js/profile.js"></script>
</body>
</html>
