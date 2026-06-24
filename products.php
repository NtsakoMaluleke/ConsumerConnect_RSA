<?php
require_once 'auth.php';
$user = currentUser();

$q        = trim($_GET['q'] ?? '');
$catSlug  = trim($_GET['category'] ?? '');
$province = trim($_GET['province'] ?? '');
$minPrice = (float)($_GET['min_price'] ?? 0);
$maxPrice = (float)($_GET['max_price'] ?? 0);
$sort     = $_GET['sort'] ?? 'newest';
$page     = max(1,(int)($_GET['page'] ?? 1));
$perPage  = 12;
$offset   = ($page - 1) * $perPage;

$provinces = ['Eastern Cape','Free State','Gauteng','KwaZulu-Natal','Limpopo','Mpumalanga','Northern Cape','North West','Western Cape'];

// Build query
$where  = ['p.status = "active"'];
$params = [];

if ($q) { $where[] = '(p.title LIKE ? OR p.description LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }

if ($catSlug) {
    $where[] = 'c.slug = ?';
    $params[] = $catSlug;
}
if ($province) { $where[] = 'u.province = ?'; $params[] = $province; }
if ($minPrice > 0) { $where[] = 'p.price >= ?'; $params[] = $minPrice; }
if ($maxPrice > 0) { $where[] = 'p.price <= ?'; $params[] = $maxPrice; }

$orderMap = [
    'newest'    => 'p.created_at DESC',
    'price_asc' => 'p.price ASC',
    'price_desc'=> 'p.price DESC',
    'popular'   => 'p.views DESC',
];
$orderBy = $orderMap[$sort] ?? 'p.created_at DESC';

$whereSQL = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$countStmt = db()->prepare(
    "SELECT COUNT(*) FROM products p
     JOIN categories c ON p.category_id=c.id
     JOIN users u ON p.seller_id=u.id
     $whereSQL"
);
$countStmt->execute($params);
$total   = (int)$countStmt->fetchColumn();
$pages   = max(1, ceil($total / $perPage));

$stmt = db()->prepare(
    "SELECT p.*, c.name AS cat_name, c.slug AS cat_slug,
            u.city, u.province,
            (SELECT filename FROM product_images WHERE product_id=p.id AND is_primary=1 LIMIT 1) AS img,
            (SELECT ROUND(AVG(rating),1) FROM reviews WHERE product_id=p.id) AS avg_rating
     FROM products p
     JOIN categories c ON p.category_id=c.id
     JOIN users u ON p.seller_id=u.id
     $whereSQL
     ORDER BY $orderBy
     LIMIT $perPage OFFSET $offset"
);
$stmt->execute($params);
$products = $stmt->fetchAll();

$cats = db()->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $q ? h("\"$q\" — ") : '' ?>Products — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
<style>
/* Horizontal filter bar */
.filter-bar {
  background: var(--white);
  border-radius: var(--radius-lg);
  padding: 16px 20px;
  box-shadow: var(--shadow-sm);
  margin-bottom: 24px;
}
.filter-bar form {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  gap: 12px;
}
.filter-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 140px;
  flex: 1;
}
.filter-item label {
  font-size: .75rem;
  font-weight: 600;
  color: var(--grey-700);
  text-transform: uppercase;
  letter-spacing: .03em;
}
.price-range-wrap {
  display: flex;
  gap: 6px;
  align-items: center;
}
.price-range-wrap input { flex: 1; min-width: 70px; }
.filter-actions {
  display: flex;
  gap: 8px;
  align-items: flex-end;
  flex-shrink: 0;
}
.results-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.results-count  { font-size:.9rem; color:var(--grey-400); }
@media(max-width:768px){
  .filter-bar form { flex-direction: column; }
  .filter-item { min-width: 100%; }
  .filter-actions { width: 100%; }
  .filter-actions .btn { flex: 1; justify-content: center; }
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container section">

  <!-- Horizontal Filter Bar -->
  <div class="filter-bar">
    <form method="GET">
      <?php if ($q): ?><input type="hidden" name="q" value="<?= h($q) ?>"><?php endif; ?>

      <div class="filter-item">
        <label>Category</label>
        <select name="category" class="form-control">
          <option value="">All Categories</option>
          <?php foreach ($cats as $c): ?>
          <option value="<?= h($c['slug']) ?>" <?= $catSlug === $c['slug'] ? 'selected' : '' ?>><?= h($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filter-item">
        <label>Province</label>
        <select name="province" class="form-control">
          <option value="">All Provinces</option>
          <?php foreach ($provinces as $prov): ?>
          <option value="<?= h($prov) ?>" <?= $province === $prov ? 'selected' : '' ?>><?= h($prov) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filter-item">
        <label>Price Range (R)</label>
        <div class="price-range-wrap">
          <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?= $minPrice ?: '' ?>" min="0">
          <span style="color:var(--grey-400);font-size:.85rem;">&#8211;</span>
          <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?= $maxPrice ?: '' ?>" min="0">
        </div>
      </div>

      <div class="filter-item">
        <label>Sort By</label>
        <select name="sort" class="form-control">
          <option value="newest"     <?= $sort==='newest'?'selected':'' ?>>Newest First</option>
          <option value="price_asc"  <?= $sort==='price_asc'?'selected':'' ?>>Price: Low to High</option>
          <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Price: High to Low</option>
          <option value="popular"    <?= $sort==='popular'?'selected':'' ?>>Most Popular</option>
        </select>
      </div>

      <div class="filter-actions">
        <button type="submit" class="btn btn-primary">Apply</button>
        <a href="products.php" class="btn btn-outline">Clear</a>
      </div>
    </form>
  </div>

  <!-- Results header -->
  <div class="results-header">
    <div>
      <h2 style="margin-bottom:4px;"><?= $q ? 'Results for "'.h($q).'"' : 'Browse Products' ?></h2>
      <div class="results-count"><?= number_format($total) ?> <?= $total === 1 ? 'product' : 'products' ?> found</div>
    </div>

  </div>

  <!-- Product Grid -->
  <main>
      <?php if (empty($products)): ?>
      <div style="text-align:center;padding:60px;color:var(--grey-400);">
        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 20px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <h3 style="margin-bottom:8px;font-family:'DM Sans',sans-serif;">No products found</h3>
        <p>Try adjusting your search or filters.</p>
      </div>
      <?php else: ?>
      <div class="product-grid">
        <?php foreach ($products as $p): ?>
        <div class="product-card">
          <div class="product-img-wrap">
            <?php if ($p['img']): ?>
            <img src="uploads/<?= h($p['img']) ?>" alt="<?= h($p['title']) ?>" class="product-img">
            <?php else: ?>
            <div style="width:100%;height:100%;background:var(--grey-100);display:flex;align-items:center;justify-content:center;color:var(--grey-400)">
              <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </div>
            <?php endif; ?>
            <span class="product-badge <?= $p['condition_type'] === 'used' ? 'used' : '' ?>"><?= ucfirst(h($p['condition_type'])) ?></span>
            <button class="wishlist-btn" onclick="toggleWishlist(this,<?= $p['id'] ?>)">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </button>
          </div>
          <div class="product-info">
            <div class="product-category"><a href="products.php?category=<?= h($p['cat_slug']) ?>"><?= h($p['cat_name']) ?></a></div>
            <?php if ($p['avg_rating']): ?>
            <div class="star-rating">
              <?php for($s=1;$s<=5;$s++): ?>
              <span><?= $s <= round($p['avg_rating']) ? '★' : '☆' ?></span>
              <?php endfor; ?>
              <span style="color:var(--grey-400);font-size:.78rem;">(<?= $p['avg_rating'] ?>)</span>
            </div>
            <?php endif; ?>
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

      <!-- Pagination -->
      <?php if ($pages > 1): ?>
      <div class="pagination">
        <?php
        $qs = $_GET; unset($qs['page']);
        $baseUrl = 'products.php?' . http_build_query($qs);
        ?>
        <?php if ($page > 1): ?>
        <a href="<?= $baseUrl ?>&page=<?= $page-1 ?>" class="page-btn">←</a>
        <?php endif; ?>
        <?php for ($i = max(1,$page-2); $i <= min($pages,$page+2); $i++): ?>
        <a href="<?= $baseUrl ?>&page=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
        <a href="<?= $baseUrl ?>&page=<?= $page+1 ?>" class="page-btn">→</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
      <?php endif; ?>
    </main>
  </div>
</div>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
</body>
</html>
