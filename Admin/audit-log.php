<?php
require_once '../auth.php';
requireRole('/', 'super_admin', 'admin');
$adminUser = currentUser();
$db = db();
$page = max(1,(int)($_GET['page'] ?? 1));
$perPage = 20; $offset = ($page-1)*$perPage;
$total = (int)$db->query('SELECT COUNT(*) FROM audit_log')->fetchColumn();
$pages = max(1,ceil($total/$perPage));
$logs = $db->query("SELECT al.*, u.first_name, u.last_name FROM audit_log al LEFT JOIN users u ON al.user_id=u.id ORDER BY al.created_at DESC LIMIT $perPage OFFSET $offset")->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Audit Log — Admin | ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/admin.css">
</head><body>
<div class="admin-layout">
<?php include __DIR__.'/sidebar.php'; ?>
<div class="main-wrap">
  <header class="topbar"><span class="topbar-title">Audit Log</span><div class="topbar-search"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" placeholder="Search…" id="globalSearch"></div><div class="topbar-actions"><a href="../index.php" class="icon-btn"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a></div></header>
  <div class="page-content">
    <div class="page-header"><div><div class="page-title">Audit Log</div><div class="page-subtitle"><?= number_format($total) ?> entries — immutable activity record</div></div></div>
    <div class="table-card"><table class="data-table"><thead><tr><th>#</th><th>User</th><th>Action</th><th>Target</th><th>Target ID</th><th>Details</th><th>IP Address</th><th>Timestamp</th></tr></thead>
    <tbody>
      <?php foreach($logs as $l): ?>
      <tr>
        <td style="font-family:var(--mono);color:var(--text-muted)"><?= $l['id'] ?></td>
        <td><?= $l['first_name'] ? h($l['first_name'].' '.$l['last_name']) : '<span style="color:var(--text-muted)">System</span>' ?></td>
        <td><span class="badge" style="background:var(--accent-2);color:#fff;font-family:var(--mono);font-size:.7rem"><?= h($l['action']) ?></span></td>
        <td style="color:var(--text-muted);font-size:.82rem"><?= h($l['target']??'—') ?></td>
        <td style="font-family:var(--mono);color:var(--text-muted)"><?= $l['target_id']??'—' ?></td>
        <td style="font-size:.8rem;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= h($l['details']??'') ?></td>
        <td style="font-family:var(--mono);font-size:.78rem;color:var(--text-muted)"><?= h($l['ip_address']??'—') ?></td>
        <td style="font-size:.78rem;color:var(--text-muted)"><?= date('d M Y H:i',strtotime($l['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($logs)): ?><tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:40px">No log entries yet.</td></tr><?php endif; ?>
    </tbody></table></div>
    <?php if($pages>1): ?><div style="display:flex;gap:6px;justify-content:center;margin-top:20px"><?php for($i=1;$i<=$pages;$i++): ?><a href="?page=<?= $i ?>" class="btn <?= $i===$page?'btn-primary':'btn-outline' ?> btn-sm"><?= $i ?></a><?php endfor; ?></div><?php endif; ?>
  </div>
</div></div>
<script src="js/admin.js"></script></body></html>
