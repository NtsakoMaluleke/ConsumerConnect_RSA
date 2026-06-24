<?php
require_once 'auth.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Safety Tips — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
<style>
.safety-hero { background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-mid) 100%); color: #fff; padding: clamp(40px, 8vw, 80px) 0; text-align: center; }
.safety-hero h1 { font-size: 2.8rem; margin-bottom: 16px; }
.safety-hero p { font-size: 1.1rem; opacity: .85; max-width: 620px; margin: 0 auto; }
.safety-section { padding: 64px 0; }
.safety-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 28px; margin-top: 40px; }
.safety-card { background: var(--white); border-radius: var(--radius-lg); padding: 30px; box-shadow: var(--shadow-sm); display:flex; gap:18px; }
.safety-card .icon { flex-shrink:0; width: 50px; height: 50px; background: var(--green-mid); border-radius: 50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.3rem; }
.safety-card h3 { margin-bottom: 8px; }
.safety-card p { color: var(--grey-400); font-size: .92rem; }
.escrow-banner { background: var(--green-deep); color:#fff; border-radius: var(--radius-lg); padding: 40px; margin-top: 56px; display:flex; align-items:center; gap: 28px; }
.escrow-banner .icon { font-size: 2.6rem; flex-shrink:0; }
.escrow-banner h3 { color: var(--gold); margin-bottom: 8px; }
.escrow-banner p { opacity:.88; }
.warning-box { background: #fff3cd; border-radius: var(--radius-lg); padding: 28px; margin-top: 32px; }
.warning-box h4 { color: #8a6d00; margin-bottom: 10px; }
.warning-box ul { margin: 0; padding-left: 20px; color: #5c4a00; font-size: .92rem; line-height:1.8; }
@media(max-width:768px){ .safety-grid { grid-template-columns: 1fr; } .safety-hero h1 { font-size: 2rem; } .escrow-banner { flex-direction:column; text-align:center; } }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="safety-hero">
  <div class="container">
    <h1>Safety Tips</h1>
    <p>Trade with confidence. Here's how ConsumerConnect RSA helps keep every transaction safe.</p>
  </div>
</div>

<section class="safety-section">
  <div class="container">
    <div style="text-align:center;">
      <div class="eyebrow">Best Practices</div>
      <h2>Protecting Yourself While Trading</h2>
    </div>

    <div class="safety-grid">
      <div class="safety-card">
        <div class="icon">🔒</div>
        <div><h3>Always Use Escrow Payments</h3><p>Never pay a seller directly outside the platform. Escrow holds your funds securely until you confirm delivery, protecting you from non-delivery scams.</p></div>
      </div>
      <div class="safety-card">
        <div class="icon">💬</div>
        <div><h3>Keep Conversations On-Platform</h3><p>Use in-app messaging to discuss orders. It creates a record we can refer to if a dispute arises and keeps your contact details private.</p></div>
      </div>
      <div class="safety-card">
        <div class="icon">⭐</div>
        <div><h3>Check Ratings & Reviews</h3><p>Before buying, review a seller's rating and past feedback from other buyers to gauge their reliability.</p></div>
      </div>
      <div class="safety-card">
        <div class="icon">📦</div>
        <div><h3>Confirm Before Releasing Funds</h3><p>Only confirm delivery once you've inspected the item and are satisfied. Escrow funds release to the seller immediately after confirmation.</p></div>
      </div>
      <div class="safety-card">
        <div class="icon">🚩</div>
        <div><h3>Report Suspicious Activity</h3><p>If a listing or message seems like a scam — unrealistic pricing, pressure to pay off-platform — report it via Contact Us immediately.</p></div>
      </div>
      <div class="safety-card">
        <div class="icon">🧾</div>
        <div><h3>Verify Product Condition</h3><p>Read the full description and condition rating (New / Used / Refurbished) and ask the seller questions before purchasing.</p></div>
      </div>
    </div>

    <div class="escrow-banner">
      <div class="icon">🛡️</div>
      <div>
        <h3>How Escrow Protects You</h3>
        <p>When you check out, your payment is held securely by ConsumerConnect RSA rather than sent directly to the seller. The seller only receives payment once you've received your item and confirmed it on your Orders page. This removes the single biggest risk in informal C2C trade — paying for something that never arrives.</p>
      </div>
    </div>

    <div class="warning-box">
      <h4>⚠️ Warning Signs of a Scam</h4>
      <ul>
        <li>A seller insists on payment via EFT or cash outside the platform's escrow system</li>
        <li>Prices that are far below market value with pressure to "act fast"</li>
        <li>Requests to move the conversation to WhatsApp or another app before a sale</li>
        <li>Refusal to answer questions about the item's condition or origin</li>
        <li>Requests for in-person meetups in unsafe or unfamiliar locations</li>
      </ul>
    </div>

    <div style="text-align:center;margin-top:48px;">
      <a href="contact-us.php" class="btn btn-primary">Report a Concern</a>
      <a href="help-centre.php" class="btn btn-outline" style="margin-left:12px;">Back to Help Centre</a>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
</body>
</html>
