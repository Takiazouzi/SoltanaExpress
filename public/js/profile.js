(function() {
  'use strict';
  const API = '/api/profile.php';
  const $ = id => document.getElementById(id);

  async function fetchJson(url) {
    const res = await fetch(url, { credentials: 'same-origin' });
    return res.json();
  }

  // Load sidebar stats
  async function loadStats() {
    try {
      const data = await fetchJson(API);
      if (data.success) {
        if ($('stat-orders')) $('stat-orders').textContent = data.data.orders ?? 0;
        if ($('stat-reservations')) $('stat-reservations').textContent = data.data.reservations ?? 0;
        if ($('stat-users')) $('stat-users').textContent = data.data.users ?? 0;
        if ($('stat-revenue')) $('stat-revenue').textContent = '$' + (data.data.revenue ?? 0);
      }
    } catch(e) { console.error('Stats load failed:', e); }
  }

  // Render Orders
  async function loadOrders() {
    const container = $('orders-list');
    if (!container) return;
    container.innerHTML = '<div class="loading">Loading orders...</div>';
    try {
      const data = await fetchJson(API + '?action=orders');
      if (!data.success || !data.data.length) {
        container.innerHTML = '<div class="empty-state"><div class="empty-state-icon">📦</div><p class="empty-state-text">No orders yet</p><a href="/menu.php" class="empty-state-link">Browse Menu</a></div>';
        return;
      }
      container.innerHTML = data.data.map(o => `
        <div class="order-card" onclick="this.classList.toggle('expanded')">
          <div class="order-header">
            <div>
              <div class="order-id">Order #${o.id}</div>
              <div class="order-date">${new Date(o.created_at).toLocaleDateString()} • ${o.item_count} item${o.item_count>1?'s':''}</div>
            </div>
            <span class="badge badge-${o.status}">${o.status}</span>
          </div>
          <div class="order-total" style="margin-top:8px">$${parseFloat(o.total).toFixed(2)}</div>
          <div class="order-items">
            ${o.notes ? `<p style="margin-bottom:8px"><strong>Notes:</strong> ${o.notes}</p>` : ''}
            <p class="text-muted" style="font-size:12px">Click to collapse</p>
          </div>
        </div>
      `).join('');
    } catch(e) { container.innerHTML = '<div class="empty-state">Failed to load orders</div>'; }
  }

  // Render Reservations
  async function loadReservations() {
    const container = $('reservations-list');
    if (!container) return;
    container.innerHTML = '<div class="loading">Loading reservations...</div>';
    try {
      const data = await fetchJson(API + '?action=reservations');
      if (!data.success || !data.data.length) {
        container.innerHTML = '<div class="empty-state"><div class="empty-state-icon">📅</div><p class="empty-state-text">No reservations yet</p><a href="/reservation.php" class="empty-state-link">Book a Table</a></div>';
        return;
      }
      container.innerHTML = data.data.map(r => `
        <div class="reservation-card" onclick="this.classList.toggle('expanded')">
          <div class="res-header">
            <div>
              <div class="res-date">${new Date(r.date).toLocaleDateString('en-US', {weekday:'short', month:'short', day:'numeric'})}</div>
              <div class="res-time">🕐 ${r.time} • 👥 ${r.guests} guests</div>
            </div>
            <span class="badge badge-${r.status}">${r.status}</span>
          </div>
          <div class="res-details">
            ${r.special_requests ? `<p style="margin-bottom:8px"><strong>Requests:</strong> ${r.special_requests}</p>` : ''}
            <p class="text-muted" style="font-size:12px">Booked on ${new Date(r.created_at).toLocaleDateString()}</p>
          </div>
        </div>
      `).join('');
    } catch(e) { container.innerHTML = '<div class="empty-state">Failed to load reservations</div>'; }
  }

  // Tab Switching
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      const tab = this.dataset.tab;
      if ($(`${tab}-tab`)) $(`${tab}-tab`).classList.add('active');
      if (tab === 'orders') loadOrders();
      if (tab === 'reservations') loadReservations();
    });
  });

  // Init
  document.addEventListener('DOMContentLoaded', () => {
    loadStats();
    const activeTab = document.querySelector('.tab-btn.active');
    if (activeTab) {
      if (activeTab.dataset.tab === 'orders') loadOrders();
      if (activeTab.dataset.tab === 'reservations') loadReservations();
    }
  });
})();
