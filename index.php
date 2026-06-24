<?php
require_once 'auth.php';
$user = currentUser();

//Fetch featured products
$stmt = db()->query(
    'SELECT p.*, c.name AS cat_name, u.city, u.province,
            (SELECT filename FROM product_images WHERE product_id=p.id AND is_primary=1 LIMIT 1) AS img
     FROM products p
     JOIN categories c ON p.category_id = c.id
     JOIN users u ON p.seller_id = u.id
     WHERE p.status="active"
     ORDER BY p.created_at DESC LIMIT 8'
);
$products = $stmt->fetchAll();

// Fetch categories
$cats = db()->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();

// Site stats
$stats = [
    'users'    => db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'products' => db()->query('SELECT COUNT(*) FROM products WHERE status="active"')->fetchColumn(),
    'orders'   => db()->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ConsumerConnect RSA — Buy &amp; Sell Across South Africa</title>
<link rel="stylesheet" href="css/style.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🛒</text></svg>">
</head>
<body>

<!-- ── Navbar ───────────────────────────── -->
<nav class="navbar">
  <div class="container navbar-inner">
    <a href="index.php" class="navbar-brand">
      ConsumerConnect<span class="brand-dot">.</span>RSA
    </a>

    <div class="navbar-links" id="navLinks">
      <a href="index.php" class="active">Home</a>
      <a href="products.php">Browse</a>
      <a href="sell.php">Sell</a>
      <a href="about.php">About</a>
    </div>

    <div class="navbar-actions">
      <a href="cart.php" class="cart-badge">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span class="cart-count" id="cartCount">0</span>
      </a>

      <?php if ($user): ?>
      <div class="user-menu-wrap">
        <button class="user-avatar-btn" id="avatarBtn">
          <?= strtoupper(substr($user['first_name'],0,1)) ?>
        </button>
        <div class="dropdown-menu" id="userDropdown">
          <a href="dashboard.php">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Dashboard
          </a>
          <a href="orders.php">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
            My Orders
          </a>
          <?php if (hasRole('seller','super_admin','admin')): ?>
          <a href="seller/dashboard.php">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            Seller Hub
          </a>
          <?php endif; ?>
          <?php if (hasRole('super_admin','admin','moderator')): ?>
          <a href="../admin/index.php">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
            Admin Panel
          </a>
          <?php endif; ?>
          <div class="dropdown-divider"></div>
          <a href="profile.php">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Profile
          </a>
          <form method="POST" action="logout.php">
            <?= csrfField() ?>
            <button type="submit">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Sign Out
            </button>
          </form>
        </div>
      </div>
      <?php else: ?>
      <a href="login.php" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,.4);">Login</a>
      <a href="register.php" class="btn btn-gold">Register</a>
      <?php endif; ?>

      <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>

<!-- ── Hero ─────────────────────────────── -->
<section class="hero">
  <div class="container">
    <div class="hero-content">
      <div class="hero-badge">
        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
        South Africa's Marketplace
      </div>
      <h1>Buy &amp; Sell Anything<br>Across South Africa</h1>
      <p>From Cape Town to Johannesburg — discover great deals or reach millions of buyers on ConsumerConnect RSA.</p>

      <form class="hero-search" action="products.php" method="GET">
        <input type="text" name="q" placeholder="Search for products, brands, categories…" autocomplete="off">
        <select name="category">
          <option value="">All Categories</option>
          <?php foreach ($cats as $cat): ?>
          <option value="<?= h($cat['slug']) ?>"><?= h($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit">Search</button>
      </form>

      <div class="hero-stats">
        <div class="hero-stat">
          <strong><?= number_format($stats['users']) ?>+</strong>
          <span>Registered users</span>
        </div>
        <div class="hero-stat">
          <strong><?= number_format($stats['products']) ?>+</strong>
          <span>Active listings</span>
        </div>
        <div class="hero-stat">
          <strong><?= number_format($stats['orders']) ?>+</strong>
          <span>Orders completed</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── Categories ───────────────────────── -->
<div class="categories-strip">
  <div class="container">
    <div class="categories-row">
      <?php
      $icons = [
        'electronics'    => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
        'clothing'       => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.38 3.46L16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.57a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.57a2 2 0 0 0-1.34-2.23z"/></svg>',
        'home-garden'    => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
        'vehicles'       => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
        'food-groceries' => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
        'sports-leisure' => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M4.93 4.93l4.24 4.24m5.66 5.66l4.24 4.24M4.93 19.07l4.24-4.24m5.66-5.66l4.24-4.24"/></svg>',
        'books-education'=> '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
        'services'       => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
      ];
      foreach ($cats as $cat): ?>
      <a href="products.php?category=<?= h($cat['slug']) ?>" class="cat-pill">
        <?= $icons[$cat['slug']] ?? '' ?>
        <span><?= h($cat['name']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ── Flash Messages ───────────────────── -->
<?php foreach (getFlashes() as $f): ?>
<div class="container" style="padding-top:16px">
  <div class="alert alert-<?= h($f['type']) ?>"><?= h($f['message']) ?></div>
</div>
<?php endforeach; ?>

<!-- ── Featured Products ────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <div>
        <div class="eyebrow">Freshly Listed</div>
        <h2>Latest Products</h2>
      </div>
      <a href="products.php">View all →</a>
    </div>

    <div class="product-grid">
      <?php if (empty($products)): ?>
      <div style="grid-column:1/-1;text-align:center;color:var(--grey-400);padding:60px 0">
        <p>No products listed yet. <a href="sell.php" style="color:var(--green-mid)">Be the first to sell!</a></p>
      </div>
      <?php endif; ?>

      <?php foreach ($products as $p): ?>
      <div class="product-card">
        <div class="product-img-wrap">
          <?php if ($p['img']): ?>
          <img src="uploads/<?= h($p['img']) ?>" alt="<?= h($p['title']) ?>" loading="lazy">
          <?php else: ?>
          <div style="width:100%;height:100%;background:var(--grey-100);display:flex;align-items:center;justify-content:center;color:var(--grey-400)">
            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          </div>
          <?php endif; ?>
          <span class="product-badge <?= $p['condition_type'] === 'used' ? 'used' : '' ?>"><?= ucfirst(h($p['condition_type'])) ?></span>
          <button class="wishlist-btn" onclick="toggleWishlist(this,<?= $p['id'] ?>)" aria-label="Wishlist">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </button>
        </div>
        <div class="product-body">
          <div class="product-cat"><?= h($p['cat_name']) ?></div>
          <div class="product-title"><?= h($p['title']) ?></div>
          <div class="product-location">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <?= h($p['city'] . ', ' . $p['province']) ?>
          </div>
          <div class="product-footer">
            <div class="product-price"><?= CURRENCY ?> <?= number_format($p['price'], 2) ?></div>
            <button class="add-cart-btn" onclick="addToCart(<?= $p['id'] ?>)">
              <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
              Add
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Sell CTA ──────────────────────────── -->
<section class="section-sm">
  <div class="container">
    <div class="sell-banner">
      <div>
        <h2>Start Selling Today</h2>
        <p>List your items for free and reach buyers across all 9 provinces. Join thousands of South African sellers.</p>
      </div>
      <a href="<?= $user ? 'sell.php' : 'register.php' ?>" class="btn btn-primary btn-lg" style="flex-shrink:0">
        <?= $user ? 'List a Product' : 'Create Free Account' ?> →
      </a>
    </div>
  </div>
</section>

<!-- ── Footer ───────────────────────────── -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-brand">ConsumerConnect<span class="dot">.</span>RSA</div>
        <p class="footer-desc">South Africa's trusted platform for buying and selling goods across all provinces. Safe, simple, and local.</p>
      </div>
      <div>
        <div class="footer-heading">Marketplace</div>
        <div class="footer-links">
          <a href="products.php">Browse Products</a>
          <a href="sell.php">Sell an Item</a>
          <a href="categories.php">All Categories</a>
          <a href="deals.php">Today's Deals</a>
        </div>
      </div>
      <div>
        <div class="footer-heading">Support</div>
        <div class="footer-links">
          <a href="help.php">Help Centre</a>
          <a href="contact.php">Contact Us</a>
          <a href="safety.php">Safety Tips</a>
          <a href="disputes.php">Disputes</a>
        </div>
      </div>
      <div>
        <div class="footer-heading">Legal</div>
        <div class="footer-links">
          <a href="terms.php">Terms of Service</a>
          <a href="privacy.php">Privacy Policy</a>
          <a href="popia.php">POPIA Compliance</a>
          <a href="cookies.php">Cookie Policy</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> ConsumerConnect RSA. All rights reserved.</span>
      <div class="footer-flags">🇿🇦</div>
    </div>
  </div>
</footer>

<!-- ── Toast Container ───────────────────── -->
<div class="toast-container" id="toastContainer"></div>

<script src="js/main.js"></script>
</body>
</html>
