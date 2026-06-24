<?php
require_once '../auth.php';
requireRole('/', 'super_admin', 'admin', 'moderator');
$adminUser = currentUser();
$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    $rid = (int)($_POST['review_id'] ?? 0);
    if ($action === 'delete' && $rid) {
        $db->prepare('DELETE FROM reviews WHERE id=?')->execute([$rid]);
        flash('success','Review removed.');
    }
    header('Location: reviews.php'); exit;
}

$page = max(1,(int)($_GET['page'] ?? 1));
$perPage = 20; $offset = ($page-1)*$perPage;
$total = (int)$db->query('SELECT COUNT(*) FROM reviews')->fetchColumn();
$pages = max(1,ceil($total/$perPage));
$reviews = $db->query("SELECT r.*, p.title AS product_title, u.first_name, u.last_name FROM reviews r JOIN products p ON r.product_id=p.id JOIN users u ON r.reviewer_id=u.id ORDER BY r.created_at DESC LIMIT $perPage OFFSET $offset")->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reviews — Admin | ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/admin.css">
</head><body>
<div class="admin-layout">
<?php include __DIR__.'/sidebar.php'; ?>
<div class="main-wrap">
  <header class="topbar"><span class="topbar-title">Reviews</span><div class="topbar-search"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" placeholder="Search…" id="globalSearch"></div><div class="topbar-actions"><a href="../index.php" class="icon-btn"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a></div></header>
  <div class="page-content">
    <?php foreach(getFlashes() as $f): ?><div class="alert alert-<?= h($f['type']) ?>"><?= h($f['message']) ?></div><?php endforeach; ?>
    <div class="page-header"><div><div class="page-title">Product Reviews</div><div class="page-subtitle"><?= number_format($total) ?> reviews</div></div></div>
    <div class="table-card"><table class="data-table"><thead><tr><th>#</th><th>Reviewer</th><th>Product</th><th>Rating</th><th>Comment</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($reviews as $r): ?>
      <tr>
        <td style="font-family:var(--mono);color:var(--text-muted)"><?= $r['id'] ?></td>
        <td style="font-weight:600"><?= h($r['first_name'].' '.$r['last_name']) ?></td>
        <td style="color:var(--text-muted);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= h($r['product_title']) ?></td>
        <td><span style="color:#e9a824;font-size:1rem"><?= str_repeat('★',$r['rating']) ?><?= str_repeat('☆',5-$r['rating']) ?></span></td>
        <td style="font-size:.82rem;max-width:200px"><?= h(substr($r['comment']??'',0,80)).(strlen($r['comment']??'')>80?'…':'') ?></td>
        <td style="color:var(--text-muted);font-size:.8rem"><?= date('d M Y',strtotime($r['created_at'])) ?></td>
        <td><form method="POST" style="display:inline" onsubmit="return confirm('Remove this review?')"><?= csrfField() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="review_id" value="<?= $r['id'] ?>"><button type="submit" class="btn btn-danger btn-xs">Remove</button></form></td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($reviews)): ?><tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px">No reviews yet.</td></tr><?php endif; ?>
    </tbody></table></div>
    <?php if($pages>1): ?><div style="display:flex;gap:6px;justify-content:center;margin-top:20px"><?php for($i=1;$i<=$pages;$i++): ?><a href="?page=<?= $i ?>" class="btn <?= $i===$page?'btn-primary':'btn-outline' ?> btn-sm"><?= $i ?></a><?php endfor; ?></div><?php endif; ?>
  </div>
</div></div>
<script src="js/admin.js"></script></body></html>
