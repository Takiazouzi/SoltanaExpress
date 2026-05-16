// Profile Dashboard - Fixed API endpoint
(function() {
  'use strict';
  var API_ENDPOINT = '/api/profile.php'; // Now in public/api/
  var AUTH_ENDPOINT = '/api/auth.php';
  var currentUser = null, orders = [], reservations = [];
  
  function $(id) { return document.getElementById(id); }
  function getInitials(name) { if (!name) return '??'; var p = name.trim().split(/\s+/); return ((p[0]?p[0][0]:'')+(p[1]?p[1][0]:'')).toUpperCase(); }
  function formatDate(dateStr) { if (!dateStr) return ''; var d = new Date(dateStr); return d.toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'}); }
  function formatCurrency(amount) { var n = parseFloat(amount); return '$' + (isNaN(n)?'0.00':n.toFixed(2)); }
  function getStatusBadge(status) { var c = {pending:'badge-pending',confirmed:'badge-confirmed',preparing:'badge-preparing',ready:'badge-ready',delivered:'badge-delivered',cancelled:'badge-cancelled'}; return '<span class="badge '+(c[status]||'badge-pending')+'">'+(status?status.charAt(0).toUpperCase()+status.slice(1):'Unknown')+'</span>'; }
  
  function fetchProfileData() {
    return fetch(API_ENDPOINT, {method:'GET',credentials:'same-origin'})
    .then(function(res) { if (res.status===401) { window.location.href='/login.php'; return null; } return res.json(); })
    .then(function(data) { if (!data || !data.success) throw new Error(data?data.message:'Unknown error'); currentUser = data.user; orders = data.orders||[]; reservations = data.reservations||[]; return data; });
  }
  
  function cancelReservationApi(id) {
    return fetch(API_ENDPOINT, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'cancel_reservation',reservation_id:id}),credentials:'same-origin'}).then(function(r){return r.json();});
  }
  
  function renderUserInfo() {
    if (!currentUser) return;
    if ($('user-avatar')) $('user-avatar').textContent = getInitials(currentUser.name);
    if ($('user-name')) $('user-name').textContent = currentUser.name||'Guest';
    if ($('user-email')) $('user-email').textContent = currentUser.email||'';
    if ($('member-since')) $('member-since').textContent = 'Member since '+(currentUser.member_since||'');
  }
  
  function renderOrders() {
    var c = $('orders-list'); if (!c) return;
    if (!orders||orders.length===0) { c.innerHTML = '<div class="empty-state"><div class="empty-state-icon">🛒</div><p class="empty-state-text">No orders yet. Browse our menu to start ordering!</p><a href="/menu.php" class="empty-state-link">Browse Menu →</a></div>'; return; }
    var h = '';
    for (var i=0;i<orders.length;i++) { var o=orders[i]; h+='<div class="order-card" data-order-id="'+(o.id||'')+'"><div class="order-left"><span class="order-id">Order #'+(o.id||'?')+'</span><span class="order-date">'+formatDate(o.created_at)+'</span></div><div class="order-right"><span class="order-total">'+formatCurrency(o.total)+'</span>'+getStatusBadge(o.status)+'</div><div class="order-items"><div style="font-weight:500;margin-bottom:8px;font-size:13px">Items:</div><div class="order-item-meta">'+(o.items_count||0)+' item(s)</div></div></div>'; }
    c.innerHTML = h;
    c.querySelectorAll('.order-card').forEach(function(card){card.addEventListener('click',function(e){if(!e.target.closest('.badge'))card.classList.toggle('expanded');});});
  }
  
  function renderReservations() {
    var c = $('reservations-list'); if (!c) return;
    if (!reservations||reservations.length===0) { c.innerHTML = '<div class="empty-state"><div class="empty-state-icon">📅</div><p class="empty-state-text">No reservations yet. Book a table for your next visit!</p><a href="/reservation.php" class="empty-state-link">Make Reservation →</a></div>'; return; }
    var h = '';
    for (var i=0;i<reservations.length;i++) { var r=reservations[i]; var gt=r.guests>1?'s':''; var sp=r.special_requests?'<div style="font-size:12px;color:var(--color-text-muted);margin-top:4px">📝 '+r.special_requests+'</div>':''; var cb=r.can_cancel?'<button class="btn-cancel" data-cancel-id="'+r.id+'">Cancel</button>':''; h+='<div class="reservation-card" data-res-id="'+r.id+'"><div><div class="res-date">'+r.formatted_date+'</div><div class="res-time">🕐 '+r.formatted_time+'</div><div class="res-guests">👥 '+r.guests+' guest'+gt+'</div>'+sp+'</div><div class="res-actions">'+getStatusBadge(r.status)+cb+'</div></div>'; }
    c.innerHTML = h;
    c.querySelectorAll('[data-cancel-id]').forEach(function(btn){btn.addEventListener('click',function(e){e.stopPropagation();var id=parseInt(btn.getAttribute('data-cancel-id'));if(!id||isNaN(id))return;if(!confirm('Cancel this reservation?'))return;btn.disabled=true;btn.textContent='Cancelling...';cancelReservationApi(id).then(function(res){if(res&&res.success){fetchProfileData().then(function(){renderReservations();});}else{alert(res&&res.message?res.message:'Failed');btn.disabled=false;btn.textContent='Cancel';}}).catch(function(err){alert('Error: '+err.message);btn.disabled=false;btn.textContent='Cancel';});});});
  }
  
  function initTabs() { $$('.tab-btn').forEach(function(btn){btn.addEventListener('click',function(){ $$('.tab-btn').forEach(function(b){b.classList.remove('active');}); $$('.tab-content').forEach(function(c){c.classList.remove('active');}); btn.classList.add('active'); var t=$(btn.getAttribute('data-tab')+'-tab'); if(t)t.classList.add('active');});}); }
  function initLogout() { var lo=$('logout-link'); if(lo)lo.addEventListener('click',function(e){e.preventDefault();fetch(AUTH_ENDPOINT,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'logout'}),credentials:'same-origin'}).then(function(){window.location.href='/login.php';});}); }
  
  function init() {
    console.log('Profile initializing...');
    if($('orders-list'))$('orders-list').innerHTML='<div class="loading">Loading...</div>';
    fetchProfileData().then(function(){console.log('Data loaded');renderUserInfo();renderOrders();renderReservations();initTabs();initLogout();console.log('Ready');}).catch(function(err){console.error(err);if($('orders-list'))$('orders-list').innerHTML='<div class="empty-state"><p style="color:#991B1B">Error: '+err.message+'</p><button onclick="location.reload()" style="margin-top:10px;padding:8px 16px;background:var(--color-brand);color:#fff;border:none;border-radius:6px;cursor:pointer">Retry</button></div>';});
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
