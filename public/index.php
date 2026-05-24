<?php
/**
 * Coal Industry Forum — Public Front Controller
 * All public pages routed through ?p=<slug>.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$page = $_GET['p'] ?? 'home';
$allowed = ['home','about','program','speakers','investors','news','news-item','archive','register','contacts','download'];
if (!in_array($page, $allowed, true)) {
    http_response_code(404);
    $page = '404';
}

// POST handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action) {
        $handler = INCLUDES_DIR . '/../public/handlers/' . preg_replace('/[^a-z_]/i','',$action) . '.php';
        if (file_exists($handler)) {
            csrf_required();
            require $handler;
            exit;
        }
    }
}

// Track view
track_view($_SERVER['REQUEST_URI'] ?? '/', $LANG);

// Special: file download
if ($page === 'download') {
    $id = (int)($_GET['id'] ?? 0);
    $dl = DB::row('SELECT * FROM downloads WHERE id = ?', [$id]);
    if ($dl && file_exists(ROOT_DIR . $dl['file_path'])) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($dl['original_name'] ?: $dl['file_path']) . '"');
        header('Content-Length: ' . filesize(ROOT_DIR . $dl['file_path']));
        readfile(ROOT_DIR . $dl['file_path']);
        exit;
    }
    http_response_code(404);
    exit('File not found');
}

// Render layout + page
$pageFile = __DIR__ . '/pages/' . $page . '.php';
if (!file_exists($pageFile)) {
    http_response_code(404);
    $pageFile = __DIR__ . '/pages/404.php';
}

// Load page meta from DB if defined
$pageMeta = ['title' => '', 'subtitle' => '', 'meta_title' => '', 'meta_description' => '', 'meta_keywords' => ''];
$slugMap = ['home'=>'home','about'=>'about','program'=>'program','speakers'=>'speakers','investors'=>'investors','news'=>'news','news-item'=>'news','archive'=>'archive','register'=>'register','contacts'=>'contacts'];
if (isset($slugMap[$page])) {
    $pageMeta = page($slugMap[$page], $LANG) + $pageMeta;
}

require __DIR__ . '/layout/header.php';
require $pageFile;
require __DIR__ . '/layout/footer.php';
