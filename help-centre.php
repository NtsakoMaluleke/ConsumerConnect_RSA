<?php
require_once 'auth.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Help Centre — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
<style>
.help-hero { background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-mid) 100%); color: #fff; padding: clamp(40px, 8vw, 80px) 0; text-align: center; }
.help-hero h1 { font-size: 2.8rem; margin-bottom: 16px; }
.help-hero p { font-size: 1.1rem; opacity: .85; max-width: 600px; margin: 0 auto; }
.help-search { max-width: 520px; margin: 32px auto 0; display: flex; gap: 10px; }
.help-search input { flex: 1; padding: 14px 20px; border-radius: var(--radius-lg); border: none; font-size: 1rem; }
.help-section { padding: 64px 0; }
.help-cats { display: grid; grid-template-columns: repeat(3,1fr); gap: 28px; margin-top: 40px; }
.help-cat { background: var(--white); border-radius: var(--radius-lg); padding: 30px; box-shadow: var(--shadow-sm); transition: var(--transition); }
.help-cat:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
.help-cat .icon { width: 52px; height: 52px; background: var(--green-mid); border-radius: 50%; display:flex; align-items:center; justify-content:center; margin-bottom:16px; color:#fff; font-size:1.4rem; }
.help-cat h3 { margin-bottom: 10px; }
.help-cat p { color: var(--grey-400); font-size: .92rem; margin-bottom: 14px; }
.faq-wrap { max-width: 760px; margin: 48px auto 0; }
.faq-item { background: var(--white); border-radius: var(--radius-md); margin-bottom: 12px; box-shadow: var(--shadow-sm); overflow: hidden; }
.faq-q { padding: 18px 22px; font-weight: 700; cursor: pointer; display: flex; justify-content: space-between; align-items: center; color: var(--green-deep); }
.faq-q .chevron { transition: transform .2s; color: var(--green-mid); }
.faq-item.open .chevron { transform: rotate(180deg); }
.faq-a { padding: 0 22px; max-height: 0; overflow: hidden; transition: max-height .25s ease, padding .25s ease; color: var(--grey-700); font-size: .94rem; }
.faq-item.open .faq-a { padding: 0 22px 18px; max-height: 300px; }
@media(max-width:768px){ .help-cats { grid-template-columns: 1fr; } .help-hero h1 { font-size: 2rem; } }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="help-hero">
  <div class="container">
    <h1>Help Centre</h1>
    <p>Find answers to common questions about buying, selling, and staying safe on ConsumerConnect RSA.</p>
    <form class="help-search" onsubmit="event.preventDefault(); document.getElementById('faqWrap').scrollIntoView({behavior:'smooth'});">
      <input type="text" placeholder="Search for help…" id="helpSearchInput">
      <button type="submit" class="btn btn-gold">Search</button>
    </form>
  </div>
</div>

<section class="help-section">
  <div class="container">
    <div style="text-align:center;">
      <div class="eyebrow">Browse by Topic</div>
      <h2>How can we help?</h2>
    </div>
    <div class="help-cats">
      <div class="help-cat">
        <div class="icon">🛒</div>
        <h3>Buying</h3>
        <p>Learn how to search, message sellers, add to cart, and check out securely.</p>
        <a href="products.php" class="btn btn-outline" style="padding:8px 16px;font-size:.85rem;">Browse Products</a>
      </div>
      <div class="help-cat">
        <div class="icon">🏷️</div>
        <h3>Selling</h3>
        <p>Tips for creating great listings, pricing items fairly, and managing orders.</p>
        <a href="sell.php" class="btn btn-outline" style="padding:8px 16px;font-size:.85rem;">List an Item</a>
      </div>
      <div class="help-cat">
        <div class="icon">🛡️</div>
        <h3>Safety</h3>
        <p>Best practices for safe transactions, escrow payments, and avoiding scams.</p>
        <a href="safety-tips.php" class="btn btn-outline" style="padding:8px 16px;font-size:.85rem;">Safety Tips</a>
      </div>
    </div>

    <div class="faq-wrap" id="faqWrap">
      <div style="text-align:center;margin-bottom:8px;">
        <div class="eyebrow">Frequently Asked Questions</div>
      </div>
      <?php
      $faqs = [
        ["How do I create an account?", "Click Register in the top navigation bar, choose Buyer or Seller, and fill in your name, email, province, and password. You'll be logged in immediately after registering."],
        ["How does escrow payment work?", "When you place an order, your payment reference is generated and funds are held securely. The seller only receives payment once you confirm the item has been delivered."],
        ["How do I list an item for sale?", "Log in, click Sell in the navbar, and complete the listing form with a title, description, price, category, condition, and at least one photo."],
        ["What happens if a seller doesn't deliver my order?", "Contact the seller directly through in-platform messaging first. If unresolved, use the Contact Us page to report the issue to our support team."],
        ["Can I change or cancel an order after placing it?", "Orders can be cancelled while they're still in 'pending' status. Once a seller marks an order as paid or shipped, contact them directly via messaging to arrange a change."],
        ["Is my personal information safe?", "Yes. We only collect what's needed to run the platform and never sell your data. See our Privacy Policy and POPIA Compliance pages for full details."],
      ];
      foreach ($faqs as $i => $f):
      ?>
      <div class="faq-item" data-faq>
        <div class="faq-q"><span><?= h($f[0]) ?></span><span class="chevron">▾</span></div>
        <div class="faq-a"><p><?= h($f[1]) ?></p></div>
      </div>
      <?php endforeach; ?>
    </div>

    <div style="text-align:center;margin-top:48px;">
      <p style="color:var(--grey-400);margin-bottom:16px;">Still need help?</p>
      <a href="contact-us.php" class="btn btn-primary">Contact Our Support Team</a>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
<script>
document.querySelectorAll('[data-faq]').forEach(item => {
  item.querySelector('.faq-q').addEventListener('click', () => {
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('[data-faq]').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
  });
});
</script>
</body>
</html>
