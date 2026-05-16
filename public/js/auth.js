// ============================================================================
// Authentication Logic (Fetch API, No Reloads)
// ============================================================================

const API_ENDPOINT = '../src/controllers/AuthController.php';

export async function fetchJSON(action, payload) {
  const response = await fetch(API_ENDPOINT, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action, ...payload })
  });
  const data = await response.json();
  if (!response.ok) throw new Error(data.message || 'Request failed');
  return data;
}

function showMessage(msgEl, message, type = 'error') {
  msgEl.textContent = message;
  msgEl.className = `auth-msg ${type}`;
  msgEl.style.display = 'block';
  setTimeout(() => { msgEl.style.display = 'none'; }, 5000);
}

function setBtnLoading(btn, isLoading) {
  if (isLoading) {
    btn.dataset.originalText = btn.textContent;
    btn.textContent = '...';
    btn.disabled = true;
  } else {
    btn.textContent = btn.dataset.originalText || 'Submit';
    btn.disabled = false;
  }
}

async function handleLogin(e) {
  e.preventDefault();
  const form = e.target;
  const msgEl = document.getElementById('auth-message');
  const btn = document.getElementById('login-btn');

  msgEl.style.display = 'none';
  setBtnLoading(btn, true);

  try {
    const res = await fetchJSON('login', {
      email: form.email.value.trim(),
      password: form.password.value
    });
    if (res.success) {
      showMessage(msgEl, res.message, 'success');
      setTimeout(() => window.location.href = res.redirect || '/profile.php', 800);
    } else {
      showMessage(msgEl, res.message, 'error');
    }
  } catch (err) {
    showMessage(msgEl, err.message, 'error');
  } finally {
    setBtnLoading(btn, false);
  }
}

async function handleRegister(e) {
  e.preventDefault();
  const form = e.target;
  const msgEl = document.getElementById('auth-message');
  const btn = document.getElementById('register-btn');

  msgEl.style.display = 'none';
  document.querySelectorAll('.field-error').forEach(el => el.style.display = 'none');

  // Client-side validation
  if (form.password.value !== form.confirm_password.value) {
    document.getElementById('confirm-error').textContent = 'Passwords do not match.';
    document.getElementById('confirm-error').style.display = 'block';
    return;
  }
  if (form.password.value.length < 6) {
    document.getElementById('password').focus();
    return;
  }

  setBtnLoading(btn, true);

  try {
    const res = await fetchJSON('register', {
      name: form.name.value.trim(),
      email: form.email.value.trim(),
      password: form.password.value,
      confirm_password: form.confirm_password.value
    });
    if (res.success) {
      showMessage(msgEl, res.message, 'success');
      setTimeout(() => window.location.href = '/login.php', 1000);
    } else {
      showMessage(msgEl, res.message, 'error');
    }
  } catch (err) {
    showMessage(msgEl, err.message, 'error');
  } finally {
    setBtnLoading(btn, false);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('login-form');
  const registerForm = document.getElementById('register-form');

  if (loginForm) loginForm.addEventListener('submit', handleLogin);
  if (registerForm) registerForm.addEventListener('submit', handleRegister);
});
