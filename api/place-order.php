<?php
require_once '../auth.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
if (!$body) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request body']);
    exit;
}

$userId        = $_SESSION['user_id'];
$cart          = $body['cart']           ?? [];
$paymentMethod = $body['payment_method'] ?? 'card';
$shippingAddr  = $body['shipping_addr']  ?? '';

if (empty($cart)) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart is empty']);
    exit;
}

// Fetch product prices from DB (never trust client-side prices)
$ids          = array_map('intval', array_keys($cart));
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = db()->prepare("
    SELECT p.id, p.title, p.price, p.seller_id
    FROM products p
    WHERE p.id IN ($placeholders) AND p.status = 'active'
");
$stmt->execute($ids);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($products)) {
    http_response_code(400);
    echo json_encode(['error' => 'No valid products found']);
    exit;
}

// Calculate real total
$total = 0;
foreach ($products as $p) {
    $qty    = max(1, (int)($cart[$p['id']] ?? 1));
    $total += $p['price'] * $qty;
}

$paymentRef = 'CC-' . strtoupper(bin2hex(random_bytes(5)));

try {
    db()->beginTransaction();

    // Insert order
    $stmt = db()->prepare("
        INSERT INTO orders (buyer_id, total_amount, status, payment_method, payment_ref, shipping_addr, created_at)
        VALUES (?, ?, 'pending', ?, ?, ?, NOW())
    ");
    $stmt->execute([$userId, $total, $paymentMethod, $paymentRef, $shippingAddr]);
    $orderId = db()->lastInsertId();

    // Insert order items
    $itemStmt = db()->prepare("
        INSERT INTO order_items (order_id, product_id, seller_id, quantity, unit_price)
        VALUES (?, ?, ?, ?, ?)
    ");
    foreach ($products as $p) {
        $qty = max(1, (int)($cart[$p['id']] ?? 1));
        $itemStmt->execute([$orderId, $p['id'], $p['seller_id'], $qty, $p['price']]);
    }

    db()->commit();

    echo json_encode([
        'success'  => true,
        'order_id' => $orderId,
        'ref'      => $paymentRef,
        'total'    => $total,
    ]);

} catch (Exception $e) {
    db()->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save order: ' . $e->getMessage()]);
}
