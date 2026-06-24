<?php
require_once 'auth.php';
$user = currentUser();
if (!$user) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}
$cats = db()->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();
$provinces = ['Eastern Cape','Free State','Gauteng','KwaZulu-Natal','Limpopo','Mpumalanga','Northern Cape','North West','Western Cape'];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $condition   = $_POST['condition_type'] ?? 'new';
    $province    = $_POST['province'] ?? '';

    if (!$title)       $errors[] = 'Title is required.';
    if (!$description) $errors[] = 'Description is required.';
    if ($price <= 0)   $errors[] = 'Price must be greater than 0.';
    if (!$category_id) $errors[] = 'Please select a category.';
    if (!$province)    $errors[] = 'Please select a province.';

    if (empty($errors)) {
        $stmt = db()->prepare(
            "INSERT INTO products (seller_id, category_id, title, description, price, condition_type, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())"
        );
        $stmt->execute([$user['id'], $category_id, $title, $description, $price, $condition]);
        $productId = db()->lastInsertId();

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $ext      = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed  = ['jpg','jpeg','png','webp','gif'];
            if (in_array($ext, $allowed)) {
                $filename = 'prod-' . $productId . '-' . time() . '.' . $ext;
                $dest     = __DIR__ . '/uploads/' . $filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    db()->prepare("INSERT INTO product_images (product_id, filename, is_primary, sort_order) VALUES (?,?,1,0)")
                        ->execute([$productId, $filename]);
                }
            }
        }
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sell a Product — ConsumerConnect RSA</title>
<link rel="stylesheet" href="css/style.css">
<style>
.sell-wrap { max-width: 720px; margin: 48px auto; background: var(--white); border-radius: var(--radius-lg); padding: 40px; box-shadow: var(--shadow-md); }
.sell-wrap h1 { font-size: 1.8rem; margin-bottom: 8px; }
.sell-wrap p.sub { color: var(--grey-400); margin-bottom: 32px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; }
.form-group label { font-size: .85rem; font-weight: 600; color: var(--grey-700); }
.form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1.5px solid var(--grey-200); border-radius: var(--radius-sm); font-size: .95rem; font-family: inherit; transition: border-color .2s; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--green-mid); }
.form-group textarea { resize: vertical; min-height: 120px; }
.alert-danger { background: #fdecea; border: 1px solid #f5c6cb; color: var(--danger); padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 20px; }
.alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 20px; }
@media(max-width:600px){ .form-row { grid-template-columns: 1fr; } .sell-wrap { padding: 24px; margin: 24px 16px; } }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
  <div class="sell-wrap">
    <h1>List a Product</h1>
    <p class="sub">Fill in the details below to list your item for sale across South Africa.</p>

    <?php if ($success): ?>
    <div class="alert-success">
      ✅ Your product has been listed successfully! <a href="products.php">View listings →</a>
    </div>
    <?php endif; ?>

    <?php if ($errors): ?>
    <div class="alert-danger">
      <?php foreach ($errors as $e): ?><div>• <?= h($e) ?></div><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label>Product Title</label>
        <input type="text" name="title" placeholder="e.g. iPhone 13 Pro 256GB Space Grey" value="<?= h($_POST['title'] ?? '') ?>" required>
      </div>

      <div class="sell-form-row">
        <div class="form-group">
          <label>Category</label>
          <select name="category_id" required>
            <option value="">Select category…</option>
            <?php foreach ($cats as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($_POST['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= h($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Province</label>
          <select name="province" required>
            <option value="">Select province…</option>
            <?php foreach ($provinces as $p): ?>
            <option value="<?= h($p) ?>" <?= ($_POST['province'] ?? '') === $p ? 'selected' : '' ?>><?= h($p) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="sell-form-row">
        <div class="form-group">
          <label>Price (R)</label>
          <input type="number" name="price" placeholder="0.00" step="0.01" min="0" value="<?= h($_POST['price'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Condition</label>
          <select name="condition_type">
            <option value="new" <?= ($_POST['condition_type'] ?? '') === 'new' ? 'selected' : '' ?>>New</option>
            <option value="used" <?= ($_POST['condition_type'] ?? '') === 'used' ? 'selected' : '' ?>>Used</option>
            <option value="refurbished" <?= ($_POST['condition_type'] ?? '') === 'refurbished' ? 'selected' : '' ?>>Refurbished</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description" placeholder="Describe your item — condition, specs, reason for selling…" required><?= h($_POST['description'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Product Image</label>
        <input type="file" name="image" accept="image/*">
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:14px;">List Product →</button>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>
<div class="toast-container" id="toastContainer"></div>
<script src="js/main.js"></script>
</body>
</html>
