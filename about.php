<?php
require_once 'auth.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>About Us — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
<style>
.about-hero { background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-mid) 100%); color: #fff; padding: clamp(40px, 8vw, 80px) 0; text-align: center; }
.about-hero h1 { font-size: 2.8rem; margin-bottom: 16px; }
.about-hero p { font-size: 1.15rem; opacity: .85; max-width: 600px; margin: 0 auto; }
.about-section { padding: 64px 0; }
.about-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 32px; margin-top: 48px; }
.about-card { background: var(--white); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm); text-align: center; }
.about-card .icon { width: 56px; height: 56px; background: var(--green-mid); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: #fff; }
.about-card h3 { margin-bottom: 10px; }
.about-card p { color: var(--grey-400); font-size: .95rem; }
.team-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap: 24px; margin-top: 40px; }
.team-card { background: var(--white); border-radius: var(--radius-md); padding: 24px; box-shadow: var(--shadow-sm); text-align: center; }
.team-avatar { width: 72px; height: 72px; border-radius: 50%; background: var(--green-light); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; color: #fff; margin: 0 auto 12px; }
.team-card h4 { margin-bottom: 4px; }
.team-card span { font-size: .85rem; color: var(--grey-400); }
@media(max-width:768px){ .about-grid { grid-template-columns: 1fr; } .about-hero h1 { font-size: 2rem; } }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="about-hero">
  <div class="container">
    <h1>About ConsumerConnect RSA</h1>
    <p>South Africa's trusted marketplace — connecting buyers and sellers across all 9 provinces.</p>
  </div>
</div>

<section class="about-section">
  <div class="container">
    <div style="max-width:700px;margin:0 auto;text-align:center;">
      <div class="eyebrow">Our Mission</div>
      <h2>Making Trade Simple for Every South African</h2>
      <p style="color:var(--grey-400);margin-top:16px;font-size:1.05rem;">
        ConsumerConnect RSA was built to give every South African — from Cape Town to Limpopo — a safe, simple platform to buy and sell goods. Whether you're clearing out your home or growing a small business, we're here to help.
      </p>
    </div>

    <div class="about-grid">
      <div class="about-card">
        <div class="icon">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3>Safe & Secure</h3>
        <p>Every listing is reviewed and all transactions are protected. Shop with confidence.</p>
      </div>
      <div class="about-card">
        <div class="icon">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <h3>All 9 Provinces</h3>
        <p>From Gauteng to the Western Cape — buy and sell locally or nationwide.</p>
      </div>
      <div class="about-card">
        <div class="icon">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <h3>Free to List</h3>
        <p>Listing your products is completely free. No hidden fees, no monthly charges.</p>
      </div>
    </div>
  </div>
</section>

<section class="about-section" style="background:var(--grey-100);padding:64px 0;">
  <div class="container">
    <div style="text-align:center;">
      <div class="eyebrow">The Team</div>
      <h2>Built by South Africans, for South Africans</h2>
    </div>
    <div class="team-grid">
      <div class="team-card"><div class="team-avatar">T</div><h4>Team Member</h4><span>Developer</span></div>
      <div class="team-card"><div class="team-avatar">C</div><h4>Team Member</h4><span>Designer</span></div>
      <div class="team-card"><div class="team-avatar">S</div><h4>Team Member</h4><span>Backend</span></div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
</body>
</html>
