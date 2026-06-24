<?php
require_once 'auth.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Your Cart — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
<style>
.cart-wrap { max-width: 860px; margin: 48px auto; }
.cart-wrap h1 { font-size: 1.8rem; margin-bottom: 32px; }
.cart-row { display: flex; align-items: center; gap: 16px; background: var(--white); border-radius: var(--radius-md); padding: 16px 20px; margin-bottom: 12px; box-shadow: var(--shadow-sm); }
.cart-summary { background: var(--white); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-sm); margin-top: 24px; }
.cart-summary h3 { margin-bottom: 20px; font-size: 1.1rem; }
.cart-total-row { display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; padding-top: 16px; border-top: 2px solid var(--grey-100); }
.empty-cart { text-align: center; padding: 80px 0; color: var(--grey-400); }
.empty-cart svg { margin: 0 auto 16px; display: block; opacity: .3; }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
  <div class="cart-wrap">
    <h1>Your Cart</h1>
    <div id="cartItems">
      <div class="empty-cart">
        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <p>Loading your cart…</p>
      </div>
    </div>
    <div class="cart-summary" id="cartSummary" style="display:none;">
      <h3>Order Summary</h3>
      <div class="cart-total-row">
        <span>Total</span>
        <span id="cartTotal">R 0.00</span>
      </div>
      <?php if ($user): ?>
      <button class="btn btn-primary" style="width:100%;justify-content:center;padding:14px;" onclick="showToast('Checkout coming soon!','info')">Proceed to Checkout →</button>
      <?php else: ?>
      <a href="login.php" class="btn btn-primary" style="width:100%;justify-content:center;padding:14px;display:flex;">Login to Checkout →</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
<script>
// Override renderCartPage to show/hide summary
document.addEventListener('DOMContentLoaded', () => {
  const cart = getCart();
  const ids  = Object.keys(cart);
  const summary = document.getElementById('cartSummary');
  if (!ids.length) {
    document.getElementById('cartItems').innerHTML = `
      <div class="empty-cart">
        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <p style="margin-bottom:16px;">Your cart is empty.</p>
        <a href="products.php" class="btn btn-primary">Browse Products</a>
      </div>`;
    if (summary) summary.style.display = 'none';
    return;
  }
  if (summary) summary.style.display = 'block';
  renderCartPage();
});
</script>
</body>
</html>
