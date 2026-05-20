<?php
$pageTitle = 'Dashboard | Savoria Admin';
$breadcrumb = 'Dashboard';
require __DIR__ . '/views/header.php';
?>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-header"><h3>Total Revenue</h3><div class="stat-icon" style="background:#DCFCE7;color:#166534">💰</div></div>
    <div class="stat-value">$12,450</div><div class="stat-trend positive">↑ 12% this month</div>
  </div>
  <div class="stat-card">
    <div class="stat-header"><h3>Orders Today</h3><div class="stat-icon" style="background:#DBEAFE;color:#1E40AF">📦</div></div>
    <div class="stat-value">42</div><div class="stat-trend">Last updated: 2m ago</div>
  </div>
  <div class="stat-card">
    <div class="stat-header"><h3>Pending Reservations</h3><div class="stat-icon" style="background:#FEF3C7;color:#92400E">📅</div></div>
    <div class="stat-value">8</div><div class="stat-trend negative">3 need confirmation</div>
  </div>
  <div class="stat-card">
    <div class="stat-header"><h3>Menu Items</h3><div class="stat-icon" style="background:#F3E8FF;color:#7E22CE">🍽️</div></div>
    <div class="stat-value">24</div><div class="stat-trend">All active</div>
  </div>
</div>

<div class="charts-row">
  <div class="chart-card">
    <h3>Revenue Trend (7 Days)</h3>
    <div class="chart-container">
      <canvas id="revenueChart"></canvas>
    </div>
  </div>
  <div class="chart-card">
    <h3>Sales by Category</h3>
    <div class="chart-container">
      <canvas id="categoryChart"></canvas>
    </div>
  </div>
</div>

<div class="chart-card" style="margin-top:24px">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <h3>Recent Activity</h3>
    <button class="btn btn-secondary btn-sm" id="refresh-activity"><i class="ti ti-refresh"></i> Refresh</button>
  </div>
  <div class="activity-feed" id="activity-feed">
    <div class="activity-empty">Loading activity...</div>
  </div>
</div>

<?php
$pageScripts = '<script>
document.addEventListener("DOMContentLoaded", function() {
  // Chart defaults
  Chart.defaults.font.family = "DM Sans, system-ui, sans-serif";
  Chart.defaults.color = "#6B7280";
  Chart.defaults.scale.grid.color = "rgba(0,0,0,0.05)";

  // Revenue Line Chart
  new Chart(document.getElementById("revenueChart"), {
    type: "line",
    data: {
      labels: ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
      datasets: [{
        label: "Revenue ($)",
        data: [850, 1200, 950, 1400, 1150, 2100, 1850],
        borderColor: "#C1440E",
        backgroundColor: "rgba(193,68,14,0.08)",
        tension: 0.4,
        fill: true,
        pointBackgroundColor: "#fff",
        pointBorderColor: "#C1440E",
        pointRadius: 4,
        pointHoverRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true },
        x: { grid: { display: false } }
      }
    }
  });

  // Category Doughnut Chart
  new Chart(document.getElementById("categoryChart"), {
    type: "doughnut",
    data: {
      labels: ["Starters","Mains","Desserts","Drinks"],
      datasets: [{
        data: [25, 45, 20, 10],
        backgroundColor: ["#FBBF24", "#C1440E", "#10B981", "#6366F1"],
        borderWidth: 0,
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: "65%",
      plugins: {
        legend: { 
          display: true, 
          position: "right",
          labels: { 
            usePointStyle: true, 
            padding: 16,
            font: { size: 13 }
          }
        }
      }
    }
  });
});
</script>';
require __DIR__ . '/views/footer.php';
