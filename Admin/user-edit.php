<?php
require_once '../auth.php';
requireRole('/', 'super_admin', 'admin');
$adminUser = currentUser();
$db = db();
$id = (int)($_GET['id'] ?? 0);
$roles = $db->query('SELECT * FROM roles ORDER BY id')->fetchAll();
$errors = [];

$stmt = $db->prepare('SELECT * FROM users WHERE id=?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) { flash('danger','User not found.'); header('Location: users.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $city  = trim($_POST['city'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $roleId = hasRole('super_admin') ? (int)($_POST['role_id'] ?? $user['role_id']) : $user['role_id'];
    $newPass = $_POST['password'] ?? '';

    if (!$first || !$last) $errors[] = 'First and last name are required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';

    if (empty($errors)) {
        $check = $db->prepare('SELECT id FROM users WHERE email=? AND id<>?');
        $check->execute([$email,$id]);
        if ($check->fetch()) {
            $errors[] = 'Another user already uses this email.';
        } else {
            if ($newPass !== '' && strlen($newPass) >= 8) {
                $db->prepare('UPDATE users SET first_name=?,last_name=?,email=?,phone=?,province=?,city=?,status=?,role_id=?,password_hash=? WHERE id=?')
                   ->execute([$first,$last,$email,$phone,$province,$city,$status,$roleId,password_hash($newPass,PASSWORD_BCRYPT),$id]);
            } else {
                $db->prepare('UPDATE users SET first_name=?,last_name=?,email=?,phone=?,province=?,city=?,status=?,role_id=? WHERE id=?')
                   ->execute([$first,$last,$email,$phone,$province,$city,$status,$roleId,$id]);
            }
            auditLog('user_edited_by_admin','users',$id,"Edited by {$adminUser['email']}");
            flash('success','User updated successfully.');
            header('Location: users.php'); exit;
        }
    }
    $user = array_merge($user, $_POST);
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit User — Admin | ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/admin.css">
</head><body>
<div class="admin-layout">
<?php include __DIR__.'/sidebar.php'; ?>
<div class="main-wrap">
  <header class="topbar"><span class="topbar-title">Edit User</span><div class="topbar-actions"><a href="../index.php" class="icon-btn"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a></div></header>
  <div class="page-content">
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= h($e) ?></div><?php endforeach; ?>
    <div class="page-header"><div><div class="page-title">Edit User #<?= $user['id'] ?></div><div class="page-subtitle">Update account details</div></div><a href="users.php" class="btn btn-outline">← Back to Users</a></div>
    <div class="table-card" style="padding:28px;max-width:640px">
      <form method="POST">
        <?= csrfField() ?>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div><label style="display:block;font-weight:600;margin-bottom:6px">First Name</label><input type="text" name="first_name" class="form-control" required value="<?= h($user['first_name']) ?>"></div>
          <div><label style="display:block;font-weight:600;margin-bottom:6px">Last Name</label><input type="text" name="last_name" class="form-control" required value="<?= h($user['last_name']) ?>"></div>
        </div>
        <div style="margin-bottom:16px"><label style="display:block;font-weight:600;margin-bottom:6px">Email Address</label><input type="email" name="email" class="form-control" required value="<?= h($user['email']) ?>"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div><label style="display:block;font-weight:600;margin-bottom:6px">Phone</label><input type="text" name="phone" class="form-control" value="<?= h($user['phone']??'') ?>"></div>
          <div><label style="display:block;font-weight:600;margin-bottom:6px">Province</label><input type="text" name="province" class="form-control" value="<?= h($user['province']??'') ?>"></div>
        </div>
        <div style="margin-bottom:16px"><label style="display:block;font-weight:600;margin-bottom:6px">City</label><input type="text" name="city" class="form-control" value="<?= h($user['city']??'') ?>"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div><label style="display:block;font-weight:600;margin-bottom:6px">Role</label>
            <select name="role_id" class="form-control" <?= hasRole('super_admin')?'':'disabled' ?>>
              <?php foreach($roles as $r): ?><option value="<?= $r['id'] ?>" <?= $user['role_id']==$r['id']?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$r['name'])) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div><label style="display:block;font-weight:600;margin-bottom:6px">Status</label>
            <select name="status" class="form-control">
              <?php foreach(['active','pending','suspended'] as $s): ?><option value="<?= $s ?>" <?= $user['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
            </select>
          </div>
        </div>
        <div style="margin-bottom:24px"><label style="display:block;font-weight:600;margin-bottom:6px">New Password (leave blank to keep current)</label><input type="password" name="password" class="form-control" minlength="8"></div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="users.php" class="btn btn-outline">Cancel</a>
      </form>
    </div>
  </div>
</div></div>
<script src="js/admin.js"></script></body></html>
