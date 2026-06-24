<?php
require_once 'auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (!$email || !$pass) {
        $errors[] = 'Please enter your email and password.';
    } else {
        $stmt = db()->prepare(
            'SELECT u.*, r.name AS role_name FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && verifyPassword($pass, $user['password_hash'])) {
            if ($user['status'] === 'suspended') {
                $errors[] = 'Your account has been suspended. Please contact support.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['role_name'] = $user['role_name'];
                auditLog('login', 'users', $user['id']);
                flash('success', 'Welcome back, ' . $user['first_name'] . '!');

                $redirect = in_array($user['role_name'], ['super_admin','admin','moderator'])
                    ? '../admin/index.php'
                    : 'index.php';
                header('Location: ' . $redirect);
                exit;
            }
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign In — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">ConsumerConnect<span class="dot">.</span>RSA</div>
    <p class="auth-subtitle">Sign in to your account</p>

    <?php foreach ($errors as $e): ?>
    <div class="alert alert-danger"><?= h($e) ?></div>
    <?php endforeach; ?>

    <form method="POST">
      <?= csrfField() ?>
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" value="<?= h($_POST['email'] ?? '') ?>" placeholder="you@example.co.za" required autofocus>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;font-size:.88rem;">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
          <input type="checkbox" name="remember"> Remember me
        </label>
        <a href="forgot-password.php" style="color:var(--green-mid);">Forgot password?</a>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Sign In</button>
    </form>

    <div class="auth-divider">or</div>
    <p style="text-align:center;font-size:.9rem;color:var(--grey-400);">
      Don't have an account? <a href="register.php" style="color:var(--green-mid);font-weight:600;">Create one free</a>
    </p>
    <p style="text-align:center;margin-top:24px;">
      <a href="index.php" style="color:var(--grey-400);font-size:.85rem;">← Back to Home</a>
    </p>
  </div>
</div>
</body>
</html>
