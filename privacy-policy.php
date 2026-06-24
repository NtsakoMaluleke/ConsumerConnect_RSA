<?php
require_once 'auth.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Privacy Policy — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
<style>
.legal-hero { background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-mid) 100%); color: #fff; padding: clamp(36px, 6vw, 64px) 0; text-align: center; }
.legal-hero h1 { font-size: 2.4rem; margin-bottom: 10px; }
.legal-hero p { opacity: .8; font-size: .95rem; }
.legal-wrap { padding: 56px 0; }
.legal-layout { display: grid; grid-template-columns: 260px 1fr; gap: 48px; align-items: start; }
.legal-toc { background: var(--white); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm); position: sticky; top: 90px; }
.legal-toc h4 { margin-bottom: 14px; font-size: .85rem; text-transform: uppercase; letter-spacing: .05em; color: var(--grey-400); }
.legal-toc a { display:block; padding: 7px 0; color: var(--grey-700); font-size: .9rem; text-decoration:none; border-bottom: 1px solid var(--grey-100); }
.legal-toc a:last-child { border-bottom: none; }
.legal-toc a:hover { color: var(--green-mid); }
.legal-content { background: var(--white); border-radius: var(--radius-lg); padding: 44px; box-shadow: var(--shadow-sm); }
.legal-content h2 { color: var(--green-deep); margin-top: 36px; margin-bottom: 14px; font-size: 1.35rem; }
.legal-content h2:first-child { margin-top: 0; }
.legal-content p, .legal-content li { color: var(--grey-700); font-size: .95rem; line-height: 1.75; }
.legal-content ul { padding-left: 22px; margin-bottom: 14px; }
.legal-content table { width:100%; border-collapse: collapse; margin: 16px 0; font-size:.9rem; }
.legal-content th { background: var(--grey-100); text-align:left; padding: 10px 12px; color: var(--green-deep); }
.legal-content td { padding: 10px 12px; border-bottom: 1px solid var(--grey-100); color: var(--grey-700); }
.legal-updated { display:inline-block; background: var(--grey-100); color: var(--grey-700); padding: 5px 14px; border-radius: var(--radius-sm); font-size: .82rem; margin-bottom: 8px; }
@media(max-width:900px){ .legal-layout { grid-template-columns: 1fr; } .legal-toc { position: static; } .legal-content { padding: 28px; } }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="legal-hero">
  <div class="container">
    <h1>Privacy Policy</h1>
    <p>How ConsumerConnect RSA collects, uses, and protects your personal information.</p>
  </div>
</div>

<section class="legal-wrap">
  <div class="container">
    <div class="legal-layout">
      <aside class="legal-toc">
        <h4>On This Page</h4>
        <a href="#collect">1. Information We Collect</a>
        <a href="#use">2. How We Use Your Data</a>
        <a href="#sharing">3. Sharing of Information</a>
        <a href="#security">4. Data Security</a>
        <a href="#retention">5. Data Retention</a>
        <a href="#rights">6. Your Rights</a>
        <a href="#cookies">7. Cookies</a>
        <a href="#contact">8. Contact Us</a>
      </aside>

      <div class="legal-content">
        <span class="legal-updated">Last updated: June 2026</span>

        <h2 id="collect">1. Information We Collect</h2>
        <table>
          <tr><th>Category</th><th>Examples</th></tr>
          <tr><td>Account Information</td><td>Name, email, phone number, province, city, password (hashed)</td></tr>
          <tr><td>Listing Information</td><td>Product titles, descriptions, prices, images</td></tr>
          <tr><td>Transaction Data</td><td>Order history, payment references, shipping addresses</td></tr>
          <tr><td>Usage Data</td><td>Pages visited, actions taken, IP address, device/browser type</td></tr>
        </table>

        <h2 id="use">2. How We Use Your Data</h2>
        <ul>
          <li>To create and manage your account</li>
          <li>To facilitate listings, orders, and escrow payments</li>
          <li>To communicate order updates and respond to support requests</li>
          <li>To detect and prevent fraud and platform abuse</li>
          <li>To improve our services and user experience</li>
        </ul>

        <h2 id="sharing">3. Sharing of Information</h2>
        <p>We do not sell your personal information. Information is shared only as necessary to operate the Platform — for example, sharing your delivery address with a seller after you place an order, or with a payment processor to complete a transaction. We may disclose information if required by South African law.</p>

        <h2 id="security">4. Data Security</h2>
        <p>We use industry-standard safeguards including encrypted password storage (bcrypt), parameterised database queries, CSRF protection, and role-based access control to protect your information from unauthorised access.</p>

        <h2 id="retention">5. Data Retention</h2>
        <p>We retain your personal information for as long as your account is active or as needed to provide services, comply with legal obligations, resolve disputes, and enforce our agreements.</p>

        <h2 id="rights">6. Your Rights</h2>
        <p>You have the right to access, correct, or request deletion of your personal information. You may update most details directly from your Profile page, or contact us to make a formal request.</p>

        <h2 id="cookies">7. Cookies</h2>
        <p>We use session cookies to keep you logged in and to maintain your cart. We do not use third-party advertising or tracking cookies.</p>

        <h2 id="contact">8. Contact Us</h2>
        <p>For privacy-related questions or requests, please use our <a href="contact-us.php" style="color:var(--green-mid);font-weight:600;">Contact Us</a> page or see our <a href="popia-compliance.php" style="color:var(--green-mid);font-weight:600;">POPIA Compliance</a> page for South Africa-specific information.</p>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
</body>
</html>
