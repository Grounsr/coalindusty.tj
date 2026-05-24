<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'SEO';
$showLangSwitcher = true;
$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Inline save of meta fields
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save_meta' && $id) {
    csrf_required();
    foreach (['en','ru','tj'] as $lang) {
        $mt = trim($_POST['meta_title'][$lang] ?? '');
        $md = trim($_POST['meta_description'][$lang] ?? '');
        $exists = DB::value('SELECT page_id FROM page_translations WHERE page_id=? AND lang=?', [$id,$lang]);
        if ($exists) {
            DB::update('page_translations', ['meta_title'=>$mt,'meta_description'=>$md], 'page_id=:p AND lang=:l', ['p'=>$id,'l'=>$lang]);
        } else {
            DB::insert('page_translations', ['page_id'=>$id,'lang'=>$lang,'title'=>'','meta_title'=>$mt,'meta_description'=>$md]);
        }
    }
    log_action('update_seo', 'page', $id);
    flash('success', 'SEO сохранено');
    header('Location: /admin/seo.php'); exit;
}

$pages = DB::all('SELECT p.id, p.slug, p.system FROM pages p ORDER BY p.slug');
$allMeta = [];
foreach (DB::all('SELECT page_id, lang, meta_title, meta_description, title FROM page_translations') as $pt) {
    $allMeta[$pt['page_id']][$pt['lang']] = $pt;
}

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action === 'edit_meta' && $id): ?>
<?php $page = DB::row('SELECT * FROM pages WHERE id=?', [$id]); ?>
<div class="card">
  <div class="card-header"><h2>SEO для страницы: <code><?= e($page['slug'] ?? '') ?></code></h2><a href="/admin/seo.php" class="btn btn--sm">← Список</a></div>
  <form method="POST" action="/admin/seo.php?a=save_meta&id=<?= $id ?>">
    <?= csrf_field() ?>
    <?php foreach (['ru'=>'Русский','en'=>'English','tj'=>'Тоҷикӣ'] as $lang=>$langName): ?>
    <h3 style="margin:20px 0 12px;color:var(--gold-2);text-transform:uppercase;font-size:13px;letter-spacing:.1em;"><?= $langName ?></h3>
    <div class="field">
      <label>Meta Title (<?= strtoupper($lang) ?>)</label>
      <input type="text" name="meta_title[<?= $lang ?>]" class="input" maxlength="255" value="<?= e($allMeta[$id][$lang]['meta_title'] ?? '') ?>">
      <p class="help">Рекомендуется 50–60 символов</p>
    </div>
    <div class="field">
      <label>Meta Description (<?= strtoupper($lang) ?>)</label>
      <textarea name="meta_description[<?= $lang ?>]" class="textarea" rows="2" maxlength="500"><?= e($allMeta[$id][$lang]['meta_description'] ?? '') ?></textarea>
      <p class="help">Рекомендуется 120–160 символов</p>
    </div>
    <?php endforeach; ?>
    <button type="submit" class="btn btn--primary" style="margin-top:16px;">💾 Сохранить SEO</button>
  </form>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header"><h2>SEO — Мета-теги страниц</h2></div>
  <p style="padding:0 0 12px;color:var(--ink-3);font-size:13px;margin:0;">Управление meta title и meta description для каждой страницы сайта. Для полного редактирования — откройте страницу в <a href="/admin/pages.php">Страницы</a>.</p>
  <?php if (!$pages): ?><div class="empty">Страниц нет.</div><?php else: ?>
  <table class="data" style="font-size:13px;">
    <thead><tr><th>Slug</th><th>Meta Title (<?= strtoupper($EL) ?>)</th><th>Meta Description (<?= strtoupper($EL) ?>)</th><th>Системная</th><th class="actions">Действия</th></tr></thead>
    <tbody><?php foreach ($pages as $p): ?>
    <tr>
      <td><code><?= e($p['slug']) ?></code></td>
      <td>
        <?php $mt = $allMeta[$p['id']][$EL]['meta_title'] ?? ''; ?>
        <?php if ($mt): ?>
          <span style="color:<?= strlen($mt)>60?'var(--danger)':'var(--ink-1)' ?>"><?= e($mt) ?></span>
          <small style="color:var(--ink-3);">(<?= strlen($mt) ?>)</small>
        <?php else: ?><span style="color:var(--danger)">— не задан —</span><?php endif; ?>
      </td>
      <td>
        <?php $md = $allMeta[$p['id']][$EL]['meta_description'] ?? ''; ?>
        <?php if ($md): ?>
          <?= e(mb_substr($md,0,80)) ?><?= mb_strlen($md)>80?'…':'' ?>
          <small style="color:var(--ink-3);">(<?= mb_strlen($md) ?>)</small>
        <?php else: ?><span style="color:var(--ink-3)">— не задано —</span><?php endif; ?>
      </td>
      <td><?= $p['system'] ? '<span class="badge badge--warn">system</span>' : '' ?></td>
      <td class="actions">
        <a href="/admin/seo.php?a=edit_meta&id=<?= (int)$p['id'] ?>" class="btn btn--sm btn--primary">✎ SEO</a>
        <a href="/admin/pages.php?edit=<?= (int)$p['id'] ?>" class="btn btn--sm">📄 Страница</a>
      </td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
