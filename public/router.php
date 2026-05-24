<?php
// Router for PHP built-in dev server.
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files: /uploads, /assets, /admin/assets
if (preg_match('#^/(uploads|assets|admin/assets)/#', $uri)) {
    if (strpos($uri, '/uploads/') === 0) {
        $path = __DIR__ . '/../uploads/' . substr($uri, 9);
    } elseif (strpos($uri, '/admin/assets/') === 0) {
        $path = __DIR__ . '/../admin/assets/' . substr($uri, 14);
    } else {
        $path = __DIR__ . $uri;
    }
    if (file_exists($path) && !is_dir($path)) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = ['css'=>'text/css','js'=>'application/javascript','png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','svg'=>'image/svg+xml','pdf'=>'application/pdf','webp'=>'image/webp','mp4'=>'video/mp4','woff2'=>'font/woff2'][$ext] ?? 'application/octet-stream';
        header('Content-Type: ' . $mime);
        readfile($path);
        return true;
    }
    return false;
}

// Admin
if (preg_match('#^/admin(/.*)?$#', $uri, $m)) {
    $sub = $m[1] ?? '';
    if ($sub === '' || $sub === '/') $adminPath = __DIR__ . '/../admin/index.php';
    else $adminPath = __DIR__ . '/../admin' . $sub;
    if (is_file($adminPath)) { require $adminPath; return true; }
    if (is_dir($adminPath) && is_file($adminPath . '/index.php')) { require $adminPath . '/index.php'; return true; }
    http_response_code(404);
    echo '404';
    return true;
}

// Default: public front controller
require __DIR__ . '/index.php';
