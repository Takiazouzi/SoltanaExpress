// ============================================================================
// Authentication Logic - DEBUG MODE
// Logs every step to console + shows errors in UI
// ============================================================================
(function() {
  'use strict';
  
  console.log('🔐 auth.js loaded');
  
  const API = '/api/auth.php';
  
  // Safe DOM selector
  function $(id) {
    const el = document.getElementById(id);
    if (!el) console.warn('⚠️ Missing element: #' + id);
    return el;
  }
  
  // Show message in UI
  function showMessage(container, msg, type = 'error') {
    if (!container) {
      console.error('❌ Cannot show message: container not found');
      alert(msg); // Fallback
      return;
    }
    console.log('💬 Showing message:', msg, type);
    container.textContent = msg;
    container.className = 'auth-msg ' + type;
    container.style.display = 'block';
    // Auto-hide after 4 seconds
    setTimeout(() => { container.style.display = 'none'; }, 4000);
  }
  
  // Toggle button loading state
  function setLoading(btn, loading) {
    if (!btn) return;
    if (loading) {
      btn.dataset.origText = btn.textContent;
      btn.textContent = '...';
      btn.disabled = true;
      console.log('🔄 Button loading state: ON');
    } else {
      btn.textContent = btn.dataset.origText || 'Sign In';
      btn.disabled = false;
      console.log('🔄 Button loading state: OFF');
    }
  }
  
  // LOGIN HANDLER
  async function handleLogin(e) {
    e.preventDefault(); // CRITICAL: Stop page reload
    console.log('📤 Login form submitted');
    
    const form = e.target;
    const btn = $('login-btn');
    const msg = $('auth-message');
    
    if (!msg) {
      console.error('❌ FATAL: #auth-message not found in DOM');
      alert('UI error: Missing message container');
      return;
    }
    
    msg.style.display = 'none';
    setLoading(btn, true);
    
    try {
      console.log('🌐 Fetching:', API);
      const res = await fetch(API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'login',
          email: form.email?.value?.trim() || '',
          password: form.password?.value || ''
        }),
        credentials: 'same-origin' // Send cookies for session
      });
      
      console.log('📥 Response status:', res.status, res.ok);
      
      // Check content type
      const contentType = res.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        throw new Error('Server returned non-JSON: ' + contentType);
      }
      
      const data = await res.json();
      console.log('📦 Response data:', data);
      
      if (data.success) {
        showMessage(msg, data.message || 'Success!', 'success');
        const redirect = data.redirect || '/profile.php';
        console.log('🔀 Redirecting to:', redirect);
        
        // Force visible redirect
        setTimeout(() => {
          window.location.href = redirect;
        }, 500);
      } else {
        console.warn('⚠️ Login failed:', data.message);
        showMessage(msg, data.message || 'Login failed', 'error');
      }
      
    } catch (err) {
      console.error('💥 Fetch error:', err.name, err.message);
      console.error('Stack:', err.stack);
      showMessage(msg, 'Error: ' + err.message, 'error');
    } finally {
      setLoading(btn, false);
    }
  }
  
  // REGISTER HANDLER
  async function handleRegister(e) {
    e.preventDefault();
    console.log('📤 Register form submitted');
    
    const form = e.target;
    const btn = $('register-btn');
    const msg = $('auth-message');
    
    if (!msg) return;
    msg.style.display = 'none';
    
    // Client-side validation
    if (form.password?.value !== form.confirm_password?.value) {
      showMessage(msg, 'Passwords do not match', 'error');
      return;
    }
    if ((form.password?.value || '').length < 6) {
      showMessage(msg, 'Password must be at least 6 characters', 'error');
      return;
    }
    
    setLoading(btn, true);
    
    try {
      const res = await fetch(API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'register',
          name: form.name?.value?.trim() || '',
          email: form.email?.value?.trim() || '',
          password: form.password?.value || '',
          confirm_password: form.confirm_password?.value || ''
        }),
        credentials: 'same-origin'
      });
      
      const data = await res.json();
      console.log('📦 Register response:', data);
      
      if (data.success) {
        showMessage(msg, 'Account created! Redirecting...', 'success');
        setTimeout(() => window.location.href = '/login.php', 1000);
      } else {
        showMessage(msg, data.message || 'Registration failed', 'error');
      }
    } catch (err) {
      console.error(err);
      showMessage(msg, 'Network error: ' + err.message, 'error');
    } finally {
      setLoading(btn, false);
    }
  }
  
  // INIT: Attach listeners when DOM ready
  function init() {
    console.log('🔍 Initializing auth listeners...');
    
    const loginForm = $('login-form');
    const regForm = $('register-form');
    
    if (loginForm) {
      loginForm.addEventListener('submit', handleLogin);
      console.log('✅ Login listener attached to #login-form');
    } else {
      console.warn('⚠️ #login-form not found - are you on login.php?');
    }
    
    if (regForm) {
      regForm.addEventListener('submit', handleRegister);
      console.log('✅ Register listener attached to #register-form');
    } else {
      console.warn('⚠️ #register-form not found - are you on register.php?');
    }
  }
  
  // Start
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
    console.log('⏳ DOM not ready, waiting...');
  } else {
    console.log('✅ DOM already ready, initializing now');
    init();
  }
  
})();
