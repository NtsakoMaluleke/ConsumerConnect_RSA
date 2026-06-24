<?php
require_once 'auth.php';
requireLogin();
$user   = currentUser();
$db     = db();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'profile') {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name']  ?? '');
        $phone     = trim($_POST['phone']      ?? '');
        $province  = trim($_POST['province']   ?? '');
        $city      = trim($_POST['city']       ?? '');
        if (!$firstName || !$lastName) $errors[] = 'First and last name are required.';
        if (empty($errors)) {
            $db->prepare('UPDATE users SET first_name=?,last_name=?,phone=?,province=?,city=?,updated_at=NOW() WHERE id=?')
               ->execute([$firstName,$lastName,$phone,$province,$city,$user['id']]);
            $success = 'Profile updated successfully!';
            $user = currentUser(); // refresh
        }
    }

    if ($action === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!verifyPassword($current, $user['password_hash'])) $errors[] = 'Current password is incorrect.';
        elseif (strlen($new) < 8) $errors[] = 'New password must be at least 8 characters.';
        elseif ($new !== $confirm) $errors[] = 'New passwords do not match.';
        if (empty($errors)) {
            $db->prepare('UPDATE users SET password_hash=?,updated_at=NOW() WHERE id=?')
               ->execute([hashPassword($new),$user['id']]);
            $success = 'Password changed successfully!';
        }
    }
}

$provinces=['Eastern Cape','Free State','Gauteng','KwaZulu-Natal','Limpopo','Mpumalanga','Northern Cape','North West','Western Cape'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Profile Settings — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
<style>
.page-header{background:var(--green-deep);color:#fff;padding:28px 0}
.page-header h1{font-size:clamp(1.2rem,4vw,1.7rem)}
.profile-wrap{max-width:760px;margin:32px auto 60px}
.profile-tabs{display:flex;gap:4px;background:var(--white);border-radius:var(--radius-lg);padding:6px;box-shadow:var(--shadow-sm);margin-bottom:24px;overflow-x:auto}
.ptab{flex:1;padding:10px 14px;border-radius:var(--radius-md);font-size:.85rem;font-weight:600;cursor:pointer;text-align:center;border:none;background:none;color:var(--grey-700);transition:var(--transition);white-space:nowrap}
.ptab.active{background:var(--green-mid);color:#fff}
.tab-panel{display:none}
.tab-panel.active{display:block}
.profile-card{background:var(--white);border-radius:var(--radius-lg);padding:24px;box-shadow:var(--shadow-sm);margin-bottom:16px}
.profile-card h3{font-size:1rem;margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid var(--grey-100)}
.form-row-2{display:grid;grid-template-columns:1fr;gap:0}
@media(min-width:480px){.form-row-2{grid-template-columns:1fr 1fr;gap:14px}}
.avatar-section{display:flex;align-items:center;gap:16px;margin-bottom:24px;padding-bottom:20px;border-bottom:2px solid var(--grey-100)}
.avatar-circle{width:72px;height:72px;border-radius:50%;background:var(--green-mid);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:900;color:#fff;flex-shrink:0}
.avatar-info h4{margin-bottom:4px}
.avatar-info p{font-size:.82rem;color:var(--grey-400)}
.danger-zone{background:#fdedec;border:1.5px solid #f5c6cb;border-radius:var(--radius-lg);padding:20px}
.danger-zone h3{color:var(--danger);margin-bottom:8px;font-size:.95rem}
.danger-zone p{font-size:.85rem;color:var(--grey-700);margin-bottom:14px}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page-header">
  <div class="container">
    <div style="font-size:.78rem;opacity:.6;margin-bottom:6px"><a href="dashboard.php" style="color:rgba(255,255,255,.7)">Dashboard</a> › Profile</div>
    <h1>Profile Settings</h1>
  </div>
</div>

<div class="container">
  <div class="profile-wrap">

    <?php if ($success): ?>
    <div class="alert alert-success"><?= h($success) ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
    <div class="alert alert-danger"><?php foreach($errors as $e) echo '<div>• '.h($e).'</div>'; ?></div>
    <?php endif; ?>

    <div class="profile-tabs">
      <button class="ptab active" onclick="switchTab('info',this)">Personal Info</button>
      <button class="ptab" onclick="switchTab('password',this)">Password</button>
      <button class="ptab" onclick="switchTab('account',this)">Account</button>
    </div>

    <!-- Personal Info -->
    <div class="tab-panel active" id="tab-info">
      <div class="profile-card">
        <div class="avatar-section">
          <div class="avatar-circle"><?= strtoupper(substr($user['first_name'],0,1)) ?></div>
          <div class="avatar-info">
            <h4><?= h($user['first_name'].' '.$user['last_name']) ?></h4>
            <p><?= h($user['email']) ?></p>
            <p>Member since <?= date('F Y',strtotime($user['created_at'])) ?></p>
          </div>
        </div>
        <h3>Personal Information</h3>
        <form method="POST">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="profile">
          <div class="form-row-2">
            <div class="form-group"><label class="form-label">First Name</label><input class="form-control" type="text" name="first_name" value="<?= h($user['first_name']) ?>" required></div>
            <div class="form-group"><label class="form-label">Last Name</label><input class="form-control" type="text" name="last_name" value="<?= h($user['last_name']) ?>" required></div>
          </div>
          <div class="form-group"><label class="form-label">Email Address</label><input class="form-control" type="email" value="<?= h($user['email']) ?>" disabled style="opacity:.6;cursor:not-allowed"><small style="color:var(--grey-400);font-size:.78rem">Email cannot be changed.</small></div>
          <div class="form-group"><label class="form-label">Phone Number</label><input class="form-control" type="tel" name="phone" value="<?= h($user['phone']??'') ?>" placeholder="e.g. 071 234 5678"></div>
          <div class="form-row-2">
            <div class="form-group"><label class="form-label">Province</label>
              <select class="form-control" name="province">
                <option value="">Select province…</option>
                <?php foreach($provinces as $p): ?><option value="<?= h($p) ?>" <?= ($user['province']??'')===$p?'selected':'' ?>><?= h($p) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label class="form-label">City</label><input class="form-control" type="text" name="city" value="<?= h($user['city']??'') ?>" placeholder="e.g. Johannesburg"></div>
          </div>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
      </div>
    </div>

    <!-- Password -->
    <div class="tab-panel" id="tab-password">
      <div class="profile-card">
        <h3>Change Password</h3>
        <form method="POST">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="password">
          <div class="form-group"><label class="form-label">Current Password</label><input class="form-control" type="password" name="current_password" required placeholder="Enter current password"></div>
          <div class="form-group"><label class="form-label">New Password</label><input class="form-control" type="password" name="new_password" required placeholder="At least 8 characters"></div>
          <div class="form-group"><label class="form-label">Confirm New Password</label><input class="form-control" type="password" name="confirm_password" required placeholder="Repeat new password"></div>
          <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
      </div>
    </div>

    <!-- Account -->
    <div class="tab-panel" id="tab-account">
      <div class="profile-card">
        <h3>Account Information</h3>
        <div style="display:flex;flex-direction:column;gap:12px;font-size:.9rem">
          <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--grey-100)"><span style="color:var(--grey-400)">Account ID</span><span style="font-weight:600">#<?= $user['id'] ?></span></div>
          <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--grey-100)"><span style="color:var(--grey-400)">Role</span><span style="font-weight:600"><?= ucfirst(str_replace('_',' ',$user['role_name']??'buyer')) ?></span></div>
          <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--grey-100)"><span style="color:var(--grey-400)">Status</span><span style="font-weight:600;color:var(--success)"><?= ucfirst($user['status']) ?></span></div>
          <div style="display:flex;justify-content:space-between;padding:10px 0"><span style="color:var(--grey-400)">Member since</span><span style="font-weight:600"><?= date('d F Y',strtotime($user['created_at'])) ?></span></div>
        </div>
      </div>
      <div class="danger-zone">
        <h3>⚠️ Danger Zone</h3>
        <p>Once you delete your account all your data will be permanently removed. This action cannot be undone.</p>
        <button class="btn btn-danger btn-sm" onclick="if(confirm('Are you sure? This cannot be undone.')) alert('Please contact support to delete your account.')">Delete Account</button>
      </div>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
<script>
function switchTab(id, btn) {
  document.querySelectorAll('.ptab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tab-' + id).classList.add('active');
}
</script>
</body></html>
