<?php
/** @var string $adminTitle */
$a = admin();
$f = flash();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= e($adminTitle) ?> · Coal Forum CMS</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='6' fill='%2307080a'/%3E%3Ccircle cx='20' cy='15' r='9' fill='none' stroke='%23c9a449' stroke-width='1.5'/%3E%3C/svg%3E">
<link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body>
<aside class="admin-sidebar">
  <a href="/admin/" class="admin-brand">
    <svg viewBox="0 0 40 40" width="32" height="32"><rect width="40" height="40" rx="6" fill="#0c0e12"/><circle cx="20" cy="15" r="9" fill="none" stroke="#c9a449" stroke-width="1.5"/><path d="M11.5 15h17 M14 11 A8 8 0 0 1 26 11 M14 19 A8 8 0 0 0 26 19" fill="none" stroke="#c9a449" stroke-width="1.2"/><path d="M8 27 L13 22 L18 24.5 L23 20 L29 25 L32 30 L8 30 Z" fill="#1b1f26"/></svg>
    <span>Coal Forum<br><small>Админ-панель</small></span>
  </a>
  <nav class="admin-nav">
    <div class="nav-group">
      <div class="nav-group-title">Главное</div>
      <a href="/admin/" class="<?= $current === 'index.php' ? 'is-active' : '' ?>">📊 Дашборд</a>
    </div>
    <div class="nav-group">
      <div class="nav-group-title">Контент</div>
      <a href="/admin/pages.php" class="<?= str_starts_with($current, 'pages') ? 'is-active' : '' ?>">📝 Страницы</a>
      <a href="/admin/blocks.php" class="<?= $current === 'blocks.php' ? 'is-active' : '' ?>">🧩 Блоки контента</a>
      <a href="/admin/news.php" class="<?= str_starts_with($current, 'news') ? 'is-active' : '' ?>">📰 Новости</a>
      <a href="/admin/leadership.php" class="<?= str_starts_with($current, 'leadership') ? 'is-active' : '' ?>">👔 Руководство</a>
    </div>
    <div class="nav-group">
      <div class="nav-group-title">Форум</div>
      <a href="/admin/years.php" class="<?= str_starts_with($current, 'years') ? 'is-active' : '' ?>">📅 Годы форума</a>
      <a href="/admin/program.php" class="<?= str_starts_with($current, 'program') ? 'is-active' : '' ?>">⏰ Программа</a>
      <a href="/admin/topics.php" class="<?= str_starts_with($current, 'topics') ? 'is-active' : '' ?>">💡 Ключевые темы</a>
      <a href="/admin/speakers.php" class="<?= str_starts_with($current, 'speakers') ? 'is-active' : '' ?>">🎤 Спикеры</a>
      <a href="/admin/partners.php" class="<?= str_starts_with($current, 'partners') ? 'is-active' : '' ?>">🤝 Партнёры</a>
      <a href="/admin/investor_packages.php" class="<?= str_starts_with($current, 'investor_pack') ? 'is-active' : '' ?>">💼 Пакеты инвесторов</a>
      <a href="/admin/downloads.php" class="<?= str_starts_with($current, 'downloads') ? 'is-active' : '' ?>">📄 Документы (PDF)</a>
      <a href="/admin/gallery.php" class="<?= str_starts_with($current, 'gallery') ? 'is-active' : '' ?>">🖼 Галерея годов</a>
    </div>
    <div class="nav-group">
      <div class="nav-group-title">Заявки</div>
      <a href="/admin/registrations.php" class="<?= str_starts_with($current, 'registr') ? 'is-active' : '' ?>">✅ Регистрации</a>
      <a href="/admin/investor_inquiries.php" class="<?= str_starts_with($current, 'investor_inq') ? 'is-active' : '' ?>">💰 Запросы инвесторов</a>
      <a href="/admin/messages.php" class="<?= $current === 'messages.php' ? 'is-active' : '' ?>">💬 Сообщения</a>
    </div>
    <div class="nav-group">
      <div class="nav-group-title">Настройки</div>
      <a href="/admin/settings.php" class="<?= $current === 'settings.php' ? 'is-active' : '' ?>">⚙️ Настройки сайта</a>
      <a href="/admin/seo.php" class="<?= $current === 'seo.php' ? 'is-active' : '' ?>">🔍 SEO</a>
      <a href="/admin/admins.php" class="<?= $current === 'admins.php' ? 'is-active' : '' ?>">👤 Администраторы</a>
      <a href="/admin/activity.php" class="<?= $current === 'activity.php' ? 'is-active' : '' ?>">📜 Журнал действий</a>
    </div>
  </nav>
  <div class="admin-user">
    <div class="user-info">
      <strong><?= e($a['full_name'] ?? 'Admin') ?></strong>
      <small><?= e($a['role'] ?? '') ?></small>
    </div>
    <a href="/" target="_blank" class="user-link" title="Открыть сайт">🌐</a>
    <a href="/admin/logout.php" class="user-link" title="Выйти">⏻</a>
  </div>
</aside>

<main class="admin-main">
  <header class="admin-header">
    <button class="admin-menu-toggle" onclick="document.body.classList.toggle('sidebar-open')">☰</button>
    <h1 class="admin-title"><?= e($adminTitle) ?></h1>
    <?php if (isset($showLangSwitcher) && $showLangSwitcher): ?>
    <div class="lang-pills">
      <?php foreach ($LANGS as $code => $name): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['el' => $code])) ?>" class="<?= $code === $EL ? 'is-active' : '' ?>"><?= strtoupper($code) ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </header>

  <?php if ($f): ?>
    <div class="flash flash--<?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
  <?php endif; ?>

  <div class="admin-content">
