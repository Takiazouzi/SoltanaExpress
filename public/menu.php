<?php
session_start();
// Redirect to login if not logged in (optional, but good for UX)
// if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu | Restaurant App</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/components.css">
  <style>
    /* Page specific layout */
    .menu-page { max-width: 1200px; margin: 0 auto; padding: var(--spacing-lg); }
    .menu-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg); }
    .back-link { color: var(--color-text-muted); text-decoration: none; font-weight: 500; }
  </style>
</head>
<body>
  <div class="menu-page">
    <header class="menu-header">
      <h1>Our Menu</h1>
      <a href="/profile.php" class="back-link">← Back to Profile</a>
    </header>

    <!-- Category Filter -->
    <div class="filter-bar" id="category-filters"></div>

    <!-- Menu Grid -->
    <div class="menu-grid" id="menu-grid">
      <div class="loading" style="grid-column: 1/-1">Loading menu...</div>
    </div>
  </div>

  <!-- Fixed Cart Bar -->
  <div class="cart-bar" id="cart-bar">
    <div class="cart-info">
      <span class="cart-count" id="cart-count">0</span>
      <span class="cart-total" id="cart-total">$0.00</span>
    </div>
    <button class="cart-btn-checkout" id="btn-checkout">Place Order</button>
  </div>

  <!-- Order Modal -->
  <div class="modal-overlay" id="order-modal">
    <div class="modal-content">
      <div class="modal-icon">🍽️</div>
      <h2 id="modal-title">Order Placed!</h2>
      <p id="modal-msg" style="color:var(--color-text-muted);margin-bottom:1rem">Your order is being prepared.</p>
      <div class="modal-actions">
        <button class="btn" onclick="window.location.href='/profile.php'">View Orders</button>
        <button class="btn" style="background:var(--color-bg-alt);color:var(--color-text)" onclick="closeModal()">Close</button>
      </div>
    </div>
  </div>

  <script type="module" src="js/menu.js"></script>
</body>
</html>
