<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Руководство форума';
$showLangSwitcher = true;

$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id) {
    csrf_required();
    $row = DB::row('SELECT photo FROM leadership WHERE id=?', [$id]);
    if ($row) delete_file_safe($row['photo']);
    DB::delete('leadership', 'id=?', [$id]);
    log_action('delete_leadership', 'leadership', $id);
    flash('success', 'Удалено');
    header('Location: /admin/leadership.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create','edit'], true)) {
    csrf_required();
    $photo = admin_upload('photo', 'leadership') ?? ($_POST['photo_existing'] ?? null);
    $data = [
        'photo' => $photo,
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'is_visible' => !empty($_POST['is_visible']) ? 1 : 0,
    ];
    if ($action === 'create') { $id = DB::insert('leadership', $data); log_action('create_leadership','leadership',$id); }
    else { DB::update('leadership', $data, 'id=:id', ['id'=>$id]); log_action('update_leadership','leadership',$id); }

    foreach (['en','ru','tj'] as $lang) {
        $td = [
            'full_name' => trim($_POST['full_name'][$lang] ?? ''),
            'position'  => trim($_POST['position'][$lang] ?? ''),
            'quote'     => trim($_POST['quote'][$lang] ?? ''),
        ];
        if (!$td['full_name']) continue;
        $exists = DB::value('SELECT leadership_id FROM leadership_translations WHERE leadership_id=? AND lang=?', [$id, $lang]);
        if ($exists) DB::update('leadership_translations', $td, 'leadership_id=:l AND lang=:lg', ['l'=>$id,'lg'=>$lang]);
        else DB::insert('leadership_translations', array_merge($td, ['leadership_id'=>$id,'lang'=>$lang]));
    }
    flash('success', 'Сохранено');
    header('Location: /admin/leadership.php?a=edit&id=' . $id . '&el=' . $EL);
    exit;
}

if ($action === 'edit' && $id) {
    $current = DB::row('SELECT * FROM leadership WHERE id=?', [$id]);
    $tr = [];
    foreach (DB::all('SELECT * FROM leadership_translations WHERE leadership_id=?', [$id]) as $t) $tr[$t['lang']] = $t;
}

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header">
    <h2>Карточки руководства</h2>
    <a href="/admin/leadership.php?a=create" class="btn btn--primary">+ Добавить</a>
  </div>
  <?php $list = DB::all('SELECT l.*, lt.full_name, lt.position FROM leadership l LEFT JOIN leadership_translations lt ON lt.leadership_id=l.id AND lt.lang="ru" ORDER BY l.sort_order'); ?>
  <?php if (!$list): ?>
    <div class="empty">Пока нет карточек.</div>
  <?php else: ?>
  <table class="data">
    <thead><tr><th>Фото</th><th>ФИО</th><th>Должность</th><th>Порядок</th><th>Видимость</th><th class="actions">Действия</th></tr></thead>
    <tbody>
      <?php foreach ($list as $l): ?>
      <tr>
        <td><div class="thumb"><?php if ($l['photo']): ?><img src="<?= e($l['photo']) ?>"><?php endif; ?></div></td>
        <td><strong><?= e($l['full_name'] ?? '—') ?></strong></td>
        <td style="color:var(--ink-3);font-size:13px;"><?= e($l['position'] ?? '') ?></td>
        <td><?= (int)$l['sort_order'] ?></td>
        <td><?= $l['is_visible'] ? '<span class="badge badge--ok">видимо</span>' : '<span class="badge badge--neutral">скрыто</span>' ?></td>
        <td class="actions">
          <a href="/admin/leadership.php?a=edit&id=<?= (int)$l['id'] ?>" class="btn btn--sm btn--primary">✎</a>
          <a href="/admin/leadership.php?a=delete&id=<?= (int)$l['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить?')">🗑</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header"><h2><?= $action === 'edit' ? 'Редактирование' : 'Новая карточка' ?></h2><a href="/admin/leadership.php" class="btn btn--sm">← Список</a></div>

  <form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="field-row">
      <div class="field">
        <label>Фото руководителя</label>
        <?php if (!empty($current['photo'])): ?>
          <div class="thumb" style="width:120px;height:120px;margin-bottom:8px;"><img src="<?= e($current['photo']) ?>"></div>
          <input type="hidden" name="photo_existing" value="<?= e($current['photo']) ?>">
        <?php endif; ?>
        <input type="file" name="photo" accept="image/*" class="input">
        <p class="help">Квадратное фото (рекомендуется 600×600)</p>
      </div>
      <div class="field">
        <label>Порядок</label>
        <input type="number" name="sort_order" class="input" value="<?= e($current['sort_order'] ?? 0) ?>">
        <label class="check" style="margin-top:12px;">
          <input type="checkbox" name="is_visible" value="1" <?= !isset($current) || $current['is_visible'] ? 'checked' : '' ?>>
          Показывать на сайте
        </label>
      </div>
    </div>

    <?php $t = $tr[$EL] ?? []; ?>
    <h3 style="margin:24px 0 12px;color:var(--gold-2);text-transform:uppercase;font-size:13px;letter-spacing:.1em;">Контент (<?= strtoupper($EL) ?>)</h3>

    <div class="field">
      <label>ФИО <span class="req">*</span></label>
      <input type="text" name="full_name[<?= $EL ?>]" class="input" required value="<?= e($t['full_name'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Должность <span class="req">*</span></label>
      <input type="text" name="position[<?= $EL ?>]" class="input" required value="<?= e($t['position'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Цитата (опционально)</label>
      <textarea name="quote[<?= $EL ?>]" class="textarea" rows="3"><?= e($t['quote'] ?? '') ?></textarea>
    </div>

    <?php if ($action === 'edit'): foreach (['en','ru','tj'] as $l): if ($l === $EL) continue; $ot = $tr[$l] ?? null; ?>
    <input type="hidden" name="full_name[<?= $l ?>]" value="<?= e($ot['full_name'] ?? '') ?>">
    <input type="hidden" name="position[<?= $l ?>]" value="<?= e($ot['position'] ?? '') ?>">
    <textarea name="quote[<?= $l ?>]" style="display:none"><?= e($ot['quote'] ?? '') ?></textarea>
    <?php endforeach; endif; ?>

    <button type="submit" class="btn btn--primary" style="margin-top:16px;">💾 Сохранить</button>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
