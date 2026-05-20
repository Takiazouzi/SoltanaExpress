<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$userName = htmlspecialchars($_SESSION['name'] ?? 'User');
$userEmail = htmlspecialchars($_SESSION['email'] ?? '');
$memberSince = date('F Y', strtotime($_SESSION['created_at'] ?? 'now'));
$initials = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $userName), 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isAdmin ? 'Admin' : 'My' ?> Profile | Savoria</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/components.css">
  <link rel="stylesheet" href="/css/profile.css">
</head>
<body>
  <?php include __DIR__ . '/views/navbar.php'; ?>

  <div class="profile-layout">
    <!-- Sidebar: User Identity -->
    <aside class="profile-sidebar">
      <div class="profile-avatar" id="user-avatar"><?= $initials ?></div>
      <div class="profile-info">
        <h2 class="profile-name" id="user-name"><?= $userName ?></h2>
        <p class="profile-email" id="user-email"><?= $userEmail ?: 'No email set' ?></p>
        <span class="profile-role <?= $isAdmin ? 'admin' : '' ?>">
          <?= $isAdmin ? 'Administrator' : 'Member' ?>
        </span>
        <p class="profile-member-since">Member since <?= $memberSince ?></p>
      </div>
      
      <!-- Client Stats (Hidden for Admin) -->
      <?php if (!$isAdmin): ?>
      <div class="sidebar-stats">
        <div class="sidebar-stat"><span>Orders</span><span class="sidebar-stat-value" id="stat-orders">0</span></div>
        <div class="sidebar-stat"><span>Reservations</span><span class="sidebar-stat-value" id="stat-reservations">0</span></div>
      </div>
      <?php endif; ?>
      
      <!-- Admin Actions (Hidden for Client) -->
      <?php if ($isAdmin): ?>
      <div class="sidebar-actions">
        <button class="sidebar-btn" onclick="document.getElementById('account-name').focus()">
          <i class="ti ti-edit"></i> Edit Profile
        </button>
        <button class="sidebar-btn" onclick="document.getElementById('current-password').focus()">
          <i class="ti ti-lock"></i> Change Password
        </button>
        <button class="sidebar-btn danger" id="sidebar-delete">
          <i class="ti ti-trash"></i> Delete Account
        </button>
        <a href="/admin/index.php" class="sidebar-btn">
          <i class="ti ti-dashboard"></i> Admin Panel
        </a>
      </div>
      <?php endif; ?>
    </aside>

    <!-- Main Content -->
    <main class="profile-main">
      <!-- Tab Navigation -->
      <div class="tab-bar">
        <?php if (!$isAdmin): ?>
          <button class="tab-btn active" data-tab="orders">My Orders</button>
          <button class="tab-btn" data-tab="reservations">My Reservations</button>
        <?php endif; ?>
        <button class="tab-btn <?= $isAdmin ? 'active' : '' ?>" data-tab="account">Account Settings</button>
      </div>

      <!-- Orders Tab (Client Only) -->
      <?php if (!$isAdmin): ?>
      <div id="orders-tab" class="tab-content active">
        <div id="orders-list"><div class="loading">Loading orders...</div></div>
      </div>

      <!-- Reservations Tab (Client Only) -->
      <div id="reservations-tab" class="tab-content">
        <div id="reservations-list"><div class="loading">Loading reservations...</div></div>
      </div>
      <?php endif; ?>

      <!-- Account Settings Tab (Both) -->
      <div id="account-tab" class="tab-content <?= $isAdmin ? 'active' : '' ?>">
        
        <!-- Personal Info Card -->
        <div class="account-card">
          <h3><i class="ti ti-user"></i> Personal Information</h3>
          <form id="account-form">
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label" for="account-name">Full Name</label>
                <input type="text" id="account-name" class="form-input" value="<?= $userName ?>" required minlength="2">
              </div>
              <div class="form-group">
                <label class="form-label" for="account-email">Email Address</label>
                <input type="email" id="account-email" class="form-input" value="<?= $userEmail ?>" required>
                <small class="form-hint">Email changes may require verification</small>
              </div>
            </div>
            <button type="submit" class="btn" id="account-save-btn">Save Changes</button>
            <div id="account-message" class="form-message"></div>
          </form>
        </div>

        <!-- Password Card -->
        <div class="account-card">
          <h3><i class="ti ti-lock"></i> Change Password</h3>
          <form id="password-form">
            <div class="form-group">
              <label class="form-label" for="current-password">Current Password</label>
              <input type="password" id="current-password" class="form-input" required>
            </div>
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label" for="new-password">New Password</label>
                <input type="password" id="new-password" class="form-input" minlength="6" placeholder="Min 6 characters">
              </div>
              <div class="form-group">
                <label class="form-label" for="confirm-password">Confirm New Password</label>
                <input type="password" id="confirm-password" class="form-input" minlength="6" placeholder="Re-enter password">
              </div>
            </div>
            <button type="submit" class="btn" id="password-save-btn">Update Password</button>
            <div id="password-message" class="form-message"></div>
          </form>
        </div>

        <!-- Admin Dashboard Stats (Admin Only) -->
        <?php if ($isAdmin): ?>
        <div class="account-card">
          <h3><i class="ti ti-dashboard"></i> Quick Stats</h3>
          <div class="admin-stats">
            <div class="admin-stat">
              <div class="admin-stat-value" id="stat-orders">—</div>
              <div class="admin-stat-label">Total Orders</div>
            </div>
            <div class="admin-stat">
              <div class="admin-stat-value" id="stat-users">—</div>
              <div class="admin-stat-label">Total Users</div>
            </div>
            <div class="admin-stat">
              <div class="admin-stat-value" id="stat-revenue">—</div>
              <div class="admin-stat-label">Revenue</div>
            </div>
          </div>
          <a href="/admin/index.php" class="btn btn-block">Open Full Admin Panel →</a>
        </div>
        <?php endif; ?>

        <!-- Danger Zone (Both) -->
        <div class="account-card danger-zone">
          <h3><i class="ti ti-alert-triangle"></i> Danger Zone</h3>
          <p>Once you delete your account, there is no going back. All your data will be permanently removed.</p>
          <button type="button" class="btn btn-danger" id="delete-account-btn">Delete Account</button>
        </div>

      </div>
    </main>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal-overlay" id="delete-modal">
    <div class="modal-content">
      <div style="font-size:3rem;margin-bottom:16px">⚠️</div>
      <h3>Delete Account?</h3>
      <p>Type <strong>DELETE</strong> to confirm. This action cannot be undone.</p>
      <input type="text" id="delete-confirm-input" class="form-input" placeholder="Type DELETE here">
      <div class="modal-actions">
        <button class="btn btn-secondary" id="delete-cancel-btn">Cancel</button>
        <button class="btn btn-danger" id="delete-confirm-btn">Delete Forever</button>
      </div>
    </div>
  </div>

  <script>
    window.PROFILE_DATA = {
      isAdmin: <?= $isAdmin ? 'true' : 'false' ?>,
      userId: <?= (int)($_SESSION['user_id'] ?? 0) ?>,
      name: <?= json_encode($userName) ?>,
      email: <?= json_encode($userEmail) ?>
    };
  </script>
  <script type="module" src="/js/profile.js"></script>
</body>
</html>
