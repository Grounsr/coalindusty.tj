<?php
/**
 * Helpers — global functions used across the site.
 */
declare(strict_types=1);

// ---------- Language ----------
function current_lang(array $allowed, string $default): string
{
    // 1) ?lang= override (also stored to cookie)
    if (isset($_GET['lang']) && in_array($_GET['lang'], $allowed, true)) {
        setcookie('coalforum_lang', $_GET['lang'], time() + 60*60*24*365, '/');
        return $_GET['lang'];
    }
    // 2) cookie
    if (!empty($_COOKIE['coalforum_lang']) && in_array($_COOKIE['coalforum_lang'], $allowed, true)) {
        return $_COOKIE['coalforum_lang'];
    }
    // 3) Accept-Language
    $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    foreach (preg_split('/,/', $accept) as $chunk) {
        $code = strtolower(substr(trim($chunk), 0, 2));
        if ($code === 'tg') $code = 'tj';
        if (in_array($code, $allowed, true)) return $code;
    }
    return $default;
}

function lang_url(string $lang): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $parts = parse_url($uri);
    parse_str($parts['query'] ?? '', $q);
    $q['lang'] = $lang;
    return ($parts['path'] ?? '/') . '?' . http_build_query($q);
}

// ---------- Output ----------
function e(?string $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function clean_html(?string $v): string
{
    // Very light sanitization — preserves common tags, strips scripts.
    if ($v === null) return '';
    $v = preg_replace('#<(script|style|iframe|object|embed|form)[^>]*>.*?</\1>#is', '', $v);
    $v = preg_replace('#on\w+\s*=\s*"[^"]*"#i', '', $v);
    $v = preg_replace("#on\w+\s*=\s*'[^']*'#i", '', $v);
    $v = preg_replace('#javascript:#i', '', $v);
    return $v ?? '';
}

// ---------- CSRF ----------
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e($_SESSION['csrf'] ?? '') . '">';
}

function csrf_check(): bool
{
    return isset($_POST['_csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['_csrf']);
}

function csrf_required(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_check()) {
        http_response_code(419);
        die('CSRF token mismatch.');
    }
}

// ---------- Settings ----------
function setting(string $key, $default = null)
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (DB::all('SELECT `key_name`, `value` FROM `settings`') as $r) {
            $cache[$r['key_name']] = $r['value'];
        }
    }
    return $cache[$key] ?? $default;
}

function setting_i18n(string $key, string $lang, $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (DB::all('SELECT `key_name`,`lang`,`value` FROM `settings_i18n`') as $r) {
            $cache[$r['key_name']][$r['lang']] = $r['value'];
        }
    }
    return $cache[$key][$lang] ?? ($cache[$key]['en'] ?? $default);
}

// ---------- Content blocks ----------
function block(string $key, string $lang, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $rows = DB::all('
            SELECT cb.block_key, cbv.lang, cbv.value
            FROM content_blocks cb
            LEFT JOIN content_block_values cbv ON cbv.block_id = cb.id
        ');
        foreach ($rows as $r) {
            $cache[$r['block_key']][$r['lang']] = $r['value'];
        }
    }
    if (!isset($cache[$key])) return $default;
    if (isset($cache[$key][$lang]) && $cache[$key][$lang] !== null && $cache[$key][$lang] !== '') return $cache[$key][$lang];
    if (isset($cache[$key]['_']) && $cache[$key]['_'] !== null) return $cache[$key]['_'];
    if (isset($cache[$key]['en']) && $cache[$key]['en'] !== null) return $cache[$key]['en'];
    return $default;
}

// ---------- Pages ----------
function page(string $slug, string $lang): array
{
    $row = DB::row('
        SELECT p.id, p.slug,
               pt.title, pt.subtitle, pt.content,
               pt.meta_title, pt.meta_description, pt.meta_keywords
        FROM pages p
        LEFT JOIN page_translations pt ON pt.page_id = p.id AND pt.lang = ?
        WHERE p.slug = ?
        LIMIT 1
    ', [$lang, $slug]);

    if (!$row) return ['title' => '', 'subtitle' => '', 'content' => '', 'meta_title' => '', 'meta_description' => '', 'meta_keywords' => ''];

    if (empty($row['title'])) {
        $fallback = DB::row('SELECT title, subtitle, content, meta_title, meta_description, meta_keywords FROM page_translations WHERE page_id = (SELECT id FROM pages WHERE slug=?) AND lang = "en" LIMIT 1', [$slug]);
        if ($fallback) {
            foreach ($fallback as $k => $v) {
                if (empty($row[$k])) $row[$k] = $v;
            }
        }
    }
    return $row;
}

// ---------- Forum year ----------
function current_year(): ?array
{
    return DB::row('SELECT * FROM forum_years WHERE is_current = 1 LIMIT 1')
        ?? DB::row('SELECT * FROM forum_years ORDER BY year DESC LIMIT 1');
}

function year_t(int $yearId, string $lang): array
{
    $r = DB::row('SELECT * FROM forum_year_translations WHERE forum_year_id=? AND lang=?', [$yearId, $lang]);
    if (!$r) $r = DB::row('SELECT * FROM forum_year_translations WHERE forum_year_id=? AND lang="en"', [$yearId]);
    return $r ?: [];
}

// ---------- URL & files ----------
function asset(string $path): string
{
    return rtrim($path, '/');
}

function upload_url(string $path): string
{
    if (!$path) return '';
    if (strpos($path, '/') === 0) return $path;
    return '/uploads/' . ltrim($path, '/');
}

function format_bytes(int $bytes): string
{
    $units = ['B','KB','MB','GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) { $bytes /= 1024; $i++; }
    return round($bytes, 1) . ' ' . $units[$i];
}

// ---------- Translation strings (UI labels) ----------
function t(string $key, string $lang): string
{
    static $dict = null;
    if ($dict === null) $dict = require __DIR__ . '/translations.php';
    return $dict[$lang][$key] ?? $dict['en'][$key] ?? $key;
}

// ---------- Slug ----------
function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text) ?: strtolower($text);
    $text = preg_replace('~[^-a-z0-9]+~', '', $text);
    $text = trim($text, '-');
    return $text ?: 'item';
}

// ---------- Mailer ----------
function send_mail(string $to, string $subject, string $htmlBody, array $extraHeaders = []): bool
{
    require_once INCLUDES_DIR . '/classes/Mailer.php';
    return Mailer::send($to, $subject, $htmlBody, $extraHeaders);
}

// ---------- Active class for nav ----------
function nav_active(string $page): string
{
    $current = $_GET['p'] ?? 'home';
    return $current === $page ? 'is-active' : '';
}

// ---------- Country list (short) ----------
function countries_list(string $lang): array
{
    $list = require INCLUDES_DIR . '/countries.php';
    return $list[$lang] ?? $list['en'];
}

// ---------- Track page view ----------
function track_view(string $url, string $lang): void
{
    try {
        DB::query('INSERT INTO page_views (url, referrer, lang, ip_hash) VALUES (?,?,?,?)', [
            substr($url, 0, 500),
            substr($_SERVER['HTTP_REFERER'] ?? '', 0, 500),
            $lang,
            hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . date('Y-m-d')),
        ]);
    } catch (Throwable $e) { /* silent */ }
}
