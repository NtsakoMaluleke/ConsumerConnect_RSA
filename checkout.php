<?php
require_once 'auth.php';
$user = currentUser();
if (!$user) { header('Location: ' . SITE_URL . '/login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Checkout — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="processing" id="processingOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;flex-direction:column;gap:16px;color:#fff;font-size:1rem;font-weight:600;">
  <div style="width:44px;height:44px;border:4px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite;"></div>
  <span id="processingMsg">Processing payment…</span>
  <style>@keyframes spin{to{transform:rotate(360deg)}}</style>
</div>

<div class="container">
  <div class="checkout-wrap" id="checkoutWrap">
    <div>
      <!-- Delivery -->
      <div class="checkout-card" style="margin-bottom:18px;">
        <h2>Delivery Details</h2>
        <div class="form-row-2">
          <div class="form-group"><label class="form-label">First Name</label><input class="form-control" type="text" id="firstName" value="<?= h($user['first_name'] ?? '') ?>" placeholder="Thabo"></div>
          <div class="form-group"><label class="form-label">Last Name</label><input class="form-control" type="text" id="lastName" value="<?= h($user['last_name'] ?? '') ?>" placeholder="Nkosi"></div>
        </div>
        <div class="form-group"><label class="form-label">Street Address</label><input class="form-control" type="text" id="address" placeholder="12 Mandela Street"></div>
        <div class="form-row-2">
          <div class="form-group"><label class="form-label">City</label><input class="form-control" type="text" id="city" placeholder="Johannesburg"></div>
          <div class="form-group"><label class="form-label">Province</label>
            <select class="form-control" id="province"><option value="">Select…</option><?php foreach(['Eastern Cape','Free State','Gauteng','KwaZulu-Natal','Limpopo','Mpumalanga','Northern Cape','North West','Western Cape'] as $p): ?><option><?= $p ?></option><?php endforeach; ?></select>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Postal Code</label><input class="form-control" type="text" id="postal" placeholder="2000" maxlength="4"></div>
      </div>

      <!-- Payment -->
      <div class="checkout-card">
        <h2>Payment Method</h2>
        <div class="pay-tabs">
          <button class="pay-tab active" onclick="switchTab('card',this)">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>Card
          </button>
          <button class="pay-tab" onclick="switchTab('eft',this)">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>EFT
          </button>
          <button class="pay-tab" onclick="switchTab('payflex',this)">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>Pay Later
          </button>
        </div>

        <div class="pay-panel active" id="panel-card">
          <div class="alert alert-warning" style="font-size:.82rem;">🔒 Demo mode — use any card details. No real charge will be made.</div>
          <div class="form-group"><label class="form-label">Cardholder Name</label><input class="form-control" type="text" id="cardName" placeholder="Name on card"></div>
          <div class="form-group"><label class="form-label">Card Number</label><input class="form-control" type="text" id="cardNumber" placeholder="4242 4242 4242 4242" maxlength="19" oninput="formatCard(this)"></div>
          <div class="form-row-2">
            <div class="form-group"><label class="form-label">Expiry</label><input class="form-control" type="text" id="cardExpiry" placeholder="MM / YY" maxlength="7" oninput="formatExpiry(this)"></div>
            <div class="form-group"><label class="form-label">CVV</label><input class="form-control" type="text" id="cardCvv" placeholder="123" maxlength="4"></div>
          </div>
        </div>

        <div class="pay-panel" id="panel-eft">
          <div style="background:var(--grey-100);border-radius:var(--radius-md);padding:16px;font-size:.88rem;line-height:2.1;">
            <div><strong>Bank:</strong> FNB</div>
            <div><strong>Account Name:</strong> ConsumerConnect RSA (Pty) Ltd</div>
            <div><strong>Account No:</strong> 6285 7741 002</div>
            <div><strong>Branch Code:</strong> 250 655</div>
            <div><strong>Reference:</strong> CC-<?= $user['id'] ?>-<?= date('dmy') ?></div>
          </div>
          <p style="font-size:.8rem;color:var(--grey-400);margin-top:10px;">Your order will be confirmed once payment reflects (1–2 business days).</p>
        </div>

        <div class="pay-panel" id="panel-payflex">
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:14px;">
            <?php foreach(['Today','Week 2','Week 4','Week 6'] as $w): ?>
            <div style="background:var(--grey-100);border-radius:8px;padding:10px 6px;text-align:center;font-size:.75rem;"><strong style="display:block;font-size:1rem;color:var(--green-mid);">25%</strong><?= $w ?></div>
            <?php endforeach; ?>
          </div>
          <div class="alert alert-success" style="font-size:.83rem;">✅ Split into 4 equal payments — <strong id="pflexAmount">R0.00</strong> due today. Interest free.</div>
          <p style="font-size:.78rem;color:var(--grey-400);">Demo mode — no real payment processed.</p>
        </div>

        <button onclick="placeOrder()" class="btn btn-primary btn-block" style="margin-top:20px;font-size:1rem;">Place Order →</button>
      </div>
    </div>

    <!-- Order Summary -->
    <div>
      <div class="checkout-card" style="position:sticky;top:80px;">
        <h2>Order Summary</h2>
        <div id="summaryItems"><p style="color:var(--grey-400);font-size:.9rem">Loading…</p></div>
        <div style="margin-top:14px;">
          <div style="display:flex;justify-content:space-between;font-size:.88rem;color:var(--grey-400);margin-bottom:6px;"><span>Subtotal</span><span id="subtotal">R 0.00</span></div>
          <div style="display:flex;justify-content:space-between;font-size:.88rem;color:var(--grey-400);margin-bottom:8px;"><span>Delivery</span><span style="color:var(--success)">Free</span></div>
          <div style="display:flex;justify-content:space-between;font-size:1.1rem;font-weight:700;padding-top:12px;border-top:2px solid var(--grey-100);"><span>Total</span><span id="orderTotal">R 0.00</span></div>
        </div>
        <div style="margin-top:16px;font-size:.78rem;color:var(--grey-400);display:flex;flex-direction:column;gap:6px;">
          <span>🔒 SSL encrypted checkout</span>
          <span>✅ Buyer protection on every order</span>
          <span>🏠 Nationwide delivery</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Success -->
  <div id="successScreen" style="display:none;text-align:center;padding:60px 16px;">
    <div style="width:72px;height:72px;background:var(--success);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
      <svg width="36" height="36" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <h2 style="margin-bottom:10px;">Order Placed!</h2>
    <p style="color:var(--grey-400);margin-bottom:20px;">Thank you! You'll receive a confirmation shortly.</p>
    <div id="orderRef" style="background:var(--grey-100);border-radius:var(--radius-md);padding:14px 20px;display:inline-block;margin-bottom:24px;font-family:monospace;font-size:1.1rem;color:var(--green-deep);font-weight:700;"></div>
    <br><a href="products.php" class="btn btn-primary">Continue Shopping</a>
  </div>
</div>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
<script>
let orderTotal = 0;
document.addEventListener('DOMContentLoaded', () => {
  const cart = getCart(), ids = Object.keys(cart);
  if (!ids.length) { window.location.href = 'cart.php'; return; }
  fetch('api/cart-items.php?ids=' + ids.join(','))
    .then(r => r.json()).then(products => {
      let html = '', total = 0;
      products.forEach(p => {
        const qty = cart[p.id] || 1, sub = parseFloat(p.price) * qty;
        total += sub;
        html += `<div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--grey-100);font-size:.88rem;">
          <span>${p.title} <span style="color:var(--grey-400)">×${qty}</span></span>
          <span style="font-weight:600">R ${sub.toFixed(2)}</span></div>`;
      });
      document.getElementById('summaryItems').innerHTML = html;
      document.getElementById('subtotal').textContent = 'R ' + total.toFixed(2);
      document.getElementById('orderTotal').textContent = 'R ' + total.toFixed(2);
      document.getElementById('pflexAmount').textContent = 'R ' + (total/4).toFixed(2);
      orderTotal = total;
    });
});
function switchTab(id, btn) {
  document.querySelectorAll('.pay-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.pay-panel').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('panel-' + id).classList.add('active');
}
function formatCard(el) { let v = el.value.replace(/\D/g,'').slice(0,16); el.value = v.replace(/(.{4})/g,'$1 ').trim(); }
function formatExpiry(el) { let v = el.value.replace(/\D/g,'').slice(0,4); if(v.length>=3) v=v.slice(0,2)+' / '+v.slice(2); el.value=v; }
function validate() {
  if (!document.getElementById('firstName').value.trim() || !document.getElementById('address').value.trim() || !document.getElementById('city').value.trim() || !document.getElementById('province').value) {
    showToast('Please fill in all delivery details.','error'); return false;
  }
  const tab = document.querySelector('.pay-tab.active').textContent.trim();
  if (tab === 'Card') {
    const num = document.getElementById('cardNumber').value.replace(/\s/g,'');
    if (!document.getElementById('cardName').value.trim() || num.length < 12 || !document.getElementById('cardExpiry').value.trim() || !document.getElementById('cardCvv').value.trim()) {
      showToast('Please fill in all card details.','error'); return false;
    }
  }
  return true;
}
function placeOrder() {
  if (!validate()) return;
  const overlay = document.getElementById('processingOverlay');
  overlay.style.display = 'flex';
  const msgs = ['Verifying payment details…','Contacting payment gateway…','Confirming your order…','Finalising…'];
  let i = 0;
  const iv = setInterval(() => { document.getElementById('processingMsg').textContent = msgs[Math.min(i++, msgs.length-1)]; }, 900);

  const cart     = getCart();
  const tab      = document.querySelector('.pay-tab.active').textContent.trim().toLowerCase();
  const method   = tab === 'card' ? 'card' : tab === 'eft' ? 'eft' : 'pay_later';
  const addr     = [
    document.getElementById('firstName').value.trim(),
    document.getElementById('lastName').value.trim(),
    document.getElementById('address').value.trim(),
    document.getElementById('city').value.trim(),
    document.getElementById('province').value
  ].filter(Boolean).join(', ');

  fetch('api/place-order.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ cart, payment_method: method, shipping_addr: addr })
  })
  .then(r => r.json())
  .then(data => {
    clearInterval(iv);
    overlay.style.display = 'none';
    if (data.error) {
      showToast('Order failed: ' + data.error, 'error');
      return;
    }
    localStorage.removeItem('cc_cart'); updateCartBadge();
    document.getElementById('orderRef').textContent = 'Order #' + data.order_id + ' — Ref: ' + data.ref;
    document.getElementById('checkoutWrap').style.display = 'none';
    document.getElementById('successScreen').style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  })
  .catch(err => {
    clearInterval(iv);
    overlay.style.display = 'none';
    showToast('Something went wrong. Please try again.', 'error');
    console.error(err);
  });
}
</script>
</body>
</html>
