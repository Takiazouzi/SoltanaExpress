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
    <main class="profile-main">
      <div class="tab-bar">
        <button class="tab-btn active" data-tab="orders">My Orders</button>
        <button class="tab-btn" data-tab="reservations">My Reservations</button>
        <button class="tab-btn" data-tab="account">My Account</button>
      </div>
      <div id="orders-tab" class="tab-content active"><div id="orders-list"></div></div>
      <div id="reservations-tab" class="tab-content"><div id="reservations-list"></div></div>
      <div id="account-tab" class="tab-content">
        <div class="account-card">
          <h3>Account Settings</h3>
          <form id="account-form">
            <div class="form-group">
              <label class="form-label" for="account-name">Full Name</label>
              <input type="text" id="account-name" class="form-input" required minlength="2">
            </div>
            <div class="form-group">
              <label class="form-label" for="account-email">Email Address</label>
              <input type="email" id="account-email" class="form-input" required>
              <small class="form-hint">Email changes require verification</small>
            </div>
            <div class="form-group">
              <label class="form-label" for="account-password">New Password (optional)</label>
              <input type="password" id="account-password" class="form-input" minlength="6" placeholder="Leave blank to keep current">
            </div>
            <div class="form-group">
              <label class="form-label" for="account-password-confirm">Confirm New Password</label>
              <input type="password" id="account-password-confirm" class="form-input" minlength="6">
            </div>
            <div id="account-message" class="form-message"></div>
            <button type="submit" class="btn" id="account-save-btn">Save Changes</button>
          </form>
        </div>
        <div class="account-card">
          <h3>Preferences</h3>
          <div class="form-group">
            <label class="form-label"><input type="checkbox" id="pref-newsletter" class="form-checkbox"> Subscribe to newsletter and special offers</label>
          </div>
          <div class="form-group">
            <label class="form-label"><input type="checkbox" id="pref-sms" class="form-checkbox"> Receive SMS updates for reservations</label>
          </div>
          <button type="button" class="btn" id="pref-save-btn" style="background:var(--color-bg-alt);color:var(--color-text);border:1px solid var(--color-border)">Save Preferences</button>
        </div>
        <div class="account-card danger-zone">
          <h3 style="color:#991B1B">Danger Zone</h3>
          <p style="color:var(--color-text-muted);margin-bottom:1rem">Once you delete your account, there is no going back. Please be certain.</p>
          <button type="button" class="btn" id="delete-account-btn" style="background:#FEF2F2;color:#991B1B;border:1px solid #FCA5A5">Delete Account</button>
        </div>
      </div>
    </main>
  </div>
  <script type="module" src="js/profile.js"></script>
</body>
</html>
