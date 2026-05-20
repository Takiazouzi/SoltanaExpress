(function() {
  'use strict';
  const API = '/admin/Reservation.php';
  let reservations = [];

  const $ = id => document.getElementById(id);
  const grid = $('reservations-grid');
  const dateFilter = $('date-filter');
  const statusFilter = $('status-filter');
  const tabs = document.querySelectorAll('.status-tab');

  // Load reservations with filters
  async function load(date = '', status = '') {
    grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px">Loading...</div>';
    try {
      let url = API + '?action=list';
      if (date) url += '&date=' + encodeURIComponent(date);
      if (status) url += '&status=' + encodeURIComponent(status);
      
      const res = await fetch(url, { credentials: 'same-origin' });
      const data = await res.json();
      if (!data.success) throw new Error(data.message);
      
      reservations = data.data;
      render(reservations);
    } catch (e) {
      grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px;color:#DC2626">Error: ${e.message}</div>`;
    }
  }

  // Render reservation cards
  function render(data) {
    if (!data.length) {
      grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted)">No reservations found</div>';
      return;
    }
    
    grid.innerHTML = data.map(r => {
      const dt = new Date(r.date + 'T' + r.time);
      const formattedDate = dt.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
      const formattedTime = dt.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
      
      return `
        <div class="res-card" data-id="${r.id}" data-status="${r.status}">
          <div class="res-header">
            <div>
              <div class="res-date">${formattedDate}</div>
              <div class="res-time">🕐 ${formattedTime}</div>
              <div class="res-guests">👥 ${r.guests} guest${r.guests > 1 ? 's' : ''}</div>
            </div>
            <span class="badge badge-${r.status}">${r.status}</span>
          </div>
          <div class="res-customer">${r.customer_name}<br><small style="color:var(--text-muted)">${r.customer_email}</small></div>
          ${r.special_requests ? `<div class="res-requests">📝 ${r.special_requests}</div>` : ''}
          <div class="res-actions">
            ${r.status === 'pending' ? `
              <button class="btn btn-secondary" onclick="updateResStatus(${r.id}, 'cancelled')">Cancel</button>
              <button class="btn btn-primary" onclick="updateResStatus(${r.id}, 'confirmed')">Confirm</button>
            ` : r.status === 'confirmed' ? `
              <button class="btn btn-danger" onclick="updateResStatus(${r.id}, 'cancelled')">Cancel</button>
            ` : ''}
          </div>
        </div>
      `;
    }).join('');
  }

  // Update reservation status with optimistic UI
  window.updateResStatus = async function(id, newStatus) {
    const card = document.querySelector(`.res-card[data-id="${id}"]`);
    const badge = card?.querySelector('.badge');
    const actions = card?.querySelector('.res-actions');
    const oldStatus = card?.dataset.status;
    
    // Optimistic update
    if (badge) {
      badge.textContent = newStatus;
      badge.className = `badge badge-${newStatus}`;
    }
    if (card) card.dataset.status = newStatus;
    
    // Update actions based on new status
    if (actions) {
      if (newStatus === 'confirmed') {
        actions.innerHTML = `<button class="btn btn-danger" onclick="updateResStatus(${id}, 'cancelled')">Cancel</button>`;
      } else if (newStatus === 'cancelled') {
        actions.innerHTML = '';
      }
    }
    
    try {
      const res = await fetch(API, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update_status&id=${id}&status=${encodeURIComponent(newStatus)}`
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.message);
      
      // Update local data
      const resItem = reservations.find(r => r.id === id);
      if (resItem) resItem.status = newStatus;
    } catch (e) {
      // Rollback
      if (badge) {
        badge.textContent = oldStatus;
        badge.className = `badge badge-${oldStatus}`;
      }
      if (card) card.dataset.status = oldStatus;
      if (actions && oldStatus === 'pending') {
        actions.innerHTML = `
          <button class="btn btn-secondary" onclick="updateResStatus(${id}, 'cancelled')">Cancel</button>
          <button class="btn btn-primary" onclick="updateResStatus(${id}, 'confirmed')">Confirm</button>
        `;
      }
      alert('Failed to update: ' + e.message);
    }
  };

  // Filter handlers
  function applyFilters() {
    const date = dateFilter?.value || '';
    const status = statusFilter?.value || '';
    load(date, status);
  }
  
  if (dateFilter) dateFilter.addEventListener('change', applyFilters);
  if (statusFilter) statusFilter.addEventListener('change', applyFilters);
  
  tabs.forEach(tab => {
    tab.addEventListener('click', function() {
      tabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      if (statusFilter) statusFilter.value = this.dataset.status;
      applyFilters();
    });
  });

  // Init
  document.addEventListener('DOMContentLoaded', () => {
    if (dateFilter) dateFilter.value = new Date().toISOString().split('T')[0];
    load(dateFilter?.value || '', '');
  });
})();
