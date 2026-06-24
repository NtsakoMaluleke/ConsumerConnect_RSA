<?php
require_once '../auth.php';
requireRole('/', 'super_admin');
$adminUser = currentUser();
$db = db();
$id = (int)($_GET['id'] ?? 0);

if ($id && $id !== (int)$adminUser['id']) {
    $db->prepare('DELETE FROM users WHERE id=?')->execute([$id]);
    auditLog('user_deleted','users',$id,"Deleted by {$adminUser['email']}");
    flash('success','User deleted successfully.');
} else {
    flash('danger','You cannot delete your own account.');
}
header('Location: users.php'); exit;
