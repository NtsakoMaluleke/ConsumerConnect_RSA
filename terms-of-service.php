<?php
require_once 'auth.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Terms of Service — ConsumerConnect RSA</title>
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
.legal-updated { display:inline-block; background: var(--grey-100); color: var(--grey-700); padding: 5px 14px; border-radius: var(--radius-sm); font-size: .82rem; margin-bottom: 8px; }
@media(max-width:900px){ .legal-layout { grid-template-columns: 1fr; } .legal-toc { position: static; } .legal-content { padding: 28px; } }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="legal-hero">
  <div class="container">
    <h1>Terms of Service</h1>
    <p>Please read these terms carefully before using ConsumerConnect RSA.</p>
  </div>
</div>

<section class="legal-wrap">
  <div class="container">
    <div class="legal-layout">
      <aside class="legal-toc">
        <h4>On This Page</h4>
        <a href="#acceptance">1. Acceptance of Terms</a>
        <a href="#eligibility">2. Eligibility</a>
        <a href="#accounts">3. User Accounts</a>
        <a href="#listings">4. Listings & Conduct</a>
        <a href="#payments">5. Payments & Escrow</a>
        <a href="#prohibited">6. Prohibited Activities</a>
        <a href="#liability">7. Limitation of Liability</a>
        <a href="#termination">8. Termination</a>
        <a href="#changes">9. Changes to Terms</a>
        <a href="#contact">10. Contact</a>
      </aside>

      <div class="legal-content">
        <span class="legal-updated">Last updated: June 2026</span>

        <h2 id="acceptance">1. Acceptance of Terms</h2>
        <p>By registering for, accessing, or using ConsumerConnect RSA ("the Platform"), you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use the Platform.</p>

        <h2 id="eligibility">2. Eligibility</h2>
        <p>You must be at least 18 years old and capable of forming a legally binding contract to register an account. By registering, you confirm that the information you provide is accurate and that you meet these requirements.</p>

        <h2 id="accounts">3. User Accounts</h2>
        <ul>
          <li>You are responsible for maintaining the confidentiality of your password and account.</li>
          <li>You agree to notify us immediately of any unauthorised use of your account.</li>
          <li>Accounts may be suspended or terminated for violation of these Terms.</li>
        </ul>

        <h2 id="listings">4. Listings & Conduct</h2>
        <p>Sellers are solely responsible for the accuracy of their listings, including price, condition, and description. The Platform reserves the right to review, approve, reject, or remove any listing that violates these Terms or applicable South African law.</p>

        <h2 id="payments">5. Payments & Escrow</h2>
        <p>ConsumerConnect RSA facilitates secure escrow payments between buyers and sellers. Funds are held by the Platform until the buyer confirms receipt of goods. The Platform may charge a commission fee on completed transactions as disclosed at checkout.</p>

        <h2 id="prohibited">6. Prohibited Activities</h2>
        <ul>
          <li>Listing stolen, counterfeit, or illegal goods</li>
          <li>Circumventing escrow by arranging off-platform payment</li>
          <li>Harassing, threatening, or defrauding other users</li>
          <li>Creating multiple accounts to manipulate ratings or reviews</li>
        </ul>

        <h2 id="liability">7. Limitation of Liability</h2>
        <p>ConsumerConnect RSA acts as an intermediary platform and is not a party to transactions between buyers and sellers. To the maximum extent permitted by law, the Platform is not liable for any indirect, incidental, or consequential damages arising from your use of the Platform.</p>

        <h2 id="termination">8. Termination</h2>
        <p>We may suspend or terminate your access to the Platform at any time, with or without notice, for conduct that violates these Terms or is otherwise harmful to other users or the Platform.</p>

        <h2 id="changes">9. Changes to Terms</h2>
        <p>We may update these Terms from time to time. Continued use of the Platform after changes are posted constitutes acceptance of the revised Terms.</p>

        <h2 id="contact">10. Contact</h2>
        <p>If you have questions about these Terms, please reach out via our <a href="contact-us.php" style="color:var(--green-mid);font-weight:600;">Contact Us</a> page.</p>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
</body>
</html>
