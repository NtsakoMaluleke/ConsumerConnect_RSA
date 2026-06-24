<?php
require_once '../auth.php';
requireRole('/', 'super_admin', 'admin', 'moderator');
$adminUser = currentUser();
$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $oid = (int)($_POST['order_id'] ?? 0);
    $ns  = $_POST['new_status'] ?? '';
    $allowed = ['pending','paid','shipped','delivered','cancelled','refunded'];
    if ($oid && in_array($ns, $allowed)) {
        $db->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$ns,$oid]);
        auditLog('order_status_changed','orders',$oid,"New status: $ns");
        flash('success',"Order #$oid status updated to $ns.");
    }
    header('Location: orders.php'); exit;
}

$statusF = trim($_GET['status'] ?? '');
$search  = trim($_GET['search'] ?? '');
$page = max(1,(int)($_GET['page'] ?? 1));
$perPage = 15; $offset = ($page-1)*$perPage;
$where = ['1=1']; $params = [];
if ($statusF) { $where[] = 'o.status=?'; $params[] = $statusF; }
if ($search)  { $where[] = '(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
$whereSQL = 'WHERE '.implode(' AND ',$where);
$cstmt = $db->prepare("SELECT COUNT(*) FROM orders o JOIN users u ON o.buyer_id=u.id $whereSQL");
$cstmt->execute($params); $total = (int)$cstmt->fetchColumn();
$pages = max(1,ceil($total/$perPage));
$stmt = $db->prepare("SELECT o.*, u.first_name, u.last_name, u.email FROM orders o JOIN users u ON o.buyer_id=u.id $whereSQL ORDER BY o.created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$orders = $stmt->fetchAll();
$statuses = ['pending','paid','shipped','delivered','cancelled','refunded'];
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Orders — Admin | ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/admin.css">
</head><body>
<div class="admin-layout">
<?php include __DIR__.'/sidebar.php'; ?>
<div class="main-wrap">
  <header class="topbar"><span class="topbar-title">Orders</span><div class="topbar-search"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" placeholder="Search…" id="globalSearch"></div><div class="topbar-actions"><a href="../index.php" class="icon-btn"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a></div></header>
  <div class="page-content">
    <?php foreach(getFlashes() as $f): ?><div class="alert alert-<?= h($f['type']) ?>"><?= h($f['message']) ?></div><?php endforeach; ?>
    <div class="page-header"><div><div class="page-title">All Orders</div><div class="page-subtitle"><?= number_format($total) ?> orders</div></div></div>
    <form method="GET" style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
      <input type="text" name="search" class="form-control" style="max-width:260px" placeholder="Search buyer…" value="<?= h($search) ?>">
      <select name="status" class="form-control" style="width:auto;">
        <option value="">All Statuses</option>
        <?php foreach($statuses as $s): ?><option value="<?= $s ?>" <?= $statusF===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-outline">Filter</button>
      <a href="orders.php" class="btn btn-outline">Clear</a>
    </form>
    <div class="table-card"><table class="data-table"><thead><tr><th>#ID</th><th>Buyer</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th><th>Update Status</th></tr></thead>
    <tbody>
      <?php foreach($orders as $o): ?>
      <tr>
        <td style="font-family:var(--mono);color:var(--text-muted)">#<?= str_pad($o['id'],5,'0',STR_PAD_LEFT) ?></td>
        <td><div style="font-weight:600"><?= h($o['first_name'].' '.$o['last_name']) ?></div><div style="font-size:.75rem;color:var(--text-muted)"><?= h($o['email']) ?></div></td>
        <td style="font-family:var(--mono);font-weight:600">R <?= number_format($o['total_amount'],2) ?></td>
        <td style="color:var(--text-muted);font-size:.82rem"><?= h($o['payment_method']??'eft') ?></td>
        <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
        <td style="color:var(--text-muted);font-size:.8rem"><?= date('d M Y',strtotime($o['created_at'])) ?></td>
        <td>
          <form method="POST" style="display:flex;gap:6px;align-items:center"><?= csrfField() ?>
            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
            <select name="new_status" class="form-control" style="padding:4px 8px;font-size:.78rem;width:auto;">
              <?php foreach($statuses as $s): ?><option value="<?= $s ?>" <?= $o['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-outline btn-xs">Save</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($orders)): ?><tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px">No orders found.</td></tr><?php endif; ?>
    </tbody></table></div>
    <?php if($pages>1): ?><div style="display:flex;gap:6px;justify-content:center;margin-top:20px"><?php $qs=$_GET;unset($qs['page']);$base='orders.php?'.http_build_query($qs); for($i=1;$i<=$pages;$i++): ?><a href="<?= $base ?>&page=<?= $i ?>" class="btn <?= $i===$page?'btn-primary':'btn-outline' ?> btn-sm"><?= $i ?></a><?php endfor; ?></div><?php endif; ?>
  </div>
</div></div>
<script src="js/admin.js"></script></body></html>
