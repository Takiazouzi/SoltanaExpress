<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu | Savoria</title>
  
  <!-- Google Fonts: DM Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  
  <!-- Styles -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/components.css">
  
  <style>
    /* Menu Page Specific Styles */
    .menu-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      flex-wrap: wrap;
      gap: 16px;
    }
    
    .menu-title {
      font-size: 1.8rem;
      font-weight: 600;
      margin: 0;
    }
    
    .menu-search {
      position: relative;
      max-width: 280px;
      width: 100%;
    }
    
    .menu-search input {
      width: 100%;
      padding: 10px 14px 10px 36px;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      font-size: 14px;
      background: var(--surface-raised);
    }
    
    .menu-search::before {
      content: "🔍";
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 14px;
    }
    
    /* Category Filter Pills */
    .category-filter {
      display: flex;
      gap: 8px;
      overflow-x: auto;
      padding: 8px 0 16px;
      margin-bottom: 16px;
      scrollbar-width: none;
    }
    .category-filter::-webkit-scrollbar { display: none; }
    
    .category-pill {
      padding: 8px 16px;
      background: var(--surface-raised);
      border: 1px solid var(--border);
      border-radius: 20px;
      font-size: 14px;
      font-weight: 500;
      color: var(--text-muted);
      cursor: pointer;
      white-space: nowrap;
      transition: all var(--transition);
    }
    
    .category-pill:hover {
      border-color: var(--brand);
      color: var(--text);
    }
    
    .category-pill.active {
      background: var(--brand);
      color: #fff;
      border-color: var(--brand);
    }
    
    /* Menu Grid */
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 120px;
    }
    
    .menu-card {
      background: var(--surface-raised);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      overflow: hidden;
      transition: transform var(--transition), box-shadow var(--transition);
      display: flex;
      flex-direction: column;
    }
    
    .menu-card:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-md);
    }
    
    .menu-card-img {
      height: 160px;
      background: linear-gradient(135deg, var(--brand-light) 0%, var(--surface) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      color: var(--brand);
    }
    
    .menu-card-body {
      padding: 16px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    
    .menu-card-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin: 0 0 6px;
      color: var(--text);
    }
    
    .menu-card-desc {
      font-size: 0.9rem;
      color: var(--text-muted);
      margin: 0 0 12px;
      flex: 1;
      line-height: 1.5;
    }
    
    .menu-card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: auto;
    }
    
    .menu-card-price {
      font-weight: 600;
      font-size: 1.1rem;
      color: var(--brand);
    }
    
    .btn-add {
      padding: 8px 16px;
      background: var(--brand);
      color: #fff;
      border: none;
      border-radius: var(--radius-sm);
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: background var(--transition);
    }
    
    .btn-add:hover {
      background: var(--brand-dark);
    }
    
    .btn-add:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }
    
    /* Loading State */
    .menu-loading {
      grid-column: 1 / -1;
      text-align: center;
      padding: 40px;
      color: var(--text-muted);
    }
    
    .menu-loading::after {
      content: "";
      display: inline-block;
      width: 24px;
      height: 24px;
      border: 3px solid var(--border);
      border-top-color: var(--brand);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-left: 12px;
      vertical-align: middle;
    }
    
    @keyframes spin { to { transform: rotate(360deg); } }
    
    /* Empty State */
    .menu-empty {
      grid-column: 1 / -1;
      text-align: center;
      padding: 60px 20px;
      color: var(--text-muted);
    }
    
    .menu-empty-icon {
      font-size: 3rem;
      margin-bottom: 16px;
      opacity: 0.6;
    }
    
    /* Cart Bar (Fixed Bottom) */
    .cart-bar {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: var(--surface-raised);
      border-top: 1px solid var(--border);
      box-shadow: 0 -4px 12px rgba(0,0,0,0.08);
      padding: 16px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 100;
      transform: translateY(100%);
      transition: transform 0.3s ease;
    }
    
    .cart-bar.active {
      transform: translateY(0);
    }
    
    .cart-info {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    
    .cart-count {
      background: var(--brand);
      color: #fff;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 500;
    }
    
    .cart-total {
      font-weight: 600;
      font-size: 1.2rem;
      color: var(--text);
    }
    
    .cart-items-preview {
      font-size: 13px;
      color: var(--text-muted);
      max-width: 200px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    .cart-btn-checkout {
      padding: 12px 28px;
      background: var(--brand);
      color: #fff;
      border: none;
      border-radius: var(--radius-sm);
      font-weight: 500;
      font-size: 15px;
      cursor: pointer;
      transition: background var(--transition);
    }
    
    .cart-btn-checkout:hover {
      background: var(--brand-dark);
    }
    
    .cart-btn-checkout:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }
    
    /* Order Modal */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      padding: 20px;
    }
    
    .modal-overlay.active {
      display: flex;
      animation: fadeIn 0.2s ease;
    }
    
    .modal-content {
      background: var(--surface-raised);
      padding: 24px;
      border-radius: var(--radius-lg);
      max-width: 420px;
      width: 100%;
      text-align: center;
      box-shadow: var(--shadow-md);
    }
    
    .modal-icon {
      font-size: 3rem;
      margin-bottom: 16px;
    }
    
    .modal-title {
      font-size: 1.3rem;
      font-weight: 600;
      margin: 0 0 8px;
      color: var(--text);
    }
    
    .modal-message {
      color: var(--text-muted);
      margin: 0 0 20px;
      font-size: 15px;
    }
    
    .modal-order-summary {
      background: var(--surface);
      border-radius: var(--radius-md);
      padding: 16px;
      margin: 0 0 20px;
      text-align: left;
      font-size: 14px;
    }
    
    .modal-order-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
    }
    
    .modal-order-row:last-child {
      margin-bottom: 0;
      padding-top: 12px;
      border-top: 1px solid var(--border);
      font-weight: 600;
      color: var(--brand);
    }
    
    .modal-actions {
      display: flex;
      gap: 12px;
      justify-content: center;
    }
    
    .modal-btn {
      padding: 10px 24px;
      border-radius: var(--radius-sm);
      font-weight: 500;
      cursor: pointer;
      border: none;
      transition: background var(--transition);
    }
    
    .modal-btn-primary {
      background: var(--brand);
      color: #fff;
    }
    
    .modal-btn-primary:hover {
      background: var(--brand-dark);
    }
    
    .modal-btn-secondary {
      background: var(--surface);
      color: var(--text);
      border: 1px solid var(--border);
    }
    
    .modal-btn-secondary:hover {
      background: var(--surface-raised);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .menu-header {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .menu-search {
        max-width: 100%;
      }
      
      .menu-grid {
        grid-template-columns: 1fr;
        margin-bottom: 100px;
      }
      
      .cart-bar {
        padding: 12px 16px;
        flex-direction: column;
        gap: 12px;
        text-align: center;
      }
      
      .cart-info {
        width: 100%;
        justify-content: center;
      }
      
      .cart-btn-checkout {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/views/navbar.php'; ?>
  
  <main style="max-width:1100px;margin:0 auto;padding:24px">
    <div class="menu-header">
      <h1 class="menu-title">Our Menu</h1>
      <div class="menu-search">
        <input type="text" id="menu-search" placeholder="Search dishes..." aria-label="Search menu">
      </div>
    </div>
    
    <!-- Category Filter Pills -->
    <div class="category-filter" id="category-filter">
      <button class="category-pill active" data-category="all">All</button>
      <!-- Categories injected by JS -->
    </div>
    
    <!-- Menu Grid -->
    <div class="menu-grid" id="menu-grid">
      <div class="menu-loading">Loading menu</div>
    </div>
    
    <!-- Empty State (hidden by default) -->
    <div class="menu-empty hidden" id="menu-empty">
      <div class="menu-empty-icon">🍽️</div>
      <p>No items match your search.</p>
      <p class="text-hint" style="margin-top:8px">Try a different term or browse all categories.</p>
    </div>
  </main>
  
  <!-- Fixed Cart Bar -->
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
  
  <!-- Order Confirmation Modal -->
  <div class="modal-overlay" id="order-modal">
    <div class="modal-content">
      <div class="modal-icon">✅</div>
      <h2 class="modal-title" id="modal-title">Order Placed!</h2>
      <p class="modal-message" id="modal-message">Your order is being prepared.</p>
      
      <div class="modal-order-summary" id="modal-summary">
        <!-- Order items injected by JS -->
      </div>
      
      <div class="modal-actions">
        <button class="modal-btn modal-btn-secondary" onclick="closeModal()">Continue Shopping</button>
        <a href="profile.php" class="modal-btn modal-btn-primary">View Orders</a>
      </div>
    </div>
  </div>
  
  <script type="module" src="js/menu.js"></script>
</body>
</html>
