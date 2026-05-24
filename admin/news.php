<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Новости';
$showLangSwitcher = true;

$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id) {
    csrf_required();
    $n = DB::row('SELECT cover_image FROM news WHERE id=?', [$id]);
    if ($n) delete_file_safe($n['cover_image']);
    DB::delete('news', 'id = ?', [$id]);
    log_action('delete_news', 'news', $id);
    flash('success', 'Новость удалена');
    header('Location: /admin/news.php'); exit;
}

if ($action === 'toggle' && $id) {
    csrf_required();
    DB::query('UPDATE news SET is_published = 1 - is_published, published_at = IF(is_published=0 AND published_at IS NULL, NOW(), published_at) WHERE id=?', [$id]);
    log_action('toggle_news', 'news', $id);
    header('Location: /admin/news.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create','edit'], true)) {
    csrf_required();
    $slug = trim($_POST['slug'] ?? '') ?: slugify(trim($_POST['title']['en'] ?? trim($_POST['title']['ru'] ?? 'news')));

    $cover = admin_upload('cover_image', 'news') ?? ($_POST['cover_existing'] ?? null);

    $data = [
        'slug'         => $slug,
        'cover_image'  => $cover,
        'is_published' => !empty($_POST['is_published']) ? 1 : 0,
        'published_at' => !empty($_POST['published_at']) ? $_POST['published_at'] : date('Y-m-d H:i:s'),
        'forum_year_id'=> (int)($_POST['forum_year_id'] ?? 0) ?: null,
    ];

    if ($action === 'create') {
        $id = DB::insert('news', $data);
        log_action('create_news', 'news', $id);
    } else {
        DB::update('news', $data, 'id=:id', ['id'=>$id]);
        log_action('update_news', 'news', $id);
    }

    foreach (['en','ru','tj'] as $lang) {
        $td = [
            'title'   => trim($_POST['title'][$lang] ?? ''),
            'excerpt' => trim($_POST['excerpt'][$lang] ?? ''),
            'body'    => $_POST['body'][$lang] ?? '',
        ];
        if (!$td['title']) continue;
        $exists = DB::value('SELECT news_id FROM news_translations WHERE news_id=? AND lang=?', [$id, $lang]);
        if ($exists) {
            DB::update('news_translations', $td, 'news_id=:n AND lang=:l', ['n'=>$id,'l'=>$lang]);
        } else {
            DB::insert('news_translations', array_merge($td, ['news_id'=>$id,'lang'=>$lang]));
        }
    }

    flash('success', $action === 'create' ? 'Новость создана' : 'Новость обновлена');
    header('Location: /admin/news.php?a=edit&id=' . $id . '&el=' . $EL);
    exit;
}

if ($action === 'edit' && $id) {
    $news = DB::row('SELECT * FROM news WHERE id=?', [$id]);
    $tr = [];
    foreach (DB::all('SELECT * FROM news_translations WHERE news_id=?', [$id]) as $t) $tr[$t['lang']] = $t;
}

$years = DB::all('SELECT id, year FROM forum_years ORDER BY year DESC');

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header">
    <h2>Все новости</h2>
    <a href="/admin/news.php?a=create" class="btn btn--primary">+ Создать новость</a>
  </div>
  <?php $list = DB::all('SELECT n.*, nt.title FROM news n LEFT JOIN news_translations nt ON nt.news_id=n.id AND nt.lang="ru" ORDER BY n.published_at DESC'); ?>
  <?php if (!$list): ?>
    <div class="empty">Новостей нет. Создайте первую.</div>
  <?php else: ?>
  <table class="data">
    <thead><tr><th>Обложка</th><th>Заголовок (RU)</th><th>Slug</th><th>Дата публ.</th><th>Статус</th><th>Просм.</th><th class="actions">Действия</th></tr></thead>
    <tbody>
    <?php foreach ($list as $n): ?>
    <tr>
      <td><div class="thumb"><?php if ($n['cover_image']): ?><img src="<?= e($n['cover_image']) ?>" alt=""><?php endif; ?></div></td>
      <td><strong><?= e($n['title'] ?? '—') ?></strong></td>
      <td><code style="font-size:11px;color:var(--ink-3);"><?= e($n['slug']) ?></code></td>
      <td><?= date('d.m.Y', strtotime($n['published_at'] ?? $n['created_at'])) ?></td>
      <td>
        <?php if ($n['is_published']): ?><span class="badge badge--ok">опубликовано</span><?php else: ?><span class="badge badge--warn">черновик</span><?php endif; ?>
      </td>
      <td><?= (int)$n['views'] ?></td>
      <td class="actions">
        <a href="/?p=news-item&id=<?= (int)$n['id'] ?>&lang=ru" target="_blank" class="btn btn--sm">👁</a>
        <a href="/admin/news.php?a=edit&id=<?= (int)$n['id'] ?>" class="btn btn--sm btn--primary">✎</a>
        <a href="/admin/news.php?a=toggle&id=<?= (int)$n['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm" onclick="return confirm('Переключить публикацию?')"><?= $n['is_published'] ? '⏸' : '▶' ?></a>
        <a href="/admin/news.php?a=delete&id=<?= (int)$n['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить новость?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php else: $isEdit = $action === 'edit'; ?>
<div class="card">
  <div class="card-header">
    <h2><?= $isEdit ? 'Редактирование новости' : 'Новая новость' ?></h2>
    <a href="/admin/news.php" class="btn btn--sm">← Список</a>
  </div>

  <form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="field-row">
      <div class="field">
        <label>Дата публикации</label>
        <input type="datetime-local" name="published_at" class="input"
               value="<?= e(date('Y-m-d\TH:i', strtotime($news['published_at'] ?? 'now'))) ?>">
      </div>
      <div class="field">
        <label>Slug (URL)</label>
        <input type="text" name="slug" class="input" value="<?= e($news['slug'] ?? '') ?>" placeholder="оставьте пустым для авто">
      </div>
    </div>

    <div class="field-row">
      <div class="field">
        <label>Привязка к году форума</label>
        <select name="forum_year_id" class="select">
          <option value="">—</option>
          <?php foreach ($years as $y): ?>
          <option value="<?= (int)$y['id'] ?>" <?= ($news['forum_year_id'] ?? 0) == $y['id'] ? 'selected' : '' ?>><?= (int)$y['year'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field" style="justify-content:flex-end;">
        <label class="check"><input type="checkbox" name="is_published" value="1" <?= !empty($news['is_published']) ? 'checked' : '' ?>> Опубликовать</label>
      </div>
    </div>

    <div class="field">
      <label>Обложка (1600×900 рекомендуется)</label>
      <?php if (!empty($news['cover_image'])): ?>
        <div class="thumb" style="width:200px;height:auto;aspect-ratio:16/9;margin-bottom:8px;"><img src="<?= e($news['cover_image']) ?>"></div>
        <input type="hidden" name="cover_existing" value="<?= e($news['cover_image']) ?>">
      <?php endif; ?>
      <input type="file" name="cover_image" accept="image/*" class="input">
    </div>

    <h3 style="margin:24px 0 12px;color:var(--gold-2);font-size:14px;text-transform:uppercase;letter-spacing:.1em;">Контент (<?= strtoupper($EL) ?>)</h3>

    <?php $t = $tr[$EL] ?? []; ?>
    <div class="field">
      <label>Заголовок (<?= strtoupper($EL) ?>) <span class="req">*</span></label>
      <input type="text" name="title[<?= $EL ?>]" class="input" required value="<?= e($t['title'] ?? '') ?>">
    </div>

    <div class="field">
      <label>Краткое описание (<?= strtoupper($EL) ?>)</label>
      <textarea name="excerpt[<?= $EL ?>]" class="textarea" rows="2" maxlength="500"><?= e($t['excerpt'] ?? '') ?></textarea>
    </div>

    <div class="field">
      <label>Текст новости (<?= strtoupper($EL) ?>)</label>
      <textarea name="body[<?= $EL ?>]" class="tinymce" rows="14"><?= e($t['body'] ?? '') ?></textarea>
    </div>

    <?php if ($isEdit): foreach (['en','ru','tj'] as $l): if ($l === $EL) continue;
      $ot = $tr[$l] ?? null;
    ?>
    <!-- Hidden preserve fields for other langs -->
    <input type="hidden" name="title[<?= $l ?>]" value="<?= e($ot['title'] ?? '') ?>">
    <input type="hidden" name="excerpt[<?= $l ?>]" value="<?= e($ot['excerpt'] ?? '') ?>">
    <textarea name="body[<?= $l ?>]" style="display:none"><?= e($ot['body'] ?? '') ?></textarea>
    <?php endforeach; endif; ?>

    <div class="btn-bar" style="margin-top:24px;">
      <button type="submit" class="btn btn--primary">💾 Сохранить</button>
      <a href="/admin/news.php" class="btn">Отмена</a>
    </div>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
