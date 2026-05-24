<?php
require_once __DIR__ . '/_bootstrap.php';
$adminTitle = 'Управление страницами';
$showLangSwitcher = true;

$pages = DB::all('SELECT id, slug, system FROM pages ORDER BY id');
$editId = (int)($_GET['edit'] ?? 0);

if ($editId && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_required();
    foreach (['en','ru','tj'] as $lang) {
        $data = [
            'title'            => trim($_POST['title'][$lang] ?? ''),
            'subtitle'         => trim($_POST['subtitle'][$lang] ?? ''),
            'content'          => $_POST['content'][$lang] ?? '',
            'meta_title'       => trim($_POST['meta_title'][$lang] ?? ''),
            'meta_description' => trim($_POST['meta_description'][$lang] ?? ''),
            'meta_keywords'    => trim($_POST['meta_keywords'][$lang] ?? ''),
        ];
        $exists = DB::value('SELECT id FROM page_translations WHERE page_id=? AND lang=?', [$editId, $lang]);
        if ($exists) {
            DB::update('page_translations', $data, 'page_id = :pid AND lang = :lang', ['pid'=>$editId, 'lang'=>$lang]);
        } else {
            DB::insert('page_translations', array_merge($data, ['page_id'=>$editId, 'lang'=>$lang]));
        }
    }
    log_action('update_page', 'page', $editId);
    flash('success', 'Страница сохранена');
    header('Location: /admin/pages.php?edit=' . $editId . '&el=' . $EL);
    exit;
}

$current = $editId ? DB::row('SELECT * FROM pages WHERE id = ?', [$editId]) : null;
$translations = [];
if ($current) {
    foreach (DB::all('SELECT * FROM page_translations WHERE page_id = ?', [$editId]) as $t) {
        $translations[$t['lang']] = $t;
    }
}

require __DIR__ . '/_layout_top.php';
?>

<?php if (!$editId): ?>
<div class="card">
  <div class="card-header"><h2>Все страницы сайта</h2></div>
  <table class="data">
    <thead><tr><th>Slug</th><th>Заголовок (RU)</th><th>URL</th><th class="actions">Действия</th></tr></thead>
    <tbody>
    <?php foreach ($pages as $p):
      $tt = DB::row('SELECT title FROM page_translations WHERE page_id=? AND lang="ru"', [$p['id']]);
    ?>
    <tr>
      <td><code><?= e($p['slug']) ?></code></td>
      <td><?= e($tt['title'] ?? '—') ?></td>
      <td><a href="/?p=<?= e($p['slug']) ?>&lang=ru" target="_blank">/?p=<?= e($p['slug']) ?></a></td>
      <td class="actions">
        <a href="/admin/pages.php?edit=<?= (int)$p['id'] ?>" class="btn btn--sm btn--primary">Редактировать</a>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header">
    <h2>Редактирование: <code><?= e($current['slug']) ?></code></h2>
    <a href="/admin/pages.php" class="btn btn--sm">← К списку</a>
  </div>

  <form method="POST" action="">
    <?= csrf_field() ?>

    <?php foreach (['en','ru','tj'] as $lang): if ($lang !== $EL) continue;
      $tr = $translations[$lang] ?? ['title'=>'','subtitle'=>'','content'=>'','meta_title'=>'','meta_description'=>'','meta_keywords'=>''];
    ?>
    <div class="field">
      <label>Заголовок страницы (<?= strtoupper($lang) ?>) <span class="req">*</span></label>
      <input type="text" name="title[<?= $lang ?>]" class="input" required value="<?= e($tr['title']) ?>">
    </div>

    <div class="field">
      <label>Подзаголовок (<?= strtoupper($lang) ?>)</label>
      <input type="text" name="subtitle[<?= $lang ?>]" class="input" value="<?= e($tr['subtitle']) ?>">
    </div>

    <div class="field">
      <label>Содержание (<?= strtoupper($lang) ?>)</label>
      <textarea name="content[<?= $lang ?>]" class="tinymce" rows="14"><?= e($tr['content']) ?></textarea>
      <p class="help">Используйте редактор. Доступны изображения, ссылки, заголовки, списки, таблицы.</p>
    </div>

    <h3 style="margin:32px 0 12px;color:var(--gold-2);font-size:14px;letter-spacing:.1em;text-transform:uppercase;">SEO (<?= strtoupper($lang) ?>)</h3>

    <div class="field-row">
      <div class="field">
        <label>Meta Title</label>
        <input type="text" name="meta_title[<?= $lang ?>]" class="input" value="<?= e($tr['meta_title']) ?>" maxlength="255">
        <p class="help">Оптимально 50-60 символов</p>
      </div>
      <div class="field">
        <label>Meta Keywords</label>
        <input type="text" name="meta_keywords[<?= $lang ?>]" class="input" value="<?= e($tr['meta_keywords']) ?>">
        <p class="help">Через запятую</p>
      </div>
    </div>

    <div class="field">
      <label>Meta Description</label>
      <textarea name="meta_description[<?= $lang ?>]" class="textarea" rows="3" maxlength="500"><?= e($tr['meta_description']) ?></textarea>
      <p class="help">Оптимально 140-160 символов</p>
    </div>
    <?php endforeach; ?>

    <div class="btn-bar" style="margin-top:24px;">
      <button type="submit" class="btn btn--primary">💾 Сохранить</button>
      <a href="/?p=<?= e($current['slug']) ?>&lang=<?= $EL ?>" target="_blank" class="btn">👁 Открыть страницу</a>
    </div>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
