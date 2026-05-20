<?php
$pageTitle = 'Orders | Savoria Admin';
$breadcrumb = 'Orders';
$pageScripts = '<script src="/admin/js/admin-orders.js"></script>';
require __DIR__ . '/views/header.php';
?>

<div class="page-header">
  <h1>Orders</h1>
  <div class="controls">
    <input type="text" id="search-input" class="input" placeholder="Search customer or ID...">
  </div>
</div>

<!-- Status Tabs with Badge Counts -->
<div class="status-tabs" id="status-tabs">
  <button class="status-tab active" data-status="">All <span class="status-count" id="count-all">0</span></button>
  <button class="status-tab" data-status="pending">Pending <span class="status-count" id="count-pending">0</span></button>
  <button class="status-tab" data-status="confirmed">Confirmed <span class="status-count" id="count-confirmed">0</span></button>
  <button class="status-tab" data-status="preparing">Preparing <span class="status-count" id="count-preparing">0</span></button>
  <button class="status-tab" data-status="ready">Ready <span class="status-count" id="count-ready">0</span></button>
  <button class="status-tab" data-status="delivered">Delivered <span class="status-count" id="count-delivered">0</span></button>
  <button class="status-tab" data-status="cancelled">Cancelled <span class="status-count" id="count-cancelled">0</span></button>
</div>

<!-- Orders Table -->
<div class="table-wrapper">
  <table class="data-table">
    <thead>
      <tr>
        <th style="width:70px">Order #</th>
        <th>Customer</th>
        <th style="width:120px">Date</th>
        <th style="width:80px;text-align:right">Items</th>
        <th style="width:90px;text-align:right">Total</th>
        <th style="width:110px">Status</th>
        <th style="width:40px"></th>
      </tr>
    </thead>
    <tbody id="orders-body">
      <tr><td colspan="7" style="text-align:center;padding:24px;color:var(--text-muted)">Loading orders...</td></tr>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/views/footer.php'; ?>
