<?php
require_once '../auth.php';
requireRole('/', 'super_admin', 'admin');
$adminUser = currentUser();
$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        if ($name) {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i','-',$name));
            try { $db->prepare('INSERT INTO categories (name,slug) VALUES (?,?)')->execute([$name,$slug]); flash('success','Category added.'); }
            catch(\Exception $e) { flash('danger','Category or slug already exists.'); }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['cat_id'] ?? 0);
        $db->prepare('DELETE FROM categories WHERE id=?')->execute([$id]);
        flash('success','Category deleted.');
    }
    header('Location: categories.php'); exit;
}
$cats = $db->query('SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id ORDER BY c.name')->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Categories — Admin | ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/admin.css">
</head><body>
<div class="admin-layout">
<?php include __DIR__.'/sidebar.php'; ?>
<div class="main-wrap">
  <header class="topbar"><span class="topbar-title">Categories</span><div class="topbar-search"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" placeholder="Search…" id="globalSearch"></div><div class="topbar-actions"><a href="../index.php" class="icon-btn"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a></div></header>
  <div class="page-content">
    <?php foreach(getFlashes() as $f): ?><div class="alert alert-<?= h($f['type']) ?>"><?= h($f['message']) ?></div><?php endforeach; ?>
    <div class="page-header"><div><div class="page-title">Product Categories</div><div class="page-subtitle"><?= count($cats) ?> categories</div></div></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">
      <div class="table-card"><table class="data-table"><thead><tr><th>#</th><th>Name</th><th>Slug</th><th>Products</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach($cats as $c): ?>
        <tr>
          <td style="font-family:var(--mono);color:var(--text-muted)"><?= $c['id'] ?></td>
          <td style="font-weight:600"><?= h($c['name']) ?></td>
          <td style="font-family:var(--mono);font-size:.78rem;color:var(--text-muted)"><?= h($c['slug']) ?></td>
          <td><?= $c['product_count'] ?></td>
          <td><?php if($c['product_count']==0): ?><form method="POST" style="display:inline" onsubmit="return confirm('Delete category?')"><?= csrfField() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="cat_id" value="<?= $c['id'] ?>"><button type="submit" class="btn btn-danger btn-xs">Delete</button></form><?php else: ?><span style="color:var(--text-muted);font-size:.8rem">In use</span><?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody></table></div>
      <div class="table-card" style="padding:24px">
        <div style="font-size:1rem;font-weight:700;margin-bottom:16px">Add New Category</div>
        <form method="POST"><?= csrfField() ?><input type="hidden" name="action" value="add">
          <div style="margin-bottom:16px"><label style="display:block;font-weight:600;margin-bottom:6px">Category Name</label><input type="text" name="name" class="form-control" placeholder="e.g. Garden & Outdoor" required></div>
          <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
      </div>
    </div>
  </div>
</div></div>
<script src="js/admin.js"></script></body></html>
