<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/xml; charset=utf-8');

$base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'coalindustry.tj');
$langs = ['en','ru','tj'];

$urls = [];
$pages = DB::all('SELECT slug FROM pages');
foreach ($pages as $p) {
    foreach ($langs as $l) {
        $urls[] = ['loc' => $base . '/?p=' . $p['slug'] . '&lang=' . $l, 'lang' => $l, 'slug' => $p['slug']];
    }
}

// News
foreach (DB::all('SELECT id, published_at FROM news WHERE is_published = 1') as $n) {
    foreach ($langs as $l) {
        $urls[] = ['loc' => $base . '/?p=news-item&id=' . $n['id'] . '&lang=' . $l, 'lang' => $l, 'lastmod' => $n['published_at']];
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
foreach ($urls as $u) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($u['loc']) . "</loc>\n";
    if (!empty($u['lastmod'])) {
        echo "    <lastmod>" . date('c', strtotime($u['lastmod'])) . "</lastmod>\n";
    }
    foreach ($langs as $alt) {
        echo '    <xhtml:link rel="alternate" hreflang="' . $alt . '" href="' . htmlspecialchars(str_replace('lang=' . $u['lang'], 'lang=' . $alt, $u['loc'])) . '"/>' . "\n";
    }
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.8</priority>\n";
    echo "  </url>\n";
}
echo '</urlset>';
