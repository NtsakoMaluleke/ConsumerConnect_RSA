<?php
require_once '../auth.php';
requireRole('/', 'super_admin', 'admin');
$adminUser = currentUser();
$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $keys = ['site_name','site_tagline','commission_rate','maintenance_mode','currency_symbol','support_email'];
    foreach ($keys as $k) {
        $val = trim($_POST[$k] ?? '');
        $db->prepare('INSERT INTO settings (key_name,value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?, updated_at=NOW()')->execute([$k,$val,$val]);
    }
    auditLog('settings_updated','settings',null,'Platform settings saved');
    flash('success','Settings saved successfully.');
    header('Location: settings.php'); exit;
}

$settings = [];
foreach ($db->query('SELECT key_name, value FROM settings')->fetchAll() as $row) {
    $settings[$row['key_name']] = $row['value'];
}
function sv($settings,$key,$default='') { return htmlspecialchars($settings[$key] ?? $default); }
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Settings — Admin | ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/admin.css">
</head><body>
<div class="admin-layout">
<?php include __DIR__.'/sidebar.php'; ?>
<div class="main-wrap">
  <header class="topbar"><span class="topbar-title">Site Settings</span><div class="topbar-search"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" placeholder="Search…" id="globalSearch"></div><div class="topbar-actions"><a href="../index.php" class="icon-btn"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a></div></header>
  <div class="page-content">
    <?php foreach(getFlashes() as $f): ?><div class="alert alert-<?= h($f['type']) ?>"><?= h($f['message']) ?></div><?php endforeach; ?>
    <div class="page-header"><div><div class="page-title">Platform Settings</div><div class="page-subtitle">Global configuration for ConsumerConnect RSA</div></div></div>
    <form method="POST">
      <?= csrfField() ?>
      <div class="table-card" style="padding:28px;max-width:700px">
        <div style="margin-bottom:20px"><label style="display:block;font-weight:600;margin-bottom:6px">Site Name</label><input type="text" name="site_name" class="form-control" value="<?= sv($settings,'site_name','ConsumerConnect RSA') ?>"></div>
        <div style="margin-bottom:20px"><label style="display:block;font-weight:600;margin-bottom:6px">Site Tagline</label><input type="text" name="site_tagline" class="form-control" value="<?= sv($settings,'site_tagline','Buy & Sell Across South Africa') ?>"></div>
        <div style="margin-bottom:20px"><label style="display:block;font-weight:600;margin-bottom:6px">Commission Rate (%)</label><input type="number" name="commission_rate" class="form-control" step="0.01" min="0" max="100" value="<?= sv($settings,'commission_rate','5.00') ?>"></div>
        <div style="margin-bottom:20px"><label style="display:block;font-weight:600;margin-bottom:6px">Currency Symbol</label><input type="text" name="currency_symbol" class="form-control" style="max-width:100px" value="<?= sv($settings,'currency_symbol','R') ?>"></div>
        <div style="margin-bottom:20px"><label style="display:block;font-weight:600;margin-bottom:6px">Support Email</label><input type="email" name="support_email" class="form-control" value="<?= sv($settings,'support_email','support@consumerconnect.co.za') ?>"></div>
        <div style="margin-bottom:28px"><label style="display:block;font-weight:600;margin-bottom:6px">Maintenance Mode</label>
          <select name="maintenance_mode" class="form-control" style="width:auto">
            <option value="0" <?= ($settings['maintenance_mode']??'0')==='0'?'selected':'' ?>>Off — Site is live</option>
            <option value="1" <?= ($settings['maintenance_mode']??'0')==='1'?'selected':'' ?>>On — Maintenance page shown</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
      </div>
    </form>
  </div>
</div></div>
<script src="js/admin.js"></script></body></html>
