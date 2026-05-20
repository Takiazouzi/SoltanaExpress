<?php
session_start();
if (!empty($_SESSION['user_id'])) { header('Location: profile.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In | Savoria</title>
  
  <!-- Google Fonts: DM Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  
  <!-- Styles -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/components.css">
  
  <style>
    .auth-wrapper { min-height: calc(100vh - 60px); display: flex; align-items: center; justify-content: center; padding: 24px; }
    .auth-card { background: var(--surface-raised); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 32px; max-width: 420px; width: 100%; box-shadow: var(--shadow-md); }
    .auth-card h2 { margin-bottom: 24px; text-align: center; }
  </style>
</head>
<body>
  <?php include __DIR__ . '/views/navbar.php'; ?>
  
  <div class="auth-wrapper">
    <div class="auth-card">
      <h2>Welcome Back</h2>
      <form id="login-form">
        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input type="email" id="email" class="form-input" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input type="password" id="password" class="form-input" required>
        </div>
        <button type="submit" class="btn btn-block">Sign In</button>
      </form>
      <p class="text-center text-muted" style="margin-top:16px">
        Don't have an account? <a href="register.php" style="color:var(--brand);font-weight:500">Sign up</a>
      </p>
    </div>
  </div>
  
  <script type="module" src="js/auth.js"></script>
</body>
</html>
