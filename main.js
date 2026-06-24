/* ============================================================
   ConsumerConnect RSA — Public JS
   ============================================================ */

'use strict';

// ── Navbar ────────────────────────────────────────────────────
const hamburger    = document.getElementById('hamburger');
const mobileNav    = document.getElementById('mobileNav');
const avatarBtn    = document.getElementById('avatarBtn');
const userDropdown = document.getElementById('userDropdown');

hamburger?.addEventListener('click', () => {
  mobileNav?.classList.toggle('open');
  const spans = hamburger.querySelectorAll('span');
  if (mobileNav?.classList.contains('open')) {
    spans[0].style.transform = 'rotate(45deg) translate(5px,5px)';
    spans[1].style.opacity = '0';
    spans[2].style.transform = 'rotate(-45deg) translate(5px,-5px)';
  } else {
    spans.forEach(s => { s.style.transform=''; s.style.opacity=''; });
  }
});

avatarBtn?.addEventListener('click', e => {
  e.stopPropagation();
  userDropdown?.classList.toggle('open');
});

document.addEventListener('click', () => userDropdown?.classList.remove('open'));

// ── Toast ──────────────────────────────────────────────────────
function showToast(message, type = 'success') {
  const container = document.getElementById('toastContainer');
  if (!container) return;
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      ${type === 'success'
        ? '<polyline points="20 6 9 17 4 12"/>'
        : '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'}
    </svg>
    ${message}
  `;
  container.appendChild(toast);
  setTimeout(() => toast.remove(), 3500);
}

// ── Cart ───────────────────────────────────────────────────────
function getCart() {
  try { return JSON.parse(localStorage.getItem('cc_cart') || '{}'); } catch { return {}; }
}
function saveCart(cart) {
  localStorage.setItem('cc_cart', JSON.stringify(cart));
  updateCartBadge();
}
function updateCartBadge() {
  const cart  = getCart();
  const count = Object.values(cart).reduce((a, b) => a + b, 0);
  document.querySelectorAll('#cartCount').forEach(el => {
    el.textContent = count;
    el.style.display = count > 0 ? 'flex' : 'none';
  });
}
function addToCart(productId, qty = 1) {
  const cart = getCart();
  cart[productId] = (cart[productId] || 0) + qty;
  saveCart(cart);
  showToast('Added to cart!', 'success');
}
function removeFromCart(productId) {
  const cart = getCart();
  delete cart[productId];
  saveCart(cart);
}

// ── Wishlist (localStorage) ───────────────────────────────────
function getWishlist() {
  try { return JSON.parse(localStorage.getItem('cc_wishlist') || '[]'); } catch { return []; }
}
function toggleWishlist(btn, productId) {
  let wl = getWishlist();
  if (wl.includes(productId)) {
    wl = wl.filter(id => id !== productId);
    btn.classList.remove('active');
    showToast('Removed from wishlist', 'success');
  } else {
    wl.push(productId);
    btn.classList.add('active');
    showToast('Added to wishlist ♥', 'success');
  }
  localStorage.setItem('cc_wishlist', JSON.stringify(wl));
}
function syncWishlistUI() {
  const wl = getWishlist();
  document.querySelectorAll('.wishlist-btn[onclick]').forEach(btn => {
    const match = btn.getAttribute('onclick').match(/\d+/);
    if (match && wl.includes(parseInt(match[0]))) btn.classList.add('active');
  });
}

// ── Cart page render ──────────────────────────────────────────
function renderCartPage() {
  const cartEl = document.getElementById('cartItems');
  if (!cartEl) return;

  const cart = getCart();
  const ids  = Object.keys(cart);
  if (!ids.length) {
    cartEl.innerHTML = '<p style="color:var(--grey-400);text-align:center;padding:40px">Your cart is empty. <a href="products.php" style="color:var(--green-mid)">Browse products</a></p>';
    document.getElementById('cartTotal').textContent = 'R 0.00';
    return;
  }

  fetch('api/cart-items.php?ids=' + ids.join(','))
    .then(r => r.json())
    .then(products => {
      let html  = '';
      let total = 0;
      products.forEach(p => {
        const qty   = cart[p.id] || 1;
        const sub   = parseFloat(p.price) * qty;
        total += sub;
        html += `
          <div class="cart-row" data-id="${p.id}">
            <img src="${p.img ? 'uploads/'+p.img : 'images/placeholder.png'}" alt="${p.title}" style="width:80px;height:70px;object-fit:cover;border-radius:8px;">
            <div style="flex:1">
              <div style="font-weight:600">${p.title}</div>
              <div style="color:var(--grey-400);font-size:.85rem">${p.cat_name}</div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
              <button onclick="changeQty(${p.id},-1)" style="background:var(--grey-100);border:none;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:1rem;">−</button>
              <span id="qty-${p.id}">${qty}</span>
              <button onclick="changeQty(${p.id},1)" style="background:var(--grey-100);border:none;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:1rem;">+</button>
            </div>
            <div style="font-weight:700;color:var(--green-mid);min-width:90px;text-align:right">R ${sub.toFixed(2)}</div>
            <button onclick="removeFromCart(${p.id});renderCartPage()" style="background:none;border:none;color:var(--danger);cursor:pointer;">✕</button>
          </div>`;
      });
      cartEl.innerHTML = html;
      document.getElementById('cartTotal').textContent = 'R ' + total.toFixed(2);
    });
}
function changeQty(id, delta) {
  const cart = getCart();
  cart[id] = Math.max(1, (cart[id] || 1) + delta);
  saveCart(cart);
  renderCartPage();
}

// ── Init ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  updateCartBadge();
  syncWishlistUI();
  renderCartPage();
});
