<?php session_start(); if(!empty($_SESSION['user_id'])) header('Location: profile.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In | Savoria</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/components.css">
</head>
<body>
  <?php include __DIR__ . '/views/navbar.php'; ?>
  
  <div class="auth-wrapper">
    <div class="auth-card">
      <h2>Welcome Back</h2>
      <!-- CRITICAL: These IDs must match auth.js -->
      <div id="auth-message" class="auth-msg"></div>
      <form id="login-form" autocomplete="on">
        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input type="email" id="email" name="email" class="form-input" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input type="password" id="password" name="password" class="form-input" required>
        </div>
        <button type="submit" id="login-btn" class="btn btn-block">Sign In</button>
      </form>
      <p class="text-center text-muted" style="margin-top:16px">
        Don't have an account? <a href="register.php" style="color:var(--brand)">Sign up</a>
      </p>
    </div>
  </div>
  
  <script type="module" src="js/auth.js"></script>
</body>
</html>
