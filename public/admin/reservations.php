<?php
$pageTitle = 'Reservations | Savoria Admin';
$breadcrumb = 'Reservations';
$pageScripts = '<script src="/admin/js/admin-reservations.js"></script>';
require __DIR__ . '/views/header.php';
?>

<div class="page-header">
  <h1>Reservations</h1>
  <div class="controls">
    <input type="date" id="date-filter" class="date-picker" value="<?= date('Y-m-d') ?>">
    <select id="status-filter" class="select">
      <option value="">All Statuses</option>
      <option value="pending">Pending</option>
      <option value="confirmed">Confirmed</option>
      <option value="cancelled">Cancelled</option>
    </select>
  </div>
</div>

<!-- Status Tabs for Quick Filter -->
<div class="status-tabs">
  <button class="status-tab active" data-status="">All</button>
  <button class="status-tab" data-status="pending">Pending</button>
  <button class="status-tab" data-status="confirmed">Confirmed</button>
  <button class="status-tab" data-status="cancelled">Cancelled</button>
</div>

<!-- Reservations Grid -->
<div class="res-grid" id="reservations-grid">
  <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted)">Loading reservations...</div>
</div>

<?php require __DIR__ . '/views/footer.php'; ?>
