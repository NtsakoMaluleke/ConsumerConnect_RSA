<?php
require_once '../auth.php';
requireRole('/', 'super_admin', 'admin', 'moderator');
$adminUser = currentUser();

// ── Stats ──────────────────────────────────────────────────────
$db = db();
$totalUsers    = $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalProducts = $db->query('SELECT COUNT(*) FROM products WHERE status="active"')->fetchColumn();
$totalOrders   = $db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$totalRevenue  = $db->query('SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status NOT IN ("cancelled","refunded")')->fetchColumn();
$pendingProducts = $db->query('SELECT COUNT(*) FROM products WHERE status="pending"')->fetchColumn();
$newUsersToday   = $db->query('SELECT COUNT(*) FROM users WHERE DATE(created_at)=CURDATE()')->fetchColumn();

// ── Recent orders ──────────────────────────────────────────────
$recentOrders = $db->query(
    'SELECT o.*, u.first_name, u.last_name, u.email
     FROM orders o JOIN users u ON o.buyer_id=u.id
     ORDER BY o.created_at DESC LIMIT 8'
)->fetchAll();

// ── Recent users ───────────────────────────────────────────────
$recentUsers = $db->query(
    'SELECT u.*, r.name AS role_name
     FROM users u JOIN roles r ON u.role_id=r.id
     ORDER BY u.created_at DESC LIMIT 6'
)->fetchAll();

// ── User role breakdown ────────────────────────────────────────
$roleBreakdown = $db->query(
    'SELECT r.name, COUNT(u.id) AS cnt
     FROM roles r LEFT JOIN users u ON u.role_id=r.id
     GROUP BY r.id ORDER BY r.id'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard — Admin | ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="admin-layout">

  <?php include __DIR__ . '/sidebar.php'; ?>

  <div class="main-wrap">
    <!-- Topbar -->
    <header class="topbar">
      <span class="topbar-title">Dashboard</span>
      <div class="topbar-search">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search users, orders, products…" id="globalSearch">
      </div>
      <div class="topbar-actions">
        <div class="icon-btn">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <div class="notif-dot"></div>
        </div>
        <a href="../index.php" class="icon-btn" title="View Site">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        </a>
      </div>
    </header>

    <div class="page-content">
      <!-- Flash messages -->
      <?php foreach (getFlashes() as $f): ?>
      <div class="alert alert-<?= htmlspecialchars($f['type']) ?>"><?= htmlspecialchars($f['message']) ?></div>
      <?php endforeach; ?>

      <!-- Page header -->
      <div class="page-header">
        <div>
          <div class="page-title">Welcome back, <?= htmlspecialchars($adminUser['first_name']) ?> 👋</div>
          <div class="page-subtitle">Here's what's happening on ConsumerConnect RSA today.</div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="user-create.php" class="btn btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add User
          </a>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-card-header">
            <div class="stat-label">Total Users</div>
            <div class="stat-icon green">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
          </div>
          <div class="stat-value"><?= number_format($totalUsers) ?></div>
          <div class="stat-change"><span class="up">+<?= $newUsersToday ?></span> today</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-header">
            <div class="stat-label">Active Listings</div>
            <div class="stat-icon gold">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
            </div>
          </div>
          <div class="stat-value"><?= number_format($totalProducts) ?></div>
          <div class="stat-change"><span class="up"><?= $pendingProducts ?></span> pending review</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-header">
            <div class="stat-label">Total Orders</div>
            <div class="stat-icon blue">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
          </div>
          <div class="stat-value"><?= number_format($totalOrders) ?></div>
          <div class="stat-change">All time</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-header">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-icon red">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
          </div>
          <div class="stat-value" style="font-size:1.5rem;">R <?= number_format($totalRevenue, 2) ?></div>
          <div class="stat-change">Net revenue</div>
        </div>
      </div>

      <!-- Two-column layout -->
      <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px">

        <!-- Recent Orders Table -->
        <div class="table-card">
          <div class="table-card-header">
            <div class="table-card-title">Recent Orders</div>
            <a href="orders.php" class="btn btn-outline btn-sm">View all</a>
          </div>
          <table class="data-table">
            <thead>
              <tr>
                <th>#ID</th>
                <th>Buyer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentOrders as $o): ?>
              <tr>
                <td><span style="font-family:var(--mono);color:var(--text-muted)">#<?= str_pad($o['id'],5,'0',STR_PAD_LEFT) ?></span></td>
                <td>
                  <div style="font-weight:600"><?= htmlspecialchars($o['first_name'].' '.$o['last_name']) ?></div>
                  <div style="font-size:.75rem;color:var(--text-muted)"><?= htmlspecialchars($o['email']) ?></div>
                </td>
                <td style="font-family:var(--mono);font-weight:600">R <?= number_format($o['total_amount'],2) ?></td>
                <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                <td style="color:var(--text-muted);font-size:.8rem"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($recentOrders)): ?>
              <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:28px">No orders yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Role breakdown -->
        <div class="table-card">
          <div class="table-card-header">
            <div class="table-card-title">Users by Role</div>
            <a href="roles.php" class="btn btn-outline btn-sm">Manage</a>
          </div>
          <div style="padding:20px">
            <?php foreach ($roleBreakdown as $r): ?>
            <div style="margin-bottom:16px">
              <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:.85rem;">
                <span class="badge badge-role-<?= $r['name'] ?>"><?= ucfirst(str_replace('_',' ',$r['name'])) ?></span>
                <span style="font-family:var(--mono);font-weight:600"><?= $r['cnt'] ?></span>
              </div>
              <div style="height:6px;background:var(--border);border-radius:3px;overflow:hidden">
                <div style="height:100%;background:var(--accent-2);width:<?= $totalUsers > 0 ? round($r['cnt']/$totalUsers*100) : 0 ?>%;border-radius:3px;transition:width .6s"></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Recent Users Table -->
      <div class="table-card">
        <div class="table-card-header">
          <div class="table-card-title">Recently Registered Users</div>
          <a href="users.php" class="btn btn-outline btn-sm">View all</a>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>User</th>
              <th>Role</th>
              <th>Province</th>
              <th>Status</th>
              <th>Registered</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentUsers as $u): ?>
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div style="width:34px;height:34px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0">
                    <?= strtoupper(substr($u['first_name'],0,1)) ?>
                  </div>
                  <div>
                    <div style="font-weight:600"><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></div>
                    <div style="font-size:.75rem;color:var(--text-muted)"><?= htmlspecialchars($u['email']) ?></div>
                  </div>
                </div>
              </td>
              <td><span class="badge badge-role-<?= $u['role_name'] ?>"><?= ucfirst(str_replace('_',' ',$u['role_name'])) ?></span></td>
              <td style="color:var(--text-muted)"><?= htmlspecialchars($u['province'] ?? '—') ?></td>
              <td><span class="badge badge-<?= $u['status'] ?>"><?= ucfirst($u['status']) ?></span></td>
              <td style="color:var(--text-muted);font-size:.8rem"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
              <td>
                <div class="actions">
                  <a href="user-edit.php?id=<?= $u['id'] ?>" class="btn btn-outline btn-xs">Edit</a>
                  <?php if ($u['id'] !== $adminUser['id']): ?>
                  <a href="user-delete.php?id=<?= $u['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Delete this user?')">Delete</a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div><!-- /.page-content -->
  </div><!-- /.main-wrap -->
</div><!-- /.admin-layout -->

<script src="js/admin.js"></script>
</body>
</html>