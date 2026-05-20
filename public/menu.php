<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu | Savoria</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/menu.css">
</head>
<body>
  <?php include __DIR__ . '/views/navbar.php'; ?>
  
  <div class="admin-banner <?= $isAdmin ? 'show' : '' ?>">
    👋 Welcome <strong><?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?></strong>. You are viewing the menu in <strong>Admin Preview Mode</strong>. Ordering is disabled.
  </div>

  <main style="max-width:1100px;margin:0 auto;padding:24px">
    <div class="menu-header">
      <h1 class="menu-title">Our Menu</h1>
      <div class="menu-search">
        <input type="text" id="menu-search" placeholder="Search dishes..." aria-label="Search menu">
      </div>
    </div>
    
    <div class="category-filter" id="category-filter">
      <button class="category-pill active" data-category="all">All</button>
    </div>
    
    <div class="menu-grid" id="menu-grid">
      <div class="menu-loading">Loading menu</div>
    </div>
    
    <div class="menu-empty hidden" id="menu-empty">
      <div class="menu-empty-icon">🍽️</div>
      <p>No items match your search.</p>
    </div>
  </main>
  
  <div class="cart-bar" id="cart-bar">
    <div class="cart-info">
      <span class="cart-count" id="cart-count">0</span>
      <div>
        <div class="cart-total" id="cart-total">$0.00</div>
        <div class="cart-items-preview" id="cart-preview"></div>
      </div>
    </div>
    <button class="cart-btn-checkout" id="btn-checkout" disabled>Place Order</button>
  </div>
  
  <div class="modal-overlay" id="order-modal">
    <div class="modal-content">
      <div class="modal-icon">✅</div>
      <h2 class="modal-title" id="modal-title">Order Placed!</h2>
      <p class="modal-message" id="modal-message">Your order is being prepared.</p>
      <div class="modal-order-summary" id="modal-summary"></div>
      <div class="modal-actions">
        <button class="modal-btn modal-btn-secondary" onclick="closeModal()">Continue Shopping</button>
        <a href="profile.php" class="modal-btn modal-btn-primary">View Orders</a>
      </div>
    </div>
  </div>

  <script>
    window.IS_ADMIN = <?= $isAdmin ? 'true' : 'false' ?>;
  </script>
  <script type="module" src="js/menu.js"></script>
</body>
</html>
