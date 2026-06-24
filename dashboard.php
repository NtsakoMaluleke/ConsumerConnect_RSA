<?php
require_once 'auth.php';
requireLogin();
$user = currentUser();
$db   = db();

$totalOrders = $db->prepare('SELECT COUNT(*) FROM orders WHERE buyer_id=?');
$totalOrders->execute([$user['id']]); $totalOrders = $totalOrders->fetchColumn();

$totalSpent = $db->prepare('SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE buyer_id=? AND status!="cancelled"');
$totalSpent->execute([$user['id']]); $totalSpent = $totalSpent->fetchColumn();

$activeOrders = $db->prepare('SELECT COUNT(*) FROM orders WHERE buyer_id=? AND status IN ("pending","paid","shipped")');
$activeOrders->execute([$user['id']]); $activeOrders = $activeOrders->fetchColumn();

$myListings = $db->prepare('SELECT COUNT(*) FROM products WHERE seller_id=? AND status="active"');
$myListings->execute([$user['id']]); $myListings = $myListings->fetchColumn();

$recentOrders = $db->prepare('SELECT o.*,COUNT(oi.id) AS item_count FROM orders o LEFT JOIN order_items oi ON oi.order_id=o.id WHERE o.buyer_id=? GROUP BY o.id ORDER BY o.created_at DESC LIMIT 5');
$recentOrders->execute([$user['id']]); $recentOrders = $recentOrders->fetchAll();

$recentListings = $db->prepare('SELECT p.*,c.name AS cat_name,(SELECT filename FROM product_images WHERE product_id=p.id AND is_primary=1 LIMIT 1) AS img FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.seller_id=? ORDER BY p.created_at DESC LIMIT 4');
$recentListings->execute([$user['id']]); $recentListings = $recentListings->fetchAll();

$statusColors=['pending'=>'#e9a824','paid'=>'#2980b9','shipped'=>'#8e44ad','delivered'=>'#27ae60','cancelled'=>'#c0392b','refunded'=>'#7f8c8d'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
<style>
.dash-hero{background:linear-gradient(135deg,var(--green-deep),var(--green-mid));color:#fff;padding:32px 0 28px}
.dash-hero h1{font-size:clamp(1.3rem,4vw,1.9rem);margin-bottom:4px}
.dash-hero p{opacity:.75;font-size:.88rem}
.stats-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin:20px 0}
@media(min-width:640px){.stats-grid{grid-template-columns:repeat(4,1fr)}}
.stat-card{background:var(--white);border-radius:var(--radius-lg);padding:18px;box-shadow:var(--shadow-sm);text-align:center}
.stat-icon{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-size:1.2rem}
.stat-num{font-family:'Playfair Display',serif;font-size:1.7rem;font-weight:900;color:var(--green-mid);line-height:1}
.stat-label{font-size:.72rem;color:var(--grey-400);margin-top:4px;font-weight:600;text-transform:uppercase;letter-spacing:.05em}
.quick-actions{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:24px}
@media(min-width:480px){.quick-actions{grid-template-columns:repeat(4,1fr)}}
.qa-btn{display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 10px;background:var(--white);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);color:var(--grey-700);font-size:.78rem;font-weight:600;text-decoration:none;transition:var(--transition);text-align:center}
.qa-btn:hover{background:var(--green-mid);color:#fff;transform:translateY(-2px);box-shadow:var(--shadow-md)}
.qa-icon{font-size:1.5rem}
.dash-grid{display:grid;grid-template-columns:1fr;gap:20px;margin-bottom:40px}
@media(min-width:860px){.dash-grid{grid-template-columns:1fr 1fr}}
.dash-card{background:var(--white);border-radius:var(--radius-lg);padding:20px;box-shadow:var(--shadow-sm)}
.dash-card h3{font-size:.95rem;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between}
.dash-card h3 a{font-size:.78rem;font-weight:500;color:var(--green-mid)}
.order-row{display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--grey-100);font-size:.85rem;gap:8px}
.order-row:last-child{border-bottom:none}
.status-pill{display:inline-block;padding:3px 9px;border-radius:20px;font-size:.68rem;font-weight:700;color:#fff;text-transform:capitalize;flex-shrink:0}
.listing-mini-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}
.listing-mini{border-radius:var(--radius-md);overflow:hidden;border:1.5px solid var(--grey-200)}
.listing-mini img,.listing-mini-placeholder{width:100%;height:80px;object-fit:cover}
.listing-mini-placeholder{background:var(--grey-100);display:flex;align-items:center;justify-content:center;font-size:1.8rem}
.listing-mini-body{padding:7px 9px}
.listing-mini-title{font-size:.75rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.listing-mini-price{font-size:.82rem;font-weight:700;color:var(--green-mid)}
.empty-state{text-align:center;padding:28px 12px;color:var(--grey-400);font-size:.88rem}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="dash-hero">
  <div class="container">
    <h1>Welcome back, <?= h($user['first_name']) ?>!</h1>
    <p>Here's what's happening with your account today.</p>
  </div>
</div>

<div class="container">
  <div class="stats-grid">
    <div class="stat-card"><div class="stat-icon" style="background:#eafaf1">🛒</div><div class="stat-num"><?= $totalOrders ?></div><div class="stat-label">Total Orders</div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#fef9e7">💰</div><div class="stat-num">R<?= number_format($totalSpent,0) ?></div><div class="stat-label">Total Spent</div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#eaf4fb">📦</div><div class="stat-num"><?= $activeOrders ?></div><div class="stat-label">Active Orders</div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#f5eef8">🏷️</div><div class="stat-num"><?= $myListings ?></div><div class="stat-label">My Listings</div></div>
  </div>

  <div class="quick-actions">
    <a href="products.php" class="qa-btn"><span class="qa-icon">🔍</span>Browse</a>
    <a href="sell.php"     class="qa-btn"><span class="qa-icon">➕</span>Sell Item</a>
    <a href="orders.php"   class="qa-btn"><span class="qa-icon">📋</span>My Orders</a>
    <a href="profile.php"  class="qa-btn"><span class="qa-icon">⚙️</span>Settings</a>
  </div>

  <div class="dash-grid">
    <div class="dash-card">
      <h3>Recent Orders <a href="orders.php">View all →</a></h3>
      <?php if ($recentOrders): foreach ($recentOrders as $o): $c=$statusColors[$o['status']]??'#999'; ?>
      <div class="order-row">
        <div><div style="font-weight:600">#<?= $o['id'] ?></div><div style="color:var(--grey-400);font-size:.72rem"><?= date('d M Y',strtotime($o['created_at'])) ?> · <?= $o['item_count'] ?> item<?= $o['item_count']!=1?'s':'' ?></div></div>
        <div style="text-align:right"><div style="font-weight:700;color:var(--green-mid)">R <?= number_format($o['total_amount'],2) ?></div><span class="status-pill" style="background:<?= $c ?>"><?= h($o['status']) ?></span></div>
      </div>
      <?php endforeach; else: ?>
      <div class="empty-state"><div style="font-size:2.2rem;margin-bottom:8px">🛍️</div><p>No orders yet.<br><a href="products.php" style="color:var(--green-mid)">Start shopping →</a></p></div>
      <?php endif; ?>
    </div>

    <div class="dash-card">
      <h3>My Listings <a href="sell.php">+ Add new</a></h3>
      <?php if ($recentListings): ?>
      <div class="listing-mini-grid">
        <?php foreach ($recentListings as $p): ?>
        <div class="listing-mini">
          <?php if ($p['img']): ?><img src="uploads/<?= h($p['img']) ?>" alt="<?= h($p['title']) ?>">
          <?php else: ?><div class="listing-mini-placeholder">📦</div><?php endif; ?>
          <div class="listing-mini-body"><div class="listing-mini-title"><?= h($p['title']) ?></div><div class="listing-mini-price">R <?= number_format($p['price'],2) ?></div></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="empty-state"><div style="font-size:2.2rem;margin-bottom:8px">🏷️</div><p>No listings yet.<br><a href="sell.php" style="color:var(--green-mid)">List your first item →</a></p></div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
</body></html>
