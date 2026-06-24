<?php
require_once 'auth.php';

if (isLoggedIn()) { header('Location: index.php'); exit; }

$provinces = ['Eastern Cape','Free State','Gauteng','KwaZulu-Natal','Limpopo','Mpumalanga','Northern Cape','North West','Western Cape'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $first    = trim($_POST['first_name'] ?? '');
    $last     = trim($_POST['last_name']  ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $phone    = trim($_POST['phone'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $city     = trim($_POST['city'] ?? '');
    $role     = $_POST['account_type'] ?? 'buyer'; // buyer|seller
    $pass     = $_POST['password'] ?? '';
    $pass2    = $_POST['password_confirm'] ?? '';

    if (!$first || !$last)        $errors[] = 'First and last name required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
    if (strlen($pass) < 8)        $errors[] = 'Password must be at least 8 characters.';
    if ($pass !== $pass2)         $errors[] = 'Passwords do not match.';
    if (!in_array($province, $provinces)) $errors[] = 'Please select a valid province.';

    if (empty($errors)) {
        // Check duplicate email
        $check = db()->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = 'An account with that email already exists.';
        } else {
            // Map role name to ID
            $roleStmt = db()->prepare('SELECT id FROM roles WHERE name = ?');
            $roleStmt->execute([$role === 'seller' ? 'seller' : 'buyer']);
            $roleRow = $roleStmt->fetch();
            $roleId  = $roleRow ? $roleRow['id'] : 5;

            db()->prepare(
                'INSERT INTO users (role_id,first_name,last_name,email,phone,password_hash,province,city,status,verified_at)
                 VALUES (?,?,?,?,?,?,?,?,"active",NOW())'
            )->execute([$roleId,$first,$last,$email,$phone,hashPassword($pass),$province,$city]);

            $userId = (int)db()->lastInsertId();
            auditLog('register', 'users', $userId, "Role: $role");

            session_regenerate_id(true);
            $_SESSION['user_id']   = $userId;
            $_SESSION['role_name'] = $role === 'seller' ? 'seller' : 'buyer';
            flash('success', "Welcome to ConsumerConnect RSA, $first!");
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create Account — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-page" style="align-items:flex-start;padding:40px 24px;">
  <div class="auth-card" style="max-width:560px;margin:auto;">
    <div class="auth-logo">ConsumerConnect<span class="dot">.</span>RSA</div>
    <p class="auth-subtitle">Create your free account</p>

    <?php foreach ($errors as $e): ?>
    <div class="alert alert-danger"><?= h($e) ?></div>
    <?php endforeach; ?>

    <form method="POST">
      <?= csrfField() ?>

      <!-- Account type toggle -->
      <div class="form-group">
        <label class="form-label">I want to…</label>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <label style="cursor:pointer;">
            <input type="radio" name="account_type" value="buyer" <?= ($_POST['account_type'] ?? 'buyer') === 'buyer' ? 'checked' : '' ?> style="display:none" class="role-radio">
            <div class="role-card" style="border:2px solid var(--green-mid);border-radius:12px;padding:16px;text-align:center;transition:all .2s;">
              <div style="font-size:1.8rem;margin-bottom:6px;">🛍️</div>
              <div style="font-weight:700;font-size:.95rem;">Buy</div>
              <div style="font-size:.78rem;color:var(--grey-400);">Browse &amp; purchase</div>
            </div>
          </label>
          <label style="cursor:pointer;">
            <input type="radio" name="account_type" value="seller" <?= ($_POST['account_type'] ?? '') === 'seller' ? 'checked' : '' ?> style="display:none" class="role-radio">
            <div class="role-card" style="border:2px solid var(--grey-200);border-radius:12px;padding:16px;text-align:center;transition:all .2s;">
              <div style="font-size:1.8rem;margin-bottom:6px;">🏪</div>
              <div style="font-weight:700;font-size:.95rem;">Sell</div>
              <div style="font-size:.78rem;color:var(--grey-400);">List &amp; earn</div>
            </div>
          </label>
        </div>
      </div>

      <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div class="form-group">
          <label class="form-label">First Name</label>
          <input type="text" name="first_name" class="form-control" value="<?= h($_POST['first_name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Last Name</label>
          <input type="text" name="last_name" class="form-control" value="<?= h($_POST['last_name'] ?? '') ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" value="<?= h($_POST['email'] ?? '') ?>" placeholder="you@example.co.za" required>
      </div>

      <div class="form-group">
        <label class="form-label">Phone Number (optional)</label>
        <input type="tel" name="phone" class="form-control" value="<?= h($_POST['phone'] ?? '') ?>" placeholder="+27 81 234 5678">
      </div>

      <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div class="form-group">
          <label class="form-label">Province</label>
          <select name="province" class="form-control" required>
            <option value="">Select…</option>
            <?php foreach ($provinces as $p): ?>
            <option value="<?= h($p) ?>" <?= ($_POST['province'] ?? '') === $p ? 'selected' : '' ?>><?= h($p) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">City</label>
          <input type="text" name="city" class="form-control" value="<?= h($_POST['city'] ?? '') ?>">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="At least 8 characters" required>
      </div>
      <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password_confirm" class="form-control" placeholder="Repeat password" required>
      </div>

      <p style="font-size:.82rem;color:var(--grey-400);margin-bottom:20px;">
        By registering you agree to our <a href="terms.php" style="color:var(--green-mid)">Terms of Service</a> and <a href="privacy.php" style="color:var(--green-mid)">Privacy Policy</a> (POPIA compliant).
      </p>

      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Create Account</button>
    </form>

    <div class="auth-divider">or</div>
    <p style="text-align:center;font-size:.9rem;color:var(--grey-400);">
      Already have an account? <a href="login.php" style="color:var(--green-mid);font-weight:600;">Sign in</a>
    </p>
  </div>
</div>

<script>
// Role card highlight
document.querySelectorAll('.role-radio').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.role-card').forEach(c => {
      c.style.borderColor = 'var(--grey-200)';
      c.style.background = '';
    });
    radio.nextElementSibling.style.borderColor = 'var(--green-mid)';
    radio.nextElementSibling.style.background = 'rgba(45,106,79,.06)';
  });
  if (radio.checked) {
    radio.nextElementSibling.style.borderColor = 'var(--green-mid)';
    radio.nextElementSibling.style.background = 'rgba(45,106,79,.06)';
  }
});
</script>
</body>
</html>
