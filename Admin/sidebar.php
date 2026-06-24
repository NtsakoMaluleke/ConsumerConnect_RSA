<?php
// admin/partials/sidebar.php
$currentFile = basename($_SERVER['PHP_SELF']);
function navItem(string $file, string $icon, string $label, ?string $badge = null, string $currentFile = ''): string {
    $active = ($currentFile === $file) ? ' active' : '';
    $bdg = $badge ? "<span class='nav-badge'>$badge</span>" : '';
    return "<a href='$file' class='nav-item$active'>$icon <span>$label</span>$bdg</a>";
}
?>
<aside class="sidebar" id="adminSidebar">
  <div class="sidebar-brand">
    <div class="brand-icon">CC</div>
    <div>
      <div class="sidebar-brand-text">ConsumerConnect</div>
      <div class="sidebar-brand-sub">Admin Panel · RSA</div>
    </div>
  </div>

  <div class="sidebar-section-label">Overview</div>
  <?= navItem('index.php', '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>', 'Dashboard', null, $currentFile) ?>
  <?= navItem('audit-log.php', '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>', 'Audit Log', null, $currentFile) ?>

  <div class="sidebar-section-label">Users & Access</div>
  <?= navItem('users.php', '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>', 'All Users', null, $currentFile) ?>
  <?= navItem('roles.php', '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>', 'Roles & RBAC', null, $currentFile) ?>
  <?= navItem('user-create.php', '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>', 'Create User', null, $currentFile) ?>

  <div class="sidebar-section-label">Marketplace</div>
  <?= navItem('products.php', '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>', 'Products', null, $currentFile) ?>
  <?= navItem('orders.php', '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>', 'Orders', null, $currentFile) ?>
  <?= navItem('categories.php', '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>', 'Categories', null, $currentFile) ?>
  <?= navItem('reviews.php', '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', 'Reviews', null, $currentFile) ?>

  <div class="sidebar-section-label">Settings</div>
  <?= navItem('settings.php', '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>', 'Site Settings', null, $currentFile) ?>

  <div class="sidebar-footer">
    <div class="sidebar-user-avatar"><?= strtoupper(substr($adminUser['first_name'],0,1)) ?></div>
    <div>
      <div class="sidebar-user-name"><?= htmlspecialchars($adminUser['first_name'].' '.$adminUser['last_name']) ?></div>
      <div class="sidebar-user-role"><?= htmlspecialchars(ucfirst(str_replace('_',' ',$adminUser['role_name']))) ?></div>
    </div>
    <form method="POST" action="../logout.php" style="margin-left:auto">
      <?= csrfField() ?>
      <button class="sidebar-logout-btn" type="submit" title="Sign Out">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      </button>
    </form>
  </div>
</aside>