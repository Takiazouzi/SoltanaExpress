(function() {
  'use strict';

  const API_ENDPOINT = '/api/menu.php';
  const ORDER_ENDPOINT = '/api/order.php';
  
  // State
  var cart = [];
  var allItems = [];

  // DOM Helpers
  function $(id) { return document.getElementById(id); }
  function $$(sel) { return document.querySelectorAll(sel); }

  // Utilities
  function formatPrice(price) { return '$' + parseFloat(price).toFixed(2); }
  function getCartTotal() { return cart.reduce((sum, item) => sum + (item.price * item.qty), 0); }
  function getCartCount() { return cart.reduce((sum, item) => sum + item.qty, 0); }

  function renderCategoryFilters(categories) {
    var filterBar = $('category-filter');
    if (!filterBar) return;
    var allBtn = filterBar.querySelector('[data-category="all"]');
    filterBar.innerHTML = '';
    if (allBtn) filterBar.appendChild(allBtn);
    
    categories.forEach(function(cat) {
      var btn = document.createElement('button');
      btn.className = 'category-pill';
      btn.textContent = cat;
      btn.setAttribute('data-category', cat);
      btn.addEventListener('click', function() {
        $$('.category-pill').forEach(function(b) { b.classList.remove('active'); });
        btn.classList.add('active');
        filterItemsByCategory(cat);
      });
      filterBar.appendChild(btn);
    });
  }

  function filterItemsByCategory(category) {
    var cards = $$('#menu-grid .menu-card');
    cards.forEach(function(card) {
      var itemCat = card.getAttribute('data-category');
      card.style.display = (category === 'all' || itemCat === category) ? 'flex' : 'none';
    });
    var visible = Array.from(cards).filter(function(c) { return c.style.display !== 'none'; });
    $('menu-empty').classList.toggle('hidden', visible.length > 0);
  }

  function setupSearch() {
    var searchInput = $('menu-search');
    if (!searchInput) return;
    searchInput.addEventListener('input', function() {
      var term = this.value.toLowerCase().trim();
      var cards = $$('#menu-grid .menu-card');
      var visibleCount = 0;
      cards.forEach(function(card) {
        var name = card.getAttribute('data-name') || '';
        var desc = card.getAttribute('data-desc') || '';
        var matches = name.toLowerCase().includes(term) || desc.toLowerCase().includes(term);
        card.style.display = matches ? 'flex' : 'none';
        if (matches) visibleCount++;
      });
      $('menu-empty').classList.toggle('hidden', visibleCount > 0 || term === '');
    });
  }

  function renderMenu(data) {
    var grid = $('menu-grid');
    if (!grid) return;
    grid.innerHTML = '';
    allItems = [];
    
    var categories = data.map(function(cat) { return cat.name; });
    renderCategoryFilters(categories);
    
    data.forEach(function(category) {
      category.items.forEach(function(item) {
        allItems.push(item);
        var card = document.createElement('div');
        card.className = 'menu-card';
        card.setAttribute('data-category', item.category);
        card.setAttribute('data-name', item.name);
        card.setAttribute('data-desc', item.description || '');
        
        // If Admin, DO NOT render the Add button
        var btnHtml = window.IS_ADMIN ? '' : 
          '<button class="btn-add" data-id="' + item.id + '" data-name="' + item.name + '" data-price="' + item.price + '">Add +</button>';

        card.innerHTML = 
          '<div class="menu-card-img">🍽️</div>' +
          '<div class="menu-card-body">' +
            '<h3 class="menu-card-title">' + item.name + '</h3>' +
            '<p class="menu-card-desc">' + (item.description || '') + '</p>' +
            '<div class="menu-card-footer">' +
              '<span class="menu-card-price">' + formatPrice(item.price) + '</span>' +
              btnHtml +
            '</div>' +
          '</div>';
        
        grid.appendChild(card);
      });
    });
    
    // Attach Add listeners (only if buttons exist)
    $$('#menu-grid .btn-add').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var id = parseInt(this.getAttribute('data-id'));
        var name = this.getAttribute('data-name');
        var price = parseFloat(this.getAttribute('data-price'));
        addToCart(id, name, price);
      });
    });
  }

  function addToCart(id, name, price) {
    if (window.IS_ADMIN) return; // Security check
    var existing = cart.find(function(item) { return item.id === id; });
    if (existing) existing.qty++;
    else cart.push({ id: id, name: name, price: price, qty: 1 });
    updateCartUI();
  }

  function removeFromCart(id) {
    var idx = cart.findIndex(function(item) { return item.id === id; });
    if (idx === -1) return;
    if (cart[idx].qty > 1) cart[idx].qty--;
    else cart.splice(idx, 1);
    updateCartUI();
  }

  function updateCartUI() {
    if (window.IS_ADMIN) return; // Hide UI for admin
    
    var bar = $('cart-bar');
    var countEl = $('cart-count');
    var totalEl = $('cart-total');
    var previewEl = $('cart-preview');
    var checkoutBtn = $('btn-checkout');
    
    if (!bar || !countEl || !totalEl) return;
    
    var count = getCartCount();
    var total = getCartTotal();
    
    countEl.textContent = count;
    totalEl.textContent = formatPrice(total);
    if (previewEl) {
      previewEl.textContent = cart.slice(0, 2).map(function(i) { return i.name; }).join(', ') + (cart.length > 2 ? ' +...' : '');
    }
    
    if (count > 0) {
      bar.classList.add('active');
      if (checkoutBtn) checkoutBtn.disabled = false;
    } else {
      bar.classList.remove('active');
      if (checkoutBtn) checkoutBtn.disabled = true;
    }
  }

  function placeOrder() {
    if (window.IS_ADMIN) { alert('Admins cannot place orders.'); return; }
    if (cart.length === 0) return;
    
    var btn = $('btn-checkout');
    if (!btn) return;
    
    btn.disabled = true; btn.textContent = 'Processing...';
    
    var payload = {
      action: 'place_order',
      items: cart.map(function(i) { return { id: i.id, qty: i.qty }; }),
      notes: ''
    };
    
    fetch(ORDER_ENDPOINT, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
      credentials: 'same-origin'
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (data.success) {
        showOrderSuccess(data);
        cart = [];
        updateCartUI();
      } else { alert(data.message || 'Failed to place order'); }
    })
    .catch(function(err) { alert('Network error. Please try again.'); })
    .finally(function() { btn.disabled = false; btn.textContent = 'Place Order'; });
  }

  function showOrderSuccess(orderData) {
    var modal = $('order-modal');
    var summary = $('modal-summary');
    if (!modal || !summary) return;
    
    var html = '';
    cart.forEach(function(item) {
      html += '<div class="modal-order-row"><span>' + item.name + ' × ' + item.qty + '</span><span>' + formatPrice(item.price * item.qty) + '</span></div>';
    });
    html += '<div class="modal-order-row"><span><strong>Total</strong></span><span><strong>' + formatPrice(orderData.total) + '</strong></span></div>';
    
    summary.innerHTML = html;
    $('modal-title').textContent = 'Order Placed!';
    $('modal-message').textContent = 'Order #' + orderData.order_id + ' is being prepared.';
    modal.classList.add('active');
  }

  window.closeModal = function() {
    var modal = $('order-modal');
    if (modal) modal.classList.remove('active');
  };

  function loadMenu() {
    var grid = $('menu-grid');
    if (grid) grid.innerHTML = '<div class="menu-loading">Loading menu</div>';
    
    fetch(API_ENDPOINT)
      .then(function(res) { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
      .then(function(data) {
        if (data && data.success && Array.isArray(data.data)) {
          renderMenu(data.data);
          setupSearch();
          // If admin, ensure cart is hidden immediately after render
          if (window.IS_ADMIN) {
            var bar = $('cart-bar');
            if (bar) bar.style.display = 'none';
          }
        } else { throw new Error(data.message || 'Invalid response'); }
      })
      .catch(function(err) {
        console.error('Menu load error:', err);
        if (grid) grid.innerHTML = '<div class="menu-empty"><div class="menu-empty-icon">⚠️</div><p>Error loading menu</p></div>';
      });
  }

  // Init
  function init() {
    var checkoutBtn = $('btn-checkout');
    if (checkoutBtn && !window.IS_ADMIN) checkoutBtn.addEventListener('click', placeOrder);
    
    loadMenu();
    updateCartUI();
    
    if (window.IS_ADMIN) {
      console.log('Admin detected: Ordering disabled.');
      var bar = $('cart-bar');
      if (bar) bar.style.display = 'none';
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else { init(); }
})();
