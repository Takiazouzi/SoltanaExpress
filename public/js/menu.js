(function() {
  'use strict';

  var API_ENDPOINT = '/api/menu.php';
  var ORDER_ENDPOINT = '/api/order.php';
  
  // State
  var cart = [];
  var allItems = []; // Flattened list for search/filter

  // DOM Helpers
  function $(id) { return document.getElementById(id); }
  function $$(sel) { return document.querySelectorAll(sel); }

  // Utilities
  function formatPrice(price) {
    return '$' + parseFloat(price).toFixed(2);
  }

  function getCartTotal() {
    return cart.reduce(function(sum, item) {
      return sum + (item.price * item.qty);
    }, 0);
  }

  function getCartCount() {
    return cart.reduce(function(sum, item) {
      return sum + item.qty;
    }, 0);
  }

  // Render Category Filter Pills
  function renderCategoryFilters(categories) {
    var filterBar = $('category-filter');
    if (!filterBar) return;
    
    // Keep "All" button
    var allBtn = filterBar.querySelector('[data-category="all"]');
    filterBar.innerHTML = '';
    if (allBtn) filterBar.appendChild(allBtn);
    
    categories.forEach(function(cat) {
      var btn = document.createElement('button');
      btn.className = 'category-pill';
      btn.textContent = cat;
      btn.setAttribute('data-category', cat);
      btn.addEventListener('click', function() {
        // Update active state
        $$('.category-pill').forEach(function(b) { b.classList.remove('active'); });
        btn.classList.add('active');
        // Filter items
        filterItemsByCategory(cat);
      });
      filterBar.appendChild(btn);
    });
  }

  // Filter items by category (client-side)
  function filterItemsByCategory(category) {
    var cards = $$('#menu-grid .menu-card');
    cards.forEach(function(card) {
      var itemCat = card.getAttribute('data-category');
      if (category === 'all' || itemCat === category) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    });
    
    // Show/hide empty state
    var visible = Array.from(cards).filter(function(c) { return c.style.display !== 'none'; });
    $('menu-empty').classList.toggle('hidden', visible.length > 0);
  }

  // Search filter
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
      
      // Show empty state if no matches
      $('menu-empty').classList.toggle('hidden', visibleCount > 0 || term === '');
    });
  }

  // Render Menu Cards
  function renderMenu(data) {
    var grid = $('menu-grid');
    if (!grid) return;
    
    grid.innerHTML = '';
    allItems = [];
    
    // Extract categories for filter pills
    var categories = data.map(function(cat) { return cat.name; });
    renderCategoryFilters(categories);
    
    // Render items
    data.forEach(function(category) {
      category.items.forEach(function(item) {
        allItems.push(item);
        
        var card = document.createElement('div');
        card.className = 'menu-card';
        card.setAttribute('data-category', item.category);
        card.setAttribute('data-name', item.name);
        card.setAttribute('data-desc', item.description || '');
        
        // Card HTML - using string concatenation for safety
        card.innerHTML = 
          '<div class="menu-card-img">🍽️</div>' +
          '<div class="menu-card-body">' +
            '<h3 class="menu-card-title">' + item.name + '</h3>' +
            '<p class="menu-card-desc">' + (item.description || '') + '</p>' +
            '<div class="menu-card-footer">' +
              '<span class="menu-card-price">' + formatPrice(item.price) + '</span>' +
              '<button class="btn-add" data-id="' + item.id + '" data-name="' + item.name + '" data-price="' + item.price + '">Add +</button>' +
            '</div>' +
          '</div>';
        
        grid.appendChild(card);
      });
    });
    
    // Attach "Add to Cart" listeners
    $$('#menu-grid .btn-add').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var id = parseInt(this.getAttribute('data-id'));
        var name = this.getAttribute('data-name');
        var price = parseFloat(this.getAttribute('data-price'));
        addToCart(id, name, price);
      });
    });
  }

  // Cart Functions
  function addToCart(id, name, price) {
    var existing = cart.find(function(item) { return item.id === id; });
    if (existing) {
      existing.qty++;
    } else {
      cart.push({ id: id, name: name, price: price, qty: 1 });
    }
    updateCartUI();
  }

  function removeFromCart(id) {
    var idx = cart.findIndex(function(item) { return item.id === id; });
    if (idx === -1) return;
    
    if (cart[idx].qty > 1) {
      cart[idx].qty--;
    } else {
      cart.splice(idx, 1);
    }
    updateCartUI();
  }

  function updateCartUI() {
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
    
    // Preview text
    if (previewEl) {
      var names = cart.slice(0, 2).map(function(i) { return i.name; });
      previewEl.textContent = names.join(', ') + (cart.length > 2 ? ' +...' : '');
    }
    
    // Show/hide bar
    if (count > 0) {
      bar.classList.add('active');
      if (checkoutBtn) checkoutBtn.disabled = false;
    } else {
      bar.classList.remove('active');
      if (checkoutBtn) checkoutBtn.disabled = true;
    }
  }

  // Place Order
  function placeOrder() {
    if (cart.length === 0) return;
    
    var btn = $('btn-checkout');
    if (!btn) return;
    
    btn.disabled = true;
    btn.textContent = 'Processing...';
    
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
      } else {
        alert(data.message || 'Failed to place order');
      }
    })
    .catch(function(err) {
      console.error('Order error:', err);
      alert('Network error. Please try again.');
    })
    .finally(function() {
      btn.disabled = false;
      btn.textContent = 'Place Order';
    });
  }

  // Show Success Modal
  function showOrderSuccess(orderData) {
    var modal = $('order-modal');
    var summary = $('modal-summary');
    if (!modal || !summary) {
      alert('Order #' + orderData.order_id + ' placed! Total: ' + formatPrice(orderData.total));
      return;
    }
    
    // Build summary HTML
    var html = '';
    cart.forEach(function(item) {
      html += '<div class="modal-order-row">' +
        '<span>' + item.name + ' × ' + item.qty + '</span>' +
        '<span>' + formatPrice(item.price * item.qty) + '</span>' +
        '</div>';
    });
    html += '<div class="modal-order-row"><span><strong>Total</strong></span><span><strong>' + formatPrice(orderData.total) + '</strong></span></div>';
    
    summary.innerHTML = html;
    $('modal-title').textContent = 'Order Placed!';
    $('modal-message').textContent = 'Order #' + orderData.order_id + ' is being prepared.';
    
    modal.classList.add('active');
  }

  // Close Modal
  window.closeModal = function() {
    var modal = $('order-modal');
    if (modal) modal.classList.remove('active');
  };

  // Load Menu from API
  function loadMenu() {
    var grid = $('menu-grid');
    if (grid) grid.innerHTML = '<div class="menu-loading">Loading menu</div>';
    
    fetch(API_ENDPOINT)
      .then(function(res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      })
      .then(function(data) {
        if (data && data.success && Array.isArray(data.data)) {
          renderMenu(data.data);
          setupSearch();
        } else {
          throw new Error(data.message || 'Invalid response');
        }
      })
      .catch(function(err) {
        console.error('Menu load error:', err);
        if (grid) {
          grid.innerHTML = '<div class="menu-empty"><div class="menu-empty-icon">⚠️</div><p>Error loading menu</p><p class="text-hint">' + err.message + '</p></div>';
        }
      });
  }

  // Init
  function init() {
    // Cart bar checkout button
    var checkoutBtn = $('btn-checkout');
    if (checkoutBtn) {
      checkoutBtn.addEventListener('click', placeOrder);
    }
    
    // Load menu
    loadMenu();
    
    // Initial cart UI
    updateCartUI();
    
    console.log('Menu JS initialized');
  }

  // Start when DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
