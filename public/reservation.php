<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reserve a Table | Savoria</title>
  
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
  
  <main style="max-width:1100px;margin:0 auto;padding:24px">
    <h1 style="margin-bottom:24px">Reserve a Table</h1>
    <!-- Multi-step reservation form -->
    <p class="text-muted">Reservation form loaded via JS...</p>
  </main>
  
  <script type="module" src="js/reservation.js"></script>
</body>
</html>
