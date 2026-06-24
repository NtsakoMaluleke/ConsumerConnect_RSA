<?php
require_once '../auth.php';
requireRole('/', 'super_admin', 'admin');
$adminUser = currentUser();
$db = db();
$roles = $db->query('SELECT * FROM roles ORDER BY id')->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $city  = trim($_POST['city'] ?? '');
    $roleId = (int)($_POST['role_id'] ?? 0);
    $pass  = $_POST['password'] ?? '';
    $status = $_POST['status'] ?? 'active';

    if (!$first || !$last) $errors[] = 'First and last name are required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (strlen($pass) < 8) $errors[] = 'Password must be at least 8 characters.';
    if (!$roleId) $errors[] = 'Please select a role.';

    if (empty($errors)) {
        $check = $db->prepare('SELECT id FROM users WHERE email=?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = 'A user with this email already exists.';
        } else {
            $db->prepare(
                'INSERT INTO users (role_id,first_name,last_name,email,phone,password_hash,province,city,status,verified_at)
                 VALUES (?,?,?,?,?,?,?,?,?,NOW())'
            )->execute([$roleId,$first,$last,$email,$phone,password_hash($pass,PASSWORD_BCRYPT),$province,$city,$status]);
            $newId = (int)$db->lastInsertId();
            auditLog('user_created_by_admin','users',$newId,"Created by {$adminUser['email']}");
            flash('success',"User $first $last created successfully.");
            header('Location: users.php'); exit;
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create User — Admin | ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/admin.css">
</head><body>
<div class="admin-layout">
<?php include __DIR__.'/sidebar.php'; ?>
<div class="main-wrap">
  <header class="topbar"><span class="topbar-title">Create User</span><div class="topbar-actions"><a href="../index.php" class="icon-btn"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a></div></header>
  <div class="page-content">
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= h($e) ?></div><?php endforeach; ?>
    <div class="page-header"><div><div class="page-title">Create New User</div><div class="page-subtitle">Manually add a user account to the platform</div></div><a href="users.php" class="btn btn-outline">← Back to Users</a></div>
    <div class="table-card" style="padding:28px;max-width:640px">
      <form method="POST">
        <?= csrfField() ?>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div><label style="display:block;font-weight:600;margin-bottom:6px">First Name</label><input type="text" name="first_name" class="form-control" required value="<?= h($_POST['first_name']??'') ?>"></div>
          <div><label style="display:block;font-weight:600;margin-bottom:6px">Last Name</label><input type="text" name="last_name" class="form-control" required value="<?= h($_POST['last_name']??'') ?>"></div>
        </div>
        <div style="margin-bottom:16px"><label style="display:block;font-weight:600;margin-bottom:6px">Email Address</label><input type="email" name="email" class="form-control" required value="<?= h($_POST['email']??'') ?>"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div><label style="display:block;font-weight:600;margin-bottom:6px">Phone</label><input type="text" name="phone" class="form-control" value="<?= h($_POST['phone']??'') ?>"></div>
          <div><label style="display:block;font-weight:600;margin-bottom:6px">Province</label><input type="text" name="province" class="form-control" value="<?= h($_POST['province']??'') ?>"></div>
        </div>
        <div style="margin-bottom:16px"><label style="display:block;font-weight:600;margin-bottom:6px">City</label><input type="text" name="city" class="form-control" value="<?= h($_POST['city']??'') ?>"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div><label style="display:block;font-weight:600;margin-bottom:6px">Role</label>
            <select name="role_id" class="form-control" required>
              <option value="">Select role…</option>
              <?php foreach($roles as $r): ?><option value="<?= $r['id'] ?>"><?= ucfirst(str_replace('_',' ',$r['name'])) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div><label style="display:block;font-weight:600;margin-bottom:6px">Status</label>
            <select name="status" class="form-control">
              <option value="active">Active</option>
              <option value="pending">Pending</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>
        </div>
        <div style="margin-bottom:24px"><label style="display:block;font-weight:600;margin-bottom:6px">Password</label><input type="password" name="password" class="form-control" required minlength="8"></div>
        <button type="submit" class="btn btn-primary">Create User</button>
        <a href="users.php" class="btn btn-outline">Cancel</a>
      </form>
    </div>
  </div>
</div></div>
<script src="js/admin.js"></script></body></html>
