(function() {
  'use strict';

  var API_MENU = '/api/menu.php';
  var API_ORDER = '/api/order.php';
  
  // State
  var allItems = []; // Flattened list for filtering
  var categories = [];
  var cart = []; // {id, name, price, qty}

  function $(id) { return document.getElementById(id); }
  
  function formatCurrency(n) { return '$' + parseFloat(n).toFixed(2); }

  function renderFilters() {
    var bar = $('category-filters');
    bar.innerHTML = '<button class="filter-pill active" data-cat="all">All</button>';
    categories.forEach(function(cat) {
      var btn = document.createElement('button');
      btn.className = 'filter-pill';
      btn.textContent = cat;
      btn.setAttribute('data-cat', cat);
      btn.addEventListener('click', function() { filterByCategory(cat); });
      bar.appendChild(btn);
    });
    
    // Re-add listener to 'All'
    bar.querySelector('[data-cat="all"]').addEventListener('click', function() { filterByCategory('all'); });
  }

  function filterByCategory(cat) {
    var pills = document.querySelectorAll('.filter-pill');
    pills.forEach(function(p) {
      p.classList.toggle('active', p.getAttribute('data-cat') === cat);
    });
    
    var grid = $('menu-grid');
    var cards = grid.querySelectorAll('.menu-card');
    cards.forEach(function(card) {
      var cardCat = card.getAttribute('data-cat');
      card.style.display = (cat === 'all' || cardCat === cat) ? 'flex' : 'none';
    });
  }

  function renderMenu(data) {
    var grid = $('menu-grid');
    grid.innerHTML = '';
    
    allItems = [];
    categories = data.map(function(c) { return c.name; });

    data.forEach(function(catData) {
      catData.items.forEach(function(item) {
        allItems.push(item);
        
        var card = document.createElement('div');
        card.className = 'menu-card';
        card.setAttribute('data-cat', item.category);
        
        // Card HTML
        card.innerHTML = 
          '<div class="menu-card-img">' + (item.image_path ? '🖼️' : '🍲') + '</div>' +
          '<div class="menu-card-body">' +
            '<h3 class="menu-card-title">' + item.name + '</h3>' +
            '<p class="menu-card-desc">' + (item.description || '') + '</p>' +
            '<div class="menu-card-footer">' +
              '<span class="menu-card-price">' + formatCurrency(item.price) + '</span>' +
              '<button class="btn-add" data-id="' + item.id + '" data-name="' + item.name + '" data-price="' + item.price + '">Add +</button>' +
            '</div>' +
          '</div>';
          
        grid.appendChild(card);
      });
    });

    // Attach Add listeners
    grid.querySelectorAll('.btn-add').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var id = parseInt(btn.getAttribute('data-id'));
        var name = btn.getAttribute('data-name');
        var price = parseFloat(btn.getAttribute('data-price'));
        addToCart(id, name, price);
      });
    });

    renderFilters();
  }

  // Cart Logic
  function addToCart(id, name, price) {
    var existing = cart.find(function(i) { return i.id === id; });
    if (existing) {
      existing.qty++;
    } else {
      cart.push({ id: id, name: name, price: price, qty: 1 });
    }
    updateCartUI();
  }

  function removeFromCart(id) {
    var idx = cart.findIndex(function(i) { return i.id === id; });
    if (idx !== -1) {
      if (cart[idx].qty > 1) {
        cart[idx].qty--;
      } else {
        cart.splice(idx, 1);
      }
    }
    updateCartUI();
  }

  function updateCartUI() {
    var bar = $('cart-bar');
    var countEl = $('cart-count');
    var totalEl = $('cart-total');
    
    var totalCount = cart.reduce(function(sum, item) { return sum + item.qty; }, 0);
    var totalCost = cart.reduce(function(sum, item) { return sum + (item.price * item.qty); }, 0);
    
    countEl.textContent = totalCount;
    totalEl.textContent = formatCurrency(totalCost);
    
    if (totalCount > 0) {
      bar.classList.add('active');
    } else {
      bar.classList.remove('active');
    }
  }

  function placeOrder() {
    var btn = $('btn-checkout');
    if (!confirm('Place this order?')) return;
    
    btn.disabled = true;
    btn.textContent = 'Processing...';
    
    var payload = {
      action: 'place_order',
      items: cart.map(function(i) { return { id: i.id, qty: i.qty }; }),
      notes: '' // Add notes textarea if needed
    };
    
    fetch(API_ORDER, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (data.success) {
        cart = [];
        updateCartUI();
        showModal(true);
      } else {
        alert(data.message || 'Failed');
      }
    })
    .catch(function(err) {
      console.error(err);
      alert('Network error');
    })
    .finally(function() {
      btn.disabled = false;
      btn.textContent = 'Place Order';
    });
  }

  function showModal(success) {
    $('order-modal').classList.add('active');
  }
  window.closeModal = function() { $('order-modal').classList.remove('active'); };

  // Init
  $('btn-checkout').addEventListener('click', placeOrder);
  
  fetch(API_MENU)
    .then(function(r) { return r.json(); })
    .then(function(data) { if (data.success) renderMenu(data.data); })
    .catch(function(e) { $('menu-grid').innerHTML = '<p>Error loading menu</p>'; });

})();
