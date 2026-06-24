<?php
require_once '../auth.php';
header('Content-Type: application/json');
$ids = array_filter(array_map('intval', explode(',', $_GET['ids'] ?? '')));
if (empty($ids)) { echo '[]'; exit; }
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = db()->prepare("SELECT p.id, p.title, p.price, c.name AS cat_name,
  (SELECT filename FROM product_images WHERE product_id=p.id AND is_primary=1 LIMIT 1) AS img
  FROM products p LEFT JOIN categories c ON c.id = p.category_id
  WHERE p.id IN ($placeholders) AND p.status='active'");
$stmt->execute(array_values($ids));
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
