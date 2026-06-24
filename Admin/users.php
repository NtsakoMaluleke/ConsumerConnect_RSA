<?php
require_once '../auth.php';
requireRole('/', 'super_admin', 'admin', 'moderator');
$adminUser = currentUser();
$db = db();
$roles = $db->query('SELECT * FROM roles ORDER BY id')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    $userId = (int)($_POST['user_id'] ?? 0);
    if ($action === 'toggle_status' && hasRole('super_admin','admin')) {
        $stmt = $db->prepare('SELECT status FROM users WHERE id=?');
        $stmt->execute([$userId]);
        $curr = $stmt->fetchColumn();
        $newStatus = $curr === 'active' ? 'suspended' : 'active';
        $db->prepare('UPDATE users SET status=? WHERE id=?')->execute([$newStatus,$userId]);
        auditLog("user_status_$newStatus",'users',$userId);
        flash('success',"User status updated to $newStatus.");
    } elseif ($action === 'delete' && hasRole('super_admin')) {
        if ($userId !== (int)$adminUser['id']) {
            $db->prepare('DELETE FROM users WHERE id=?')->execute([$userId]);
            auditLog('user_deleted','users',$userId);
            flash('success','User deleted successfully.');
        } else {
            flash('danger','You cannot delete your own account.');
        }
    } elseif ($action === 'change_role' && hasRole('super_admin')) {
        $newRoleId = (int)$_POST['role_id'];
        $db->prepare('UPDATE users SET role_id=? WHERE id=?')->execute([$newRoleId,$userId]);
        auditLog('user_role_changed','users',$userId,"New role_id: $newRoleId");
        flash('success','User role updated.');
    }
    header('Location: users.php'); exit;
}

$search = trim($_GET['search'] ?? '');
$roleFilter = trim($_GET['role'] ?? '');
$status = trim($_GET['status'] ?? '');
$page = max(1,(int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page-1)*$perPage;
$where = ['1=1']; $params = [];
if ($search) { $where[] = '(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($roleFilter) { $where[] = 'r.name = ?'; $params[] = $roleFilter; }
if ($status) { $where[] = 'u.status = ?'; $params[] = $status; }
$whereSQL = 'WHERE '.implode(' AND ',$where);
$countStmt = $db->prepare("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id=r.id $whereSQL");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pages = max(1,ceil($total/$perPage));
$stmt = $db->prepare("SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON u.role_id=r.id $whereSQL ORDER BY u.created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Users — Admin | ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/admin.css">
</head><body>
<div class="admin-layout">
<?php include __DIR__.'/sidebar.php'; ?>
<div class="main-wrap">
  <header class="topbar">
    <span class="topbar-title">User Management</span>
    <div class="topbar-search"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" placeholder="Search…" id="globalSearch"></div>
    <div class="topbar-actions"><a href="../index.php" class="icon-btn" title="View Site"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a></div>
  </header>
  <div class="page-content">
    <?php foreach(getFlashes() as $f): ?><div class="alert alert-<?= h($f['type']) ?>"><?= h($f['message']) ?></div><?php endforeach; ?>
    <div class="page-header">
      <div><div class="page-title">All Users</div><div class="page-subtitle"><?= number_format($total) ?> total users</div></div>
      <?php if(hasRole('super_admin','admin')): ?><a href="user-create.php" class="btn btn-primary">+ Create User</a><?php endif; ?>
    </div>
    <form method="GET" style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
      <input type="text" name="search" class="form-control" style="max-width:260px" placeholder="Search name or email…" value="<?= h($search) ?>">
      <select name="role" class="form-control" style="width:auto;">
        <option value="">All Roles</option>
        <?php foreach($roles as $r): ?><option value="<?= h($r['name']) ?>" <?= $roleFilter===$r['name']?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$r['name'])) ?></option><?php endforeach; ?>
      </select>
      <select name="status" class="form-control" style="width:auto;">
        <option value="">All Statuses</option>
        <option value="active" <?= $status==='active'?'selected':'' ?>>Active</option>
        <option value="suspended" <?= $status==='suspended'?'selected':'' ?>>Suspended</option>
        <option value="pending" <?= $status==='pending'?'selected':'' ?>>Pending</option>
      </select>
      <button type="submit" class="btn btn-outline">Filter</button>
      <a href="users.php" class="btn btn-outline">Clear</a>
    </form>
    <div class="table-card">
      <table class="data-table"><thead><tr><th>#</th><th>User</th><th>Role</th><th>Location</th><th>Status</th><th>Registered</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach($users as $u): ?>
        <tr>
          <td style="font-family:var(--mono);color:var(--text-muted)"><?= $u['id'] ?></td>
          <td><div style="display:flex;align-items:center;gap:10px"><div style="width:36px;height:36px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0"><?= strtoupper(substr($u['first_name'],0,1)) ?></div><div><div style="font-weight:600"><?= h($u['first_name'].' '.$u['last_name']) ?></div><div style="font-size:.75rem;color:var(--text-muted)"><?= h($u['email']) ?></div></div></div></td>
          <td>
            <?php if(hasRole('super_admin') && $u['id']!==(int)$adminUser['id']): ?>
            <form method="POST" style="display:inline"><?= csrfField() ?><input type="hidden" name="action" value="change_role"><input type="hidden" name="user_id" value="<?= $u['id'] ?>">
            <select name="role_id" class="form-control" style="padding:4px 8px;font-size:.78rem;width:auto;" onchange="this.form.submit()">
              <?php foreach($roles as $r): ?><option value="<?= $r['id'] ?>" <?= $u['role_id']==$r['id']?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$r['name'])) ?></option><?php endforeach; ?>
            </select></form>
            <?php else: ?><span class="badge badge-role-<?= $u['role_name'] ?>"><?= ucfirst(str_replace('_',' ',$u['role_name'])) ?></span><?php endif; ?>
          </td>
          <td style="color:var(--text-muted);font-size:.82rem"><?= h(($u['city']?$u['city'].', ':'').($u['province']??'—')) ?></td>
          <td><span class="badge badge-<?= $u['status'] ?>"><?= ucfirst($u['status']) ?></span></td>
          <td style="color:var(--text-muted);font-size:.8rem"><?= date('d M Y',strtotime($u['created_at'])) ?></td>
          <td><div class="actions">
            <a href="user-edit.php?id=<?= $u['id'] ?>" class="btn btn-outline btn-xs">Edit</a>
            <?php if($u['id']!==(int)$adminUser['id'] && hasRole('super_admin','admin')): ?>
            <form method="POST" style="display:inline"><?= csrfField() ?><input type="hidden" name="action" value="toggle_status"><input type="hidden" name="user_id" value="<?= $u['id'] ?>"><button type="submit" class="btn btn-warning btn-xs"><?= $u['status']==='active'?'Suspend':'Activate' ?></button></form>
            <?php endif; ?>
            <?php if($u['id']!==(int)$adminUser['id'] && hasRole('super_admin')): ?>
            <form method="POST" style="display:inline" onsubmit="return confirm('Permanently delete this user and all their data?')"><?= csrfField() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="user_id" value="<?= $u['id'] ?>"><button type="submit" class="btn btn-danger btn-xs">Delete</button></form>
            <?php endif; ?>
          </div></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($users)): ?><tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px">No users found.</td></tr><?php endif; ?>
      </tbody></table>
    </div>
    <?php if($pages>1): ?><div style="display:flex;gap:6px;justify-content:center;margin-top:20px"><?php $qs=$_GET;unset($qs['page']);$base='users.php?'.http_build_query($qs); for($i=1;$i<=$pages;$i++): ?><a href="<?= $base ?>&page=<?= $i ?>" class="btn <?= $i===$page?'btn-primary':'btn-outline' ?> btn-sm"><?= $i ?></a><?php endfor; ?></div><?php endif; ?>
  </div>
</div></div>
<script src="js/admin.js"></script></body></html>
