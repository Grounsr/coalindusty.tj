<?php
/**
 * Admin bootstrap — common loader for every admin page.
 * Requires auth except for login page.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$publicPages = ['login.php', 'logout.php', 'install.php'];
// Derive current script from REQUEST_URI (more reliable under router-based dev server / Apache rewrites)
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$current = basename($uriPath);
if ($current === '' || $current === 'admin' || !str_ends_with($current, '.php')) {
    $current = 'index.php';
}

if (!in_array($current, $publicPages, true) && empty($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

function admin(): ?array
{
    static $a = null;
    if ($a === null && !empty($_SESSION['admin_id'])) {
        $a = DB::row('SELECT id, username, email, full_name, role FROM admins WHERE id = ?', [$_SESSION['admin_id']]);
    }
    return $a;
}

function log_action(string $action, ?string $entityType = null, ?int $entityId = null, ?string $details = null): void
{
    try {
        DB::insert('activity_log', [
            'admin_id'    => $_SESSION['admin_id'] ?? null,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'details'     => $details,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    } catch (Throwable $e) { /* silent */ }
}

function admin_url(string $page, array $params = []): string
{
    $qs = $params ? '?' . http_build_query($params) : '';
    return '/admin/' . $page . $qs;
}

function flash(?string $type = null, ?string $msg = null)
{
    if ($type !== null) {
        $_SESSION['_flash'] = ['type' => $type, 'msg' => $msg];
        return null;
    }
    if (!empty($_SESSION['_flash'])) {
        $f = $_SESSION['_flash'];
        unset($_SESSION['_flash']);
        return $f;
    }
    return null;
}

// Common page title
$adminTitle = $adminTitle ?? 'Админ-панель';

// Languages for content editing
$LANGS = ['en' => 'English', 'ru' => 'Русский', 'tj' => 'Тоҷикӣ'];

// Editing language preference (cookie)
$EL = $_GET['el'] ?? $_COOKIE['admin_el'] ?? 'ru';
if (!array_key_exists($EL, $LANGS)) $EL = 'ru';
if (!empty($_GET['el'])) setcookie('admin_el', $EL, time()+86400*30, '/admin');
