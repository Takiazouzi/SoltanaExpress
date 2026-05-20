<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!empty($_SESSION['user_id'])) {
    $redirect = ($_SESSION['role'] ?? '') === 'admin' ? '/admin/index.php' : '/profile.php';
    header('Location: ' . $redirect);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up | Savoria</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/components.css">
  <link rel="stylesheet" href="/css/auth.css">
</head>
<body class="auth-page">
  <?php include __DIR__ . '/views/navbar.php'; ?>
  
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-logo">🍽️</div>
      <h1 class="auth-title">Create Account</h1>
      <p class="auth-subtitle">Join Savoria to order food & make reservations</p>
      
      <form id="register-form" class="auth-form" autocomplete="on">
        <div class="form-group">
          <label class="form-label" for="name">Full Name</label>
          <input type="text" id="name" name="name" class="form-input" required placeholder="John Doe" minlength="2">
        </div>
        
        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input type="email" id="email" name="email" class="form-input" required placeholder="you@example.com">
        </div>
        
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="password-wrapper">
            <input type="password" id="password" name="password" class="form-input" required placeholder="Min 6 characters" minlength="6">
            <button type="button" class="password-toggle" onclick="togglePassword('password')">👁️</button>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label" for="confirm-password">Confirm Password</label>
          <div class="password-wrapper">
            <input type="password" id="confirm-password" name="confirm_password" class="form-input" required placeholder="Re-enter password" minlength="6">
            <button type="button" class="password-toggle" onclick="togglePassword('confirm-password')">👁️</button>
          </div>
        </div>
        
        <div id="auth-message" class="form-message"></div>
        
        <button type="submit" class="auth-btn" id="register-btn">Create Account</button>
      </form>
      
      <div class="auth-footer">
        Already have an account? <a href="login.php">Log in</a>
      </div>
    </div>
  </div>

  <script>
    function togglePassword(id) {
      const input = document.getElementById(id);
      input.type = input.type === 'password' ? 'text' : 'password';
    }
  </script>
  <script type="module" src="/js/auth.js"></script>
</body>
</html>
