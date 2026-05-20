(function() {
  'use strict';
  console.log('🛠️ Admin JS loaded');

  // Logout
  const logoutBtn = document.getElementById('logout-btn');
  if (logoutBtn) logoutBtn.addEventListener('click', function(e) {
    e.preventDefault();
    if(confirm('Log out of admin panel?')) {
      fetch('/api/auth.php?action=logout', {credentials:'same-origin'}).then(() => location.href = '/login.php');
    }
  });

  // Active nav highlight
  const current = window.location.pathname.split('/').pop();
  document.querySelectorAll('.nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href && (href === current || (href !== '/' && current.startsWith(href.replace('.php',''))))) link.classList.add('active');
  });

  // Chart defaults
  if (window.Chart) {
    Chart.defaults.font.family = "'DM Sans', system-ui, sans-serif";
    Chart.defaults.color = '#6B7280';
  }

  // Shared Drawer Controls
  const overlay = document.getElementById('overlay');
  const drawer = document.getElementById('drawer');
  const closeDrawer = () => { overlay.classList.remove('open'); drawer.classList.remove('open'); };
  window.closeDrawer = closeDrawer;
  window.openDrawer = () => { overlay.classList.add('open'); drawer.classList.add('open'); };
  
  if (overlay) overlay.addEventListener('click', closeDrawer);
  const closeBtn = document.getElementById('drawer-close');
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  const cancelBtn = document.getElementById('btn-cancel');
  if (cancelBtn) cancelBtn.addEventListener('click', closeDrawer);
  // Load activity feed on dashboard
  if (window.location.pathname.includes('/admin/index.php')) {
  const script = document.createElement('script');
  script.src = '/admin/js/admin-activity.js';
  script.type = 'module';
  document.body.appendChild(script);
}
})();
