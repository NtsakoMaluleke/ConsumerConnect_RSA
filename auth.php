<?php
// ============================================================
// ConsumerConnect RSA - Auth & Session Helpers
// ============================================================

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

// ─── Core auth ───────────────────────────────────────────────

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    static $user = null;
    if ($user === null) {
        $stmt = db()->prepare(
            'SELECT u.*, r.name AS role_name
             FROM users u JOIN roles r ON u.role_id = r.id
             WHERE u.id = ?'
        );
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

function hasRole(string ...$roles): bool {
    $user = currentUser();
    return $user && in_array($user['role_name'], $roles, true);
}

function requireLogin(string $redirect = '/login.php'): void {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . $redirect);
        exit;
    }
}

function requireRole(string $redirect = '/', string ...$roles): void {
    requireLogin();
    if (!hasRole(...$roles)) {
        header('Location: ' . SITE_URL . $redirect);
        exit;
    }
}

// ─── CSRF ────────────────────────────────────────────────────

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        die('CSRF verification failed.');
    }
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken()) . '">';
}

// ─── Flash messages ───────────────────────────────────────────

function flash(string $type, string $message): void {
    $_SESSION['flash'][] = compact('type', 'message');
}

function getFlashes(): array {
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

// ─── Password ─────────────────────────────────────────────────

function hashPassword(string $pw): string {
    return password_hash($pw, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword(string $pw, string $hash): bool {
    return password_verify($pw, $hash);
}

// ─── Audit log ────────────────────────────────────────────────

function auditLog(string $action, string $target = '', ?int $targetId = null, string $details = ''): void {
    $userId = $_SESSION['user_id'] ?? null;
    $ip     = $_SERVER['REMOTE_ADDR'] ?? '';
    db()->prepare(
        'INSERT INTO audit_log (user_id,action,target,target_id,details,ip_address)
         VALUES (?,?,?,?,?,?)'
    )->execute([$userId, $action, $target, $targetId, $details, $ip]);
}

// ─── Sanitize ─────────────────────────────────────────────────

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\-]+/', '-', $text);
    return trim($text, '-');
}
?>