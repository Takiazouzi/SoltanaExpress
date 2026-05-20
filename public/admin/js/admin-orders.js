(function() {
  'use strict';
  const API = '/admin/Order.php';
  let orders = [];
  let expandedRow = null;

  const $ = id => document.getElementById(id);
  const tbody = $('orders-body');
  const tabs = document.querySelectorAll('.status-tab');
  const searchInput = $('search-input');

  // Load orders with optional status filter
  async function load(status = '') {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:24px">Loading...</td></tr>';
    try {
      const url = API + '?action=list' + (status ? '&status=' + encodeURIComponent(status) : '');
      const res = await fetch(url, { credentials: 'same-origin' });
      const data = await res.json();
      if (!data.success) throw new Error(data.message);
      
      orders = data.data;
      updateBadgeCounts();
      render(orders);
    } catch (e) {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:24px;color:#DC2626">Error: ${e.message}</td></tr>`;
    }
  }

  // Update badge counts on tabs
  function updateBadgeCounts() {
    const counts = { all: orders.length, pending:0, confirmed:0, preparing:0, ready:0, delivered:0, cancelled:0 };
    orders.forEach(o => { if (counts[o.status] !== undefined) counts[o.status]++; });
    Object.keys(counts).forEach(key => {
      const el = $(`count-${key}`);
      if (el) el.textContent = counts[key];
    });
  }

  // Render orders table
  function render(data) {
    if (!data.length) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:24px;color:var(--text-muted)">No orders found</td></tr>';
      return;
    }
    tbody.innerHTML = data.map(o => `
      <tr data-id="${o.id}" data-status="${o.status}" class="order-row" onclick="toggleDetail(${o.id}, this)">
        <td><strong>#${o.id}</strong></td>
        <td>
          <div style="font-weight:500">${o.customer_name}</div>
          <div style="font-size:12px;color:var(--text-muted)">${o.customer_email}</div>
        </td>
        <td>${new Date(o.created_at).toLocaleDateString()}</td>
        <td style="text-align:right">${o.items_count}</td>
        <td style="text-align:right;font-weight:600">$${parseFloat(o.total).toFixed(2)}</td>
        <td><span class="badge badge-${o.status}">${o.status}</span></td>
        <td style="text-align:center"><i class="ti ti-chevron-down" style="font-size:16px;color:var(--text-muted)"></i></td>
      </tr>
      <tr class="row-detail" id="detail-${o.id}">
        <td colspan="7">
          <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">
            <div>
              <h4 style="margin:0 0 12px;font-size:14px">Order Items</h4>
              <table class="items-table">
                <thead><tr><th>Item</th><th style="text-align:right">Qty</th><th style="text-align:right">Price</th><th style="text-align:right">Subtotal</th></tr></thead>
                <tbody id="items-${o.id}"><tr><td colspan="4" style="text-align:center;padding:12px">Loading items...</td></tr></tbody>
                <tfoot><tr><td colspan="3" style="text-align:right"><strong>Total</strong></td><td style="text-align:right;font-weight:600">$${parseFloat(o.total).toFixed(2)}</td></tr></tfoot>
              </table>
            </div>
            <div>
              <h4 style="margin:0 0 12px;font-size:14px">Update Status</h4>
              <select class="status-select" onchange="updateStatus(${o.id}, this.value)">
                <option value="pending" ${o.status==='pending'?'selected':''}>Pending</option>
                <option value="confirmed" ${o.status==='confirmed'?'selected':''}>Confirmed</option>
                <option value="preparing" ${o.status==='preparing'?'selected':''}>Preparing</option>
                <option value="ready" ${o.status==='ready'?'selected':''}>Ready</option>
                <option value="delivered" ${o.status==='delivered'?'selected':''}>Delivered</option>
                <option value="cancelled" ${o.status==='cancelled'?'selected':''}>Cancelled</option>
              </select>
              ${o.notes ? `<p style="margin:16px 0 0;font-size:13px;color:var(--text-muted)"><strong>Notes:</strong> ${o.notes}</p>` : ''}
            </div>
          </div>
        </td>
      </tr>
    `).join('');
  }

  // Toggle row detail (accordion)
  window.toggleDetail = async function(id, row) {
    const detail = $(`detail-${id}`);
    if (!detail) return;
    
    // Close other expanded rows
    if (expandedRow && expandedRow !== detail) {
      expandedRow.classList.remove('open');
      expandedRow.previousElementSibling.querySelector('.ti-chevron-down').style.transform = '';
    }
    
    const isOpen = detail.classList.toggle('open');
    const icon = row.querySelector('.ti-chevron-down');
    icon.style.transform = isOpen ? 'rotate(180deg)' : '';
    
    if (isOpen && !detail.dataset.loaded) {
      // Lazy-load items on first open
      try {
        const res = await fetch(API + '?action=detail&id=' + id, { credentials: 'same-origin' });
        const data = await res.json();
        if (data.success) {
          const itemsBody = $(`items-${id}`);
          if (data.data.items && data.data.items.length) {
            itemsBody.innerHTML = data.data.items.map(it => `
              <tr>
                <td>${it.item_name}</td>
                <td style="text-align:right">${it.quantity}</td>
                <td style="text-align:right">$${parseFloat(it.unit_price).toFixed(2)}</td>
                <td style="text-align:right">$${(it.quantity * it.unit_price).toFixed(2)}</td>
              </tr>
            `).join('');
          } else {
            itemsBody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:8px;color:var(--text-muted)">No items</td></tr>';
          }
          detail.dataset.loaded = 'true';
        }
      } catch (e) {
        console.error('Failed to load order detail:', e);
      }
    }
    expandedRow = isOpen ? detail : null;
  };

  // Update status with optimistic UI + rollback
  window.updateStatus = async function(id, newStatus) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    const badge = row?.querySelector('.badge');
    const oldStatus = row?.dataset.status;
    
    // Optimistic update
    if (badge) {
      badge.textContent = newStatus;
      badge.className = `badge badge-${newStatus}`;
    }
    if (row) row.dataset.status = newStatus;
    
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
      const order = orders.find(o => o.id === id);
      if (order) order.status = newStatus;
      updateBadgeCounts();
    } catch (e) {
      // Rollback on error
      if (badge) {
        badge.textContent = oldStatus;
        badge.className = `badge badge-${oldStatus}`;
      }
      if (row) row.dataset.status = oldStatus;
      alert('Failed to update status: ' + e.message);
    }
  };

  // Filter by tab click
  tabs.forEach(tab => {
    tab.addEventListener('click', function() {
      tabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      load(this.dataset.status);
    });
  });

  // Search filter (client-side)
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const q = this.value.toLowerCase();
      const filtered = orders.filter(o => 
        o.customer_name.toLowerCase().includes(q) || 
        o.customer_email.toLowerCase().includes(q) ||
        o.id.toString().includes(q)
      );
      render(filtered);
    });
  }

  // Init
  document.addEventListener('DOMContentLoaded', () => load());
})();
