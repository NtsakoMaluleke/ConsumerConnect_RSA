<?php
require_once 'auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    auditLog('logout', 'users', $_SESSION['user_id'] ?? null);
}
session_destroy();
header('Location: login.php');
exit;
?>