<?php
require_once 'auth.php';
requireLogin();
$user = currentUser();
$db   = db();

$status  = $_GET['status'] ?? '';
$page    = max(1,(int)($_GET['page'] ?? 1));
$perPage = 10;
$offset  = ($page-1)*$perPage;

$where  = 'WHERE o.buyer_id=?';
$params = [$user['id']];
if ($status) { $where .= ' AND o.status=?'; $params[] = $status; }

$total = $db->prepare("SELECT COUNT(*) FROM orders o $where");
$total->execute($params); $total = $total->fetchColumn();
$pages = max(1,ceil($total/$perPage));

$orders = $db->prepare("SELECT o.*,COUNT(oi.id) AS item_count FROM orders o LEFT JOIN order_items oi ON oi.order_id=o.id $where GROUP BY o.id ORDER BY o.created_at DESC LIMIT $perPage OFFSET $offset");
$orders->execute($params); $orders = $orders->fetchAll();

$statuses = ['pending','paid','shipped','delivered','cancelled','refunded'];
$statusColors=['pending'=>'#e9a824','paid'=>'#2980b9','shipped'=>'#8e44ad','delivered'=>'#27ae60','cancelled'=>'#c0392b','refunded'=>'#7f8c8d'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Orders — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
<style>
.page-header{background:var(--green-deep);color:#fff;padding:28px 0}
.page-header h1{font-size:clamp(1.2rem,4vw,1.7rem)}
.filter-tabs{display:flex;gap:8px;overflow-x:auto;padding:18px 0 4px;scrollbar-width:none}
.filter-tabs::-webkit-scrollbar{display:none}
.ftab{padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:600;cursor:pointer;white-space:nowrap;border:2px solid var(--grey-200);background:var(--white);color:var(--grey-700);text-decoration:none;transition:var(--transition)}
.ftab:hover,.ftab.active{background:var(--green-mid);border-color:var(--green-mid);color:#fff}
.order-card{background:var(--white);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);margin-bottom:12px;overflow:hidden}
.order-card-head{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1.5px solid var(--grey-100);gap:8px;flex-wrap:wrap}
.order-id{font-weight:700;font-size:.9rem}
.order-date{font-size:.75rem;color:var(--grey-400)}
.status-pill{display:inline-block;padding:4px 11px;border-radius:20px;font-size:.7rem;font-weight:700;color:#fff;text-transform:capitalize}
.order-card-body{padding:12px 16px}
.order-imgs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:10px}
.order-img{width:48px;height:44px;object-fit:cover;border-radius:6px;background:var(--grey-100)}
.order-img-ph{width:48px;height:44px;border-radius:6px;background:var(--grey-100);display:flex;align-items:center;justify-content:center;font-size:1.2rem}
.order-meta{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
.order-total{font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:var(--green-mid)}
.empty-orders{text-align:center;padding:60px 16px;color:var(--grey-400)}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page-header">
  <div class="container">
    <div style="font-size:.78rem;opacity:.6;margin-bottom:6px"><a href="dashboard.php" style="color:rgba(255,255,255,.7)">Dashboard</a> › Orders</div>
    <h1>My Orders</h1>
  </div>
</div>

<div class="container">
  <div class="filter-tabs">
    <a href="orders.php" class="ftab <?= !$status?'active':'' ?>">All (<?= $total ?>)</a>
    <?php foreach ($statuses as $s):
      $cnt=$db->prepare("SELECT COUNT(*) FROM orders WHERE buyer_id=? AND status=?");
      $cnt->execute([$user['id'],$s]); $cnt=$cnt->fetchColumn();
      if (!$cnt) continue;
    ?><a href="orders.php?status=<?= $s ?>" class="ftab <?= $status===$s?'active':'' ?>"><?= ucfirst($s) ?> (<?= $cnt ?>)</a><?php endforeach; ?>
  </div>

  <div style="margin-bottom:40px">
    <?php if ($orders): foreach ($orders as $o):
      $color=$statusColors[$o['status']]??'#999';
      $items=$db->prepare("SELECT oi.*,p.title,(SELECT filename FROM product_images WHERE product_id=p.id AND is_primary=1 LIMIT 1) AS img FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?");
      $items->execute([$o['id']]); $items=$items->fetchAll();
    ?>
    <div class="order-card">
      <div class="order-card-head">
        <div><div class="order-id">Order #<?= $o['id'] ?></div><div class="order-date"><?= date('d F Y, H:i',strtotime($o['created_at'])) ?></div></div>
        <span class="status-pill" style="background:<?= $color ?>"><?= h($o['status']) ?></span>
      </div>
      <div class="order-card-body">
        <?php if ($items): ?>
        <div class="order-imgs">
          <?php foreach ($items as $item): ?>
          <?php if ($item['img']): ?><img class="order-img" src="uploads/<?= h($item['img']) ?>" alt="<?= h($item['title']) ?>" title="<?= h($item['title']) ?>">
          <?php else: ?><div class="order-img-ph">📦</div><?php endif; ?>
          <?php endforeach; ?>
        </div>
        <div style="font-size:.8rem;color:var(--grey-400);margin-bottom:10px"><?= implode(', ',array_map(fn($i)=>h($i['title']),$items)) ?></div>
        <?php endif; ?>
        <div class="order-meta">
          <div><div class="order-total">R <?= number_format($o['total_amount'],2) ?></div><div style="font-size:.72rem;color:var(--grey-400)"><?= $o['item_count'] ?> item<?= $o['item_count']!=1?'s':'' ?> · <?= ucfirst($o['payment_method']??'eft') ?></div></div>
          <div style="font-size:.78rem;font-weight:600">
            <?php if ($o['status']==='pending'): ?><span style="color:var(--gold)">⏳ Awaiting payment</span>
            <?php elseif ($o['status']==='shipped'): ?><span style="color:#8e44ad">🚚 In transit</span>
            <?php elseif ($o['status']==='delivered'): ?><span style="color:var(--success)">✅ Delivered</span>
            <?php elseif ($o['status']==='cancelled'): ?><span style="color:var(--danger)">❌ Cancelled</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; else: ?>
    <div class="empty-orders">
      <div style="font-size:3rem;margin-bottom:14px">📭</div>
      <h3 style="margin-bottom:8px">No orders found</h3>
      <p><?= $status?'No '.h($status).' orders.':"You haven't placed any orders yet." ?></p>
      <a href="products.php" class="btn btn-primary" style="margin-top:20px">Browse Products</a>
    </div>
    <?php endif; ?>
  </div>

  <?php if ($pages>1): ?>
  <div class="pagination">
    <?php for ($i=1;$i<=$pages;$i++): ?>
    <a href="?page=<?= $i ?><?= $status?'&status='.$status:'' ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
</body></html>
