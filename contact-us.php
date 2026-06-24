<?php
require_once 'auth.php';
$user = currentUser();
$db = db();
$errors = [];
$sent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name) $errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (!$subject) $errors[] = 'Please select a subject.';
    if (strlen($message) < 10) $errors[] = 'Please enter a message of at least 10 characters.';

    if (empty($errors)) {
        $db->prepare('INSERT INTO contact_messages (user_id,name,email,subject,message) VALUES (?,?,?,?,?)')
           ->execute([$user['id'] ?? null, $name, $email, $subject, $message]);
        $sent = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Contact Us — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
<style>
.contact-hero { background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-mid) 100%); color: #fff; padding: clamp(40px, 8vw, 80px) 0; text-align: center; }
.contact-hero h1 { font-size: 2.8rem; margin-bottom: 16px; }
.contact-hero p { font-size: 1.1rem; opacity: .85; max-width: 600px; margin: 0 auto; }
.contact-wrap { padding: 64px 0; }
.contact-grid { display: grid; grid-template-columns: 1.1fr 1.4fr; gap: 40px; align-items: start; }
.contact-info-card { background: var(--white); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm); margin-bottom: 20px; }
.contact-info-card .icon { width: 46px; height: 46px; background: var(--green-mid); border-radius: 50%; display:flex; align-items:center; justify-content:center; margin-bottom:14px; color:#fff; font-size:1.2rem; }
.contact-info-card h4 { margin-bottom: 6px; }
.contact-info-card p { color: var(--grey-400); font-size: .92rem; }
.contact-form-card { background: var(--white); border-radius: var(--radius-lg); padding: 36px; box-shadow: var(--shadow-md); }
.contact-success { text-align:center; padding: 40px 20px; }
.contact-success .check { width:64px; height:64px; background:var(--green-mid); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:1.8rem; color:#fff; }
@media(max-width:900px){ .contact-grid { grid-template-columns: 1fr; } .contact-hero h1 { font-size: 2rem; } }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="contact-hero">
  <div class="container">
    <h1>Contact Us</h1>
    <p>Have a question, a problem with an order, or feedback for us? We're here to help.</p>
  </div>
</div>

<section class="contact-wrap">
  <div class="container">
    <div class="contact-grid">
      <div>
        <div class="contact-info-card">
          <div class="icon">✉️</div>
          <h4>Email Support</h4>
          <p>support@consumerconnect.co.za<br>We typically reply within 24–48 hours.</p>
        </div>
        <div class="contact-info-card">
          <div class="icon">📍</div>
          <h4>Based In</h4>
          <p>Pretoria, Gauteng — serving all 9 South African provinces.</p>
        </div>
        <div class="contact-info-card">
          <div class="icon">🛡️</div>
          <h4>Report a Safety Issue</h4>
          <p>For urgent fraud or safety concerns, mark your subject as "Safety / Fraud Report" below for priority review.</p>
        </div>
      </div>

      <div class="contact-form-card">
        <?php if ($sent): ?>
          <div class="contact-success">
            <div class="check">✓</div>
            <h2>Message Sent</h2>
            <p style="color:var(--grey-400);margin-top:8px;">Thanks for reaching out — our support team will get back to you shortly.</p>
            <a href="contact-us.php" class="btn btn-outline" style="margin-top:20px;">Send Another Message</a>
          </div>
        <?php else: ?>
          <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger" style="margin-bottom:14px;"><?= h($e) ?></div>
          <?php endforeach; ?>
          <form method="POST">
            <?= csrfField() ?>
            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control" required value="<?= h($_POST['name'] ?? ($user['first_name'] ?? '').' '.($user['last_name'] ?? '')) ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control" required value="<?= h($_POST['email'] ?? ($user['email'] ?? '')) ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Subject</label>
              <select name="subject" class="form-control" required>
                <option value="">Select a topic…</option>
                <?php foreach (["General Enquiry","Order Issue","Account Help","Safety / Fraud Report","Seller Support","Other"] as $opt): ?>
                  <option value="<?= h($opt) ?>" <?= (($_POST['subject'] ?? '') === $opt) ? 'selected' : '' ?>><?= h($opt) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Message</label>
              <textarea name="message" class="form-control" rows="5" required placeholder="Tell us how we can help…"><?= h($_POST['message'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Send Message</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
</body>
</html>
