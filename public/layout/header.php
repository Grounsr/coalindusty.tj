<?php
/** @var string $LANG */
/** @var array $pageMeta */
/** @var string $page */

$siteName = setting_i18n('site_name', $LANG);
$siteTagline = setting_i18n('site_tagline', $LANG);

$title = !empty($pageMeta['meta_title']) ? $pageMeta['meta_title'] : ($pageMeta['title'] ?: $siteName);
$description = $pageMeta['meta_description'] ?? $siteTagline;
$keywords = $pageMeta['meta_keywords'] ?? '';
$canonical = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'coalindustry.tj') . parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$currentYear = current_year();
$forumDate = $currentYear['event_date'] ?? '2026-11-25';
?>
<!DOCTYPE html>
<html lang="<?= e($LANG) ?>" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<?php if ($keywords): ?><meta name="keywords" content="<?= e($keywords) ?>"><?php endif; ?>

<!-- Canonical & hreflang -->
<link rel="canonical" href="<?= e($canonical) ?>">
<?php foreach (['en','ru','tj'] as $l): $url = $canonical . '?lang=' . $l; ?>
<link rel="alternate" hreflang="<?= $l ?>" href="<?= e($url) ?>">
<?php endforeach; ?>
<link rel="alternate" hreflang="x-default" href="<?= e($canonical) ?>?lang=en">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e($siteName) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:locale" content="<?= $LANG === 'tj' ? 'tg_TJ' : ($LANG === 'ru' ? 'ru_RU' : 'en_US') ?>">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($title) ?>">
<meta name="twitter:description" content="<?= e($description) ?>">

<!-- Theme color -->
<meta name="theme-color" content="#07080a">

<!-- Favicon (inline SVG) -->
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='4' fill='%2307080a'/%3E%3Ccircle cx='20' cy='15' r='9' fill='none' stroke='%23c9a449' stroke-width='1.5'/%3E%3Cpath d='M11.5 15h17 M14 11 A8 8 0 0 1 26 11 M14 19 A8 8 0 0 0 26 19' fill='none' stroke='%23c9a449' stroke-width='1.2'/%3E%3Cpath d='M8 27 L13 22 L18 24.5 L23 20 L29 25 L32 30 L8 30 Z' fill='%231b1f26'/%3E%3C/svg%3E">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

<!-- Styles -->
<link rel="stylesheet" href="/assets/css/style.css">

<!-- JSON-LD: Event -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": <?= json_encode($siteName . ' ' . ($currentYear['year'] ?? '')) ?>,
  "startDate": "<?= $forumDate ?>T08:00:00+05:00",
  "endDate": "<?= $forumDate ?>T21:30:00+05:00",
  "eventStatus": "https://schema.org/EventScheduled",
  "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
  "location": {
    "@type": "Place",
    "name": <?= json_encode(setting_i18n('site_venue', $LANG)) ?>,
    "address": <?= json_encode(setting_i18n('site_address', $LANG)) ?>
  },
  "organizer": {
    "@type": "Organization",
    "name": "Ministry of Industry and New Technologies of the Republic of Tajikistan",
    "url": "https://coalindustry.tj"
  },
  "description": <?= json_encode($description) ?>,
  "url": "<?= e($canonical) ?>"
}
</script>
</head>
<body data-page="<?= e($page) ?>" data-forum-date="<?= e($forumDate) ?>" data-lang="<?= e($LANG) ?>">

<a href="#main" class="skip-link"><?= e(t('a11y.skip', $LANG)) ?></a>

<header class="site-header" id="siteHeader" role="banner">
  <div class="header-inner">
    <a href="/?lang=<?= e($LANG) ?>" class="logo" aria-label="Coal Industry Forum">
      <svg class="logo-icon" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <rect width="40" height="40" rx="6" fill="#0c0e12"/>
        <circle cx="20" cy="15" r="9" fill="none" stroke="#c9a449" stroke-width="1.5"/>
        <path d="M11.5 15h17 M14 11 A8 8 0 0 1 26 11 M14 19 A8 8 0 0 0 26 19" fill="none" stroke="#c9a449" stroke-width="1.2"/>
        <path d="M8 27 L13 22 L18 24.5 L23 20 L29 25 L32 30 L8 30 Z" fill="#1b1f26"/>
        <path d="M13 22 L18 24.5 L21 22.5 L17 21 Z" fill="#2b313b"/>
      </svg>
      <span class="logo-text">
        Coal Forum
        <span><?= e($siteTagline) ?></span>
      </span>
    </a>

    <nav class="main-nav" aria-label="Primary">
      <ul>
        <li><a href="/?p=home&lang=<?= e($LANG) ?>" class="<?= nav_active('home') ?>"><?= e(t('nav.home', $LANG)) ?></a></li>
        <li><a href="/?p=about&lang=<?= e($LANG) ?>" class="<?= nav_active('about') ?>"><?= e(t('nav.about', $LANG)) ?></a></li>
        <li><a href="/?p=program&lang=<?= e($LANG) ?>" class="<?= nav_active('program') ?>"><?= e(t('nav.program', $LANG)) ?></a></li>
        <li><a href="/?p=speakers&lang=<?= e($LANG) ?>" class="<?= nav_active('speakers') ?>"><?= e(t('nav.speakers', $LANG)) ?></a></li>
        <li><a href="/?p=investors&lang=<?= e($LANG) ?>" class="<?= nav_active('investors') ?>"><?= e(t('nav.investors', $LANG)) ?></a></li>
        <li><a href="/?p=news&lang=<?= e($LANG) ?>" class="<?= nav_active('news') ?>"><?= e(t('nav.news', $LANG)) ?></a></li>
        <li><a href="/?p=archive&lang=<?= e($LANG) ?>" class="<?= nav_active('archive') ?>"><?= e(t('nav.archive', $LANG)) ?></a></li>
        <li><a href="/?p=contacts&lang=<?= e($LANG) ?>" class="<?= nav_active('contacts') ?>"><?= e(t('nav.contacts', $LANG)) ?></a></li>
      </ul>
      <a href="/?p=register&lang=<?= e($LANG) ?>" class="btn btn--primary btn--sm nav-cta"><?= e(t('nav.register', $LANG)) ?></a>
      <div class="lang-switcher">
        <?php foreach (['en','ru','tj'] as $l): ?>
        <a href="<?= e(lang_url($l)) ?>" class="lang-btn <?= $l === $LANG ? 'is-active' : '' ?>"><?= strtoupper($l) ?></a>
        <?php endforeach; ?>
      </div>
    </nav>

    <button class="menu-toggle" id="menuToggle" aria-label="<?= e(t('a11y.menu', $LANG)) ?>" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<div class="mobile-menu" id="mobileMenu" role="dialog" aria-label="Mobile navigation">
  <div class="mobile-menu-inner">
    <ul>
      <li><a href="/?p=home&lang=<?= e($LANG) ?>" class="<?= nav_active('home') ?>"><?= e(t('nav.home', $LANG)) ?></a></li>
      <li><a href="/?p=about&lang=<?= e($LANG) ?>" class="<?= nav_active('about') ?>"><?= e(t('nav.about', $LANG)) ?></a></li>
      <li><a href="/?p=program&lang=<?= e($LANG) ?>" class="<?= nav_active('program') ?>"><?= e(t('nav.program', $LANG)) ?></a></li>
      <li><a href="/?p=speakers&lang=<?= e($LANG) ?>" class="<?= nav_active('speakers') ?>"><?= e(t('nav.speakers', $LANG)) ?></a></li>
      <li><a href="/?p=investors&lang=<?= e($LANG) ?>" class="<?= nav_active('investors') ?>"><?= e(t('nav.investors', $LANG)) ?></a></li>
      <li><a href="/?p=news&lang=<?= e($LANG) ?>" class="<?= nav_active('news') ?>"><?= e(t('nav.news', $LANG)) ?></a></li>
      <li><a href="/?p=archive&lang=<?= e($LANG) ?>" class="<?= nav_active('archive') ?>"><?= e(t('nav.archive', $LANG)) ?></a></li>
      <li><a href="/?p=contacts&lang=<?= e($LANG) ?>" class="<?= nav_active('contacts') ?>"><?= e(t('nav.contacts', $LANG)) ?></a></li>
    </ul>
    <a href="/?p=register&lang=<?= e($LANG) ?>" class="btn btn--primary btn--block nav-cta"><?= e(t('nav.register', $LANG)) ?></a>
    <div class="mobile-lang-switcher">
      <?php foreach (['en','ru','tj'] as $l): ?>
      <a href="<?= e(lang_url($l)) ?>" class="lang-btn <?= $l === $LANG ? 'is-active' : '' ?>"><?= strtoupper($l) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<main id="main">
