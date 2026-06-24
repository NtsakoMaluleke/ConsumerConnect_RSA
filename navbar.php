<?php
// navbar.php
if (!isset($user)) $user = currentUser();
?>
<nav class="navbar">
  <div class="container navbar-inner">
    <a href="<?= SITE_URL ?>/index.php" class="navbar-brand">
      ConsumerConnect<span class="brand-dot">.</span>RSA
    </a>

    <!-- Desktop links -->
    <div class="navbar-links" id="navLinks">
      <a href="<?= SITE_URL ?>/index.php">Home</a>
      <a href="<?= SITE_URL ?>/products.php">Browse</a>
      <a href="<?= SITE_URL ?>/sell.php">Sell</a>
      <a href="<?= SITE_URL ?>/about.php">About</a>
    </div>

    <div class="navbar-actions">
      <a href="<?= SITE_URL ?>/cart.php" class="cart-badge">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span class="cart-count" id="cartCount">0</span>
      </a>

      <?php if ($user): ?>
      <div class="user-menu-wrap">
        <button class="user-avatar-btn" id="avatarBtn">
          <?= strtoupper(substr($user['first_name'],0,1)) ?>
        </button>
        <div class="dropdown-menu" id="userDropdown">
          <a href="<?= SITE_URL ?>/dashboard.php">My Dashboard</a>
          <a href="<?= SITE_URL ?>/orders.php">My Orders</a>
          <a href="<?= SITE_URL ?>/profile.php">Profile Settings</a>
          <a href="<?= SITE_URL ?>/sell.php">List a Product</a>
          <div class="dropdown-divider"></div>
          <?php if (isset($user['role_name']) && in_array($user['role_name'], ['super_admin','admin','moderator'])): ?>
          <a href="<?= SITE_URL ?>/Admin/index.php">Admin Panel</a>
          <div class="dropdown-divider"></div>
          <?php endif; ?>
          <form method="POST" action="<?= SITE_URL ?>/logout.php">
            <?php if (function_exists('csrfField')) echo csrfField(); ?>
            <button type="submit">Sign Out</button>
          </form>
        </div>
      </div>
      <?php else: ?>
      <a href="<?= SITE_URL ?>/login.php" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,.4);">Login</a>
      <a href="<?= SITE_URL ?>/register.php" class="btn btn-gold btn-sm">Register</a>
      <?php endif; ?>

      <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>

  <!-- Mobile slide-down nav -->
  <div class="mobile-nav" id="mobileNav">
    <a href="<?= SITE_URL ?>/index.php">🏠 Home</a>
    <a href="<?= SITE_URL ?>/products.php">🔍 Browse Products</a>
    <a href="<?= SITE_URL ?>/sell.php">💰 Sell an Item</a>
    <a href="<?= SITE_URL ?>/about.php">ℹ️ About Us</a>
    <a href="<?= SITE_URL ?>/cart.php">🛒 Cart</a>
    <?php if (!$user): ?>
    <div class="mobile-nav-actions">
      <a href="<?= SITE_URL ?>/login.php" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,.4);">Login</a>
      <a href="<?= SITE_URL ?>/register.php" class="btn btn-gold">Register</a>
    </div>
    <?php else: ?>
    <a href="<?= SITE_URL ?>/dashboard.php">📊 My Dashboard</a>
    <a href="<?= SITE_URL ?>/orders.php">📋 My Orders</a>
    <a href="<?= SITE_URL ?>/profile.php">⚙️ Profile Settings</a>
    <a href="<?= SITE_URL ?>/sell.php">📦 My Listings</a>
    <?php if (isset($user['role']) && in_array($user['role'], ['super_admin','admin','moderator'])): ?>
    <a href="<?= SITE_URL ?>/Admin/index.php">⚙️ Admin Panel</a>
    <?php endif; ?>
    <form method="POST" action="<?= SITE_URL ?>/logout.php" style="padding:12px 8px;border-bottom:1px solid rgba(255,255,255,.07)">
      <?php if (function_exists('csrfField')) echo csrfField(); ?>
      <button type="submit" style="background:var(--danger);color:#fff;border:none;padding:10px 20px;border-radius:8px;font-weight:600;cursor:pointer;width:100%;">Sign Out</button>
    </form>
    <?php endif; ?>
  </div>
</nav>
