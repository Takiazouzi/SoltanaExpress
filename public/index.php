<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Savoria — Modern Dining Experience</title>
  
  <!-- Google Fonts: DM Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  
  <!-- Styles -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/components.css">
</head>
<body>
  <?php include __DIR__ . '/views/navbar.php'; ?>
  
  <main class="hero">
    <div class="hero-content">
      <h1>Experience the Art of Fine Dining</h1>
      <p>Seasonal ingredients, crafted with passion. Reserve your table today for an unforgettable meal.</p>
      <div class="hero-actions">
        <a href="menu.php" class="btn">Browse Menu</a>
        <a href="reservation.php" class="btn btn-secondary">Reserve a Table</a>
      </div>
    </div>
  </main>
  
  <style>
    /* Page-specific hero styles (kept minimal, scoped to .hero) */
    .hero {
      min-height: calc(100vh - 60px);
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 40px 24px;
      background: linear-gradient(135deg, var(--surface) 0%, var(--brand-light) 100%);
    }
    .hero-content { max-width: 600px; }
    .hero h1 { font-size: 2.5rem; font-weight: 600; margin-bottom: 16px; line-height: 1.2; color: var(--text); }
    .hero p { font-size: 1.1rem; color: var(--text-muted); margin-bottom: 24px; }
    .hero-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
  </style>
</body>
</html>
