<?php
require_once '../auth.php';
requireRole('/', 'super_admin');
$adminUser = currentUser();
$db = db();
$roles = $db->query('SELECT r.*, COUNT(u.id) AS user_count FROM roles r LEFT JOIN users u ON u.role_id=r.id GROUP BY r.id ORDER BY r.id')->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Roles — Admin | ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/admin.css">
</head><body>
<div class="admin-layout">
<?php include __DIR__.'/sidebar.php'; ?>
<div class="main-wrap">
  <header class="topbar"><span class="topbar-title">Roles & RBAC</span><div class="topbar-search"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" placeholder="Search…" id="globalSearch"></div><div class="topbar-actions"><a href="../index.php" class="icon-btn"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a></div></header>
  <div class="page-content">
    <div class="page-header"><div><div class="page-title">Roles & Permissions</div><div class="page-subtitle">Role-Based Access Control overview</div></div></div>
    <div class="table-card"><table class="data-table"><thead><tr><th>#</th><th>Role</th><th>Description</th><th>Users</th><th>Permissions</th></tr></thead>
    <tbody>
      <?php
      $perms = [
        'super_admin' => 'Full platform access — users, products, orders, settings, roles',
        'admin'       => 'Manage users, products, orders. No role/settings changes.',
        'moderator'   => 'Review and approve/reject products. View users and orders (read-only).',
        'seller'      => 'Create and manage own product listings. All buyer capabilities.',
        'buyer'       => 'Browse products, add to cart, place orders, leave reviews, message sellers.',
      ];
      foreach($roles as $r):
      ?>
      <tr>
        <td style="font-family:var(--mono);color:var(--text-muted)"><?= $r['id'] ?></td>
        <td><span class="badge badge-role-<?= $r['name'] ?>"><?= ucfirst(str_replace('_',' ',$r['name'])) ?></span></td>
        <td style="color:var(--text-muted);font-size:.85rem"><?= h($r['description']??'') ?></td>
        <td><a href="users.php?role=<?= h($r['name']) ?>" class="btn btn-outline btn-xs"><?= $r['user_count'] ?> users</a></td>
        <td style="font-size:.82rem;color:var(--text-muted)"><?= h($perms[$r['name']] ?? '—') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody></table></div>
    <div class="table-card" style="padding:20px;margin-top:20px">
      <div style="font-weight:700;margin-bottom:12px">How to Change a User's Role</div>
      <p style="color:var(--text-muted);font-size:.9rem">Go to <a href="users.php" style="color:var(--accent-2)">All Users</a>, find the user, and use the role dropdown in their row. Changes take effect immediately on their next page load. Only Super Admins can change roles.</p>
    </div>
  </div>
</div></div>
<script src="js/admin.js"></script></body></html>
