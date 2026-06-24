<?php
require_once 'auth.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>POPIA Compliance — ConsumerConnect RSA</title>
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
.popia-badge { display:flex; align-items:center; gap:16px; background: var(--green-deep); color:#fff; border-radius: var(--radius-lg); padding: 28px; margin-bottom: 32px; }
.popia-badge .icon { font-size: 2.2rem; }
.popia-badge h3 { color: var(--gold); margin-bottom: 4px; }
.popia-badge p { opacity: .85; font-size: .9rem; margin: 0; }
.legal-updated { display:inline-block; background: var(--grey-100); color: var(--grey-700); padding: 5px 14px; border-radius: var(--radius-sm); font-size: .82rem; margin-bottom: 8px; }
@media(max-width:900px){ .legal-layout { grid-template-columns: 1fr; } .legal-toc { position: static; } .legal-content { padding: 28px; } .popia-badge{flex-direction:column;text-align:center;} }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="legal-hero">
  <div class="container">
    <h1>POPIA Compliance</h1>
    <p>Our commitment to South Africa's Protection of Personal Information Act.</p>
  </div>
</div>

<section class="legal-wrap">
  <div class="container">
    <div class="legal-layout">
      <aside class="legal-toc">
        <h4>On This Page</h4>
        <a href="#overview">1. What is POPIA?</a>
        <a href="#principles">2. Our Compliance Principles</a>
        <a href="#lawful">3. Lawful Processing</a>
        <a href="#subject-rights">4. Your Rights as a Data Subject</a>
        <a href="#officer">5. Information Officer</a>
        <a href="#breach">6. Data Breach Notification</a>
        <a href="#cross-border">7. Cross-Border Data Transfers</a>
        <a href="#contact">8. Contact Our Information Officer</a>
      </aside>

      <div class="legal-content">
        <span class="legal-updated">Last updated: June 2026</span>

        <div class="popia-badge">
          <div class="icon">🇿🇦</div>
          <div>
            <h3>POPIA Compliant</h3>
            <p>ConsumerConnect RSA processes personal information in accordance with the Protection of Personal Information Act 4 of 2013.</p>
          </div>
        </div>

        <h2 id="overview">1. What is POPIA?</h2>
        <p>The Protection of Personal Information Act (POPIA) is South Africa's data protection law. It sets minimum requirements for how organisations may lawfully process the personal information of data subjects, and grants individuals specific rights over their own data.</p>

        <h2 id="principles">2. Our Compliance Principles</h2>
        <ul>
          <li><strong>Accountability</strong> — We take responsibility for the personal information we process.</li>
          <li><strong>Purpose Specification</strong> — We only collect information for clearly defined purposes related to operating the Platform.</li>
          <li><strong>Minimality</strong> — We only collect what is necessary to provide our services.</li>
          <li><strong>Security Safeguards</strong> — We apply technical measures (encryption, access control, audit logging) to protect your data.</li>
          <li><strong>Openness</strong> — This page and our Privacy Policy explain what we collect and why.</li>
        </ul>

        <h2 id="lawful">3. Lawful Processing</h2>
        <p>We process your personal information based on your consent at registration, the necessity of processing to perform our contract with you (facilitating listings and orders), and our legitimate interest in operating a secure, fraud-resistant marketplace.</p>

        <h2 id="subject-rights">4. Your Rights as a Data Subject</h2>
        <ul>
          <li>The right to be notified that your information is being collected</li>
          <li>The right to access the personal information we hold about you</li>
          <li>The right to request correction or deletion of inaccurate or unlawfully held information</li>
          <li>The right to object to processing of your personal information</li>
          <li>The right to lodge a complaint with the Information Regulator of South Africa</li>
        </ul>

        <h2 id="officer">5. Information Officer</h2>
        <p>ConsumerConnect RSA has designated an Information Officer responsible for ensuring compliance with POPIA. Requests relating to your personal information can be directed to our Information Officer via the Contact Us page.</p>

        <h2 id="breach">6. Data Breach Notification</h2>
        <p>In the event of a security compromise that affects your personal information, we will notify the Information Regulator and affected users as required under POPIA, as soon as reasonably possible after discovery.</p>

        <h2 id="cross-border">7. Cross-Border Data Transfers</h2>
        <p>Where any personal information is processed or stored outside South Africa (for example, via cloud hosting infrastructure), we ensure such transfers meet POPIA's requirements for adequate levels of protection.</p>

        <h2 id="contact">8. Contact Our Information Officer</h2>
        <p>To exercise any of your rights under POPIA, or to raise a concern about how your information is processed, please reach out via our <a href="contact-us.php" style="color:var(--green-mid);font-weight:600;">Contact Us</a> page and select "Account Help" or "General Enquiry" as your subject.</p>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
</body>
</html>
