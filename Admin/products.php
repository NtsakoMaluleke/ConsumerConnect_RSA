<?php
require_once '../auth.php';
requireRole('/', 'super_admin', 'admin', 'moderator');
$adminUser = currentUser();
$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    $pid = (int)($_POST['product_id'] ?? 0);
    if ($action === 'approve') {
        $db->prepare('UPDATE products SET status="active" WHERE id=?')->execute([$pid]);
        auditLog('product_approved','products',$pid);
        flash('success','Product approved and now live.');
    } elseif ($action === 'reject') {
        $db->prepare('UPDATE products SET status="rejected" WHERE id=?')->execute([$pid]);
        auditLog('product_rejected','products',$pid);
        flash('warning','Product rejected.');
    } elseif ($action === 'delete') {
        $db->prepare('DELETE FROM products WHERE id=?')->execute([$pid]);
        auditLog('product_deleted','products',$pid);
        flash('success','Product deleted.');
    } elseif ($action === 'set_status') {
        $ns = $_POST['new_status'] ?? 'active';
        $allowed = ['active','pending','sold','rejected'];
        if (in_array($ns, $allowed)) {
            $db->prepare('UPDATE products SET status=? WHERE id=?')->execute([$ns,$pid]);
            flash('success','Product status updated.');
        }
    }
    header('Location: products.php'); exit;
}

$search = trim($_GET['search'] ?? '');
$statusF = trim($_GET['status'] ?? '');
$page = max(1,(int)($_GET['page'] ?? 1));
$perPage = 15; $offset = ($page-1)*$perPage;
$where = ['1=1']; $params = [];
if ($search) { $where[] = '(p.title LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($statusF) { $where[] = 'p.status = ?'; $params[] = $statusF; }
$whereSQL = 'WHERE '.implode(' AND ',$where);
$cstmt = $db->prepare("SELECT COUNT(*) FROM products p JOIN users u ON p.seller_id=u.id $whereSQL");
$cstmt->execute($params); $total = (int)$cstmt->fetchColumn();
$pages = max(1,ceil($total/$perPage));
$stmt = $db->prepare("SELECT p.*, u.first_name, u.last_name, c.name AS cat_name FROM products p JOIN users u ON p.seller_id=u.id LEFT JOIN categories c ON p.category_id=c.id $whereSQL ORDER BY p.created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Products — Admin | ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/admin.css">
</head><body>
<div class="admin-layout">
<?php include __DIR__.'/sidebar.php'; ?>
<div class="main-wrap">
  <header class="topbar"><span class="topbar-title">Products</span><div class="topbar-search"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" placeholder="Search…" id="globalSearch"></div><div class="topbar-actions"><a href="../index.php" class="icon-btn"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a></div></header>
  <div class="page-content">
    <?php foreach(getFlashes() as $f): ?><div class="alert alert-<?= h($f['type']) ?>"><?= h($f['message']) ?></div><?php endforeach; ?>
    <div class="page-header"><div><div class="page-title">All Products</div><div class="page-subtitle"><?= number_format($total) ?> listings</div></div></div>
    <form method="GET" style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
      <input type="text" name="search" class="form-control" style="max-width:260px" placeholder="Search title or seller…" value="<?= h($search) ?>">
      <select name="status" class="form-control" style="width:auto;">
        <option value="">All Statuses</option>
        <?php foreach(['active','pending','sold','rejected'] as $s): ?><option value="<?= $s ?>" <?= $statusF===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-outline">Filter</button>
      <a href="products.php" class="btn btn-outline">Clear</a>
    </form>
    <div class="table-card"><table class="data-table"><thead><tr><th>#</th><th>Product</th><th>Seller</th><th>Category</th><th>Price</th><th>Condition</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($products as $p): ?>
      <tr>
        <td style="font-family:var(--mono);color:var(--text-muted)"><?= $p['id'] ?></td>
        <td style="font-weight:600;max-width:200px"><?= h($p['title']) ?></td>
        <td style="color:var(--text-muted)"><?= h($p['first_name'].' '.$p['last_name']) ?></td>
        <td style="color:var(--text-muted)"><?= h($p['cat_name']??'—') ?></td>
        <td style="font-family:var(--mono);font-weight:600">R <?= number_format($p['price'],2) ?></td>
        <td><span class="badge"><?= ucfirst($p['condition_type']) ?></span></td>
        <td><span class="badge badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
        <td style="color:var(--text-muted);font-size:.8rem"><?= date('d M Y',strtotime($p['created_at'])) ?></td>
        <td><div class="actions">
          <?php if($p['status']==='pending'): ?>
          <form method="POST" style="display:inline"><?= csrfField() ?><input type="hidden" name="action" value="approve"><input type="hidden" name="product_id" value="<?= $p['id'] ?>"><button type="submit" class="btn btn-success btn-xs">Approve</button></form>
          <form method="POST" style="display:inline"><?= csrfField() ?><input type="hidden" name="action" value="reject"><input type="hidden" name="product_id" value="<?= $p['id'] ?>"><button type="submit" class="btn btn-warning btn-xs">Reject</button></form>
          <?php endif; ?>
          <form method="POST" style="display:inline" onsubmit="return confirm('Delete this product permanently?')"><?= csrfField() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="product_id" value="<?= $p['id'] ?>"><button type="submit" class="btn btn-danger btn-xs">Delete</button></form>
        </div></td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($products)): ?><tr><td colspan="9" style="text-align:center;color:var(--text-muted);padding:40px">No products found.</td></tr><?php endif; ?>
    </tbody></table></div>
    <?php if($pages>1): ?><div style="display:flex;gap:6px;justify-content:center;margin-top:20px"><?php $qs=$_GET;unset($qs['page']);$base='products.php?'.http_build_query($qs); for($i=1;$i<=$pages;$i++): ?><a href="<?= $base ?>&page=<?= $i ?>" class="btn <?= $i===$page?'btn-primary':'btn-outline' ?> btn-sm"><?= $i ?></a><?php endfor; ?></div><?php endif; ?>
  </div>
</div></div>
<script src="js/admin.js"></script></body></html>
