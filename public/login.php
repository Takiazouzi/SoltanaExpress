<?php declare(strict_types=1);
// ============================================================================
// Login Page
// ============================================================================
session_start();
if (isset($_SESSION['user_id'])) { header('Location: profile.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Restaurant App</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/components.css">
  <style>
    .auth-wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .auth-card { background: var(--color-surface); border-radius: var(--radius-lg); padding: 2.5rem; box-shadow: var(--shadow-card); width: 100%; max-width: 420px; animation: fadeIn 0.3s ease-out; }
    .auth-logo { text-align: center; font-size: 2.5rem; margin-bottom: 1.5rem; }
    .auth-title { text-align: center; margin-bottom: 1.5rem; font-size: 1.5rem; font-weight: 600; }
    .auth-link { display: block; text-align: center; margin-top: 1.25rem; color: var(--color-text-muted); font-size: 0.9rem; transition: color 0.2s; }
    .auth-link:hover { color: var(--color-brand); text-decoration: underline; }
    .auth-btn { width: 100%; height: 44px; margin-top: 0.5rem; }
    .auth-msg { display: none; padding: 0.75rem; border-radius: var(--radius-sm); margin-bottom: 1rem; text-align: center; font-size: 0.9rem; animation: fadeIn 0.25s ease-out; }
    .auth-msg.error { background: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5; }
    .auth-msg.success { background: #F0FDF4; color: #166534; border: 1px solid #BBF7D0; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
  </style>
</head>
<body>
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-logo">🍽️</div>
      <h1 class="auth-title">Welcome Back</h1>
      <div id="auth-message" class="auth-msg"></div>
      <form id="login-form" autocomplete="on">
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input type="email" id="email" name="email" class="form-input" required>
        </div>
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <input type="password" id="password" name="password" class="form-input" required>
        </div>
        <button type="submit" class="btn auth-btn" id="login-btn">Sign In</button>
      </form>
      <a href="register.php" class="auth-link">Don't have an account? Register</a>
    </div>
  </div>
  <script type="module" src="js/auth.js"></script>
</body>
</html>
