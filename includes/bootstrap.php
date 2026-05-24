<?php
/**
 * Coal Industry Forum — Bootstrap
 * Includes config, database, session, helpers, i18n.
 */

declare(strict_types=1);

// --- Errors ---
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php-errors.log');
error_reporting(E_ALL);

// --- Paths ---
define('ROOT_DIR',     realpath(__DIR__ . '/..'));
define('INCLUDES_DIR', __DIR__);
define('UPLOAD_DIR',   ROOT_DIR . '/uploads');
define('PUBLIC_DIR',   ROOT_DIR . '/public');
define('ADMIN_DIR',    ROOT_DIR . '/admin');

// --- Config ---
$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    http_response_code(500);
    die('Configuration file not found. Copy includes/config.sample.php to includes/config.php and fill in credentials.');
}
$CONFIG = require $configFile;

if (!empty($CONFIG['debug'])) {
    ini_set('display_errors', '1');
}

// --- Autoload classes (PSR-4-ish, simple) ---
spl_autoload_register(function ($class) {
    $file = INCLUDES_DIR . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// --- Helpers (procedural) ---
require_once INCLUDES_DIR . '/helpers.php';

// --- DB ---
require_once INCLUDES_DIR . '/classes/DB.php';
DB::init($CONFIG['db']);

// --- Session ---
if (session_status() === PHP_SESSION_NONE) {
    session_name($CONFIG['session_name']);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($CONFIG['cookie_secure']),
        'httponly' => !empty($CONFIG['cookie_httponly']),
        'samesite' => $CONFIG['cookie_samesite'] ?? 'Lax',
    ]);
    session_start();
}

// --- CSRF token ---
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// --- Determine current language ---
$LANG = current_lang($CONFIG['languages'], $CONFIG['default_lang']);
