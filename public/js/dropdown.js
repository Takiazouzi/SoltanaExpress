(function() {
  'use strict';
  console.log('📂 Dropdown JS loaded');

  // Toggle dropdown when trigger is clicked
  function toggleDropdown(trigger) {
    const container = trigger.closest('.profile-dropdown-container');
    if (!container) return;
    
    const dropdown = container.querySelector('.profile-dropdown');
    if (!dropdown) return;
    
    const isOpen = dropdown.classList.toggle('active');
    trigger.setAttribute('aria-expanded', isOpen);
    
    // Close other open dropdowns
    document.querySelectorAll('.profile-dropdown.active').forEach(function(d) {
      if (d !== dropdown) {
        d.classList.remove('active');
        const otherTrigger = d.closest('.profile-dropdown-container')?.querySelector('.profile-trigger');
        if (otherTrigger) otherTrigger.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // Handle trigger clicks with event delegation
  document.addEventListener('click', function(e) {
    const trigger = e.target.closest('.profile-trigger');
    if (trigger) {
      e.preventDefault();
      e.stopPropagation();
      toggleDropdown(trigger);
      return;
    }
    
    // Close dropdowns when clicking outside
    if (!e.target.closest('.profile-dropdown-container')) {
      document.querySelectorAll('.profile-dropdown.active').forEach(function(d) {
        d.classList.remove('active');
        const t = d.closest('.profile-dropdown-container')?.querySelector('.profile-trigger');
        if (t) t.setAttribute('aria-expanded', 'false');
      });
    }
  });

  // Handle keyboard: ESC to close, arrow keys for navigation
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.profile-dropdown.active').forEach(function(d) {
        d.classList.remove('active');
        const t = d.closest('.profile-dropdown-container')?.querySelector('.profile-trigger');
        if (t) t.setAttribute('aria-expanded', 'false');
        t?.focus();
      });
    }
  });

  // Unified Logout Handler (works for navbar AND dropdown)
  document.addEventListener('click', function(e) {
    const logoutLink = e.target.closest('.dropdown-logout, #nav-logout, #nav-logout-mobile, .sidebar-btn.danger');
    if (logoutLink) {
      e.preventDefault();
      e.stopPropagation();
      if (!confirm('Log out?')) return;
      
      fetch('/api/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'logout' }),
        credentials: 'same-origin'
      })
      .then(function(r) { return r.json(); })
      .then(function(d) {
        window.location.href = d.redirect || '/login.php';
      })
      .catch(function() {
        window.location.href = '/login.php';
      });
    }
  });

  // Initialize: ensure all triggers have proper attributes
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.profile-trigger').forEach(function(trigger) {
      trigger.setAttribute('aria-haspopup', 'true');
      trigger.setAttribute('aria-expanded', 'false');
    });
  });
  
  // Also run immediately in case DOM is already ready
  document.querySelectorAll('.profile-trigger').forEach(function(trigger) {
    trigger.setAttribute('aria-haspopup', 'true');
    trigger.setAttribute('aria-expanded', 'false');
  });
})();
