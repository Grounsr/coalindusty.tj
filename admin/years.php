<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Годы форума';
$showLangSwitcher = true;
$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id) {
    csrf_required();
    $r = DB::row('SELECT cover_image FROM forum_years WHERE id=?', [$id]);
    if ($r) delete_file_safe($r['cover_image']);
    DB::delete('forum_years', 'id=?', [$id]);
    log_action('delete_year', 'forum_year', $id);
    flash('success', 'Удалено');
    header('Location: /admin/years.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create','edit'], true)) {
    csrf_required();
    $cover = admin_upload('cover_image', 'years') ?? ($_POST['cover_existing'] ?? null);
    $isCurrent = !empty($_POST['is_current']) ? 1 : 0;
    if ($isCurrent) {
        // Unset current on all other years
        DB::query('UPDATE forum_years SET is_current=0');
    }
    $data = [
        'year'               => (int)$_POST['year'],
        'event_date'         => $_POST['event_date'] ?: null,
        'is_current'         => $isCurrent,
        'is_published'       => !empty($_POST['is_published']) ? 1 : 0,
        'cover_image'        => $cover,
        'participants_count' => $_POST['participants_count'] !== '' ? (int)$_POST['participants_count'] : null,
        'countries_count'    => $_POST['countries_count'] !== '' ? (int)$_POST['countries_count'] : null,
        'speakers_count'     => $_POST['speakers_count'] !== '' ? (int)$_POST['speakers_count'] : null,
    ];
    if ($action === 'create') { $id = DB::insert('forum_years', $data); log_action('create_year','forum_year',$id); }
    else { DB::update('forum_years', $data, 'id=:id', ['id'=>$id]); log_action('update_year','forum_year',$id); }
    foreach (['en','ru','tj'] as $lang) {
        $td = [
            'title'       => trim($_POST['title'][$lang] ?? ''),
            'tagline'     => trim($_POST['tagline'][$lang] ?? ''),
            'description' => $_POST['description'][$lang] ?? '',
            'venue'       => trim($_POST['venue'][$lang] ?? ''),
        ];
        if (!$td['title']) continue;
        $exists = DB::value('SELECT forum_year_id FROM forum_year_translations WHERE forum_year_id=? AND lang=?', [$id, $lang]);
        if ($exists) DB::update('forum_year_translations', $td, 'forum_year_id=:y AND lang=:l', ['y'=>$id,'l'=>$lang]);
        else DB::insert('forum_year_translations', array_merge($td, ['forum_year_id'=>$id,'lang'=>$lang]));
    }
    flash('success', 'Сохранено');
    header('Location: /admin/years.php?a=edit&id='.$id.'&el='.$EL); exit;
}

if ($action === 'edit' && $id) {
    $current = DB::row('SELECT * FROM forum_years WHERE id=?', [$id]);
    $tr = [];
    foreach (DB::all('SELECT * FROM forum_year_translations WHERE forum_year_id=?', [$id]) as $t) $tr[$t['lang']] = $t;
}

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action === 'list'):
$list = DB::all('SELECT fy.*, fyt.title FROM forum_years fy LEFT JOIN forum_year_translations fyt ON fyt.forum_year_id=fy.id AND fyt.lang="ru" ORDER BY fy.year DESC'); ?>
<div class="card">
  <div class="card-header"><h2>Годы форума</h2><a href="/admin/years.php?a=create" class="btn btn--primary">+ Добавить год</a></div>
  <?php if (!$list): ?><div class="empty">Годов форума нет.</div><?php else: ?>
  <table class="data">
    <thead><tr><th>Год</th><th>Название (RU)</th><th>Дата</th><th>Участники</th><th>Статус</th><th>Текущий</th><th class="actions">Действия</th></tr></thead>
    <tbody><?php foreach ($list as $y): ?>
    <tr>
      <td><span class="badge badge--gold"><?= (int)$y['year'] ?></span></td>
      <td><strong><?= e($y['title'] ?? '—') ?></strong></td>
      <td style="font-size:13px;"><?= $y['event_date'] ? date('d.m.Y', strtotime($y['event_date'])) : '—' ?></td>
      <td style="font-size:13px;"><?= $y['participants_count'] ?? '—' ?></td>
      <td><?= $y['is_published'] ? '<span class="badge badge--ok">опубликован</span>' : '<span class="badge badge--warn">черновик</span>' ?></td>
      <td><?= $y['is_current'] ? '<span class="badge badge--ok">✓ текущий</span>' : '' ?></td>
      <td class="actions">
        <a href="/admin/years.php?a=edit&id=<?= (int)$y['id'] ?>" class="btn btn--sm btn--primary">✎</a>
        <a href="/admin/years.php?a=delete&id=<?= (int)$y['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить год форума?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>
  <?php endif; ?>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header"><h2><?= $action==='edit' ? 'Редактирование года форума' : 'Новый год форума' ?></h2><a href="/admin/years.php" class="btn btn--sm">← Список</a></div>
  <form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="field-row--3">
      <div class="field">
        <label>Год <span class="req">*</span></label>
        <input type="number" name="year" class="input" required min="2000" max="2100" value="<?= e($current['year'] ?? date('Y')) ?>">
      </div>
      <div class="field">
        <label>Дата проведения</label>
        <input type="date" name="event_date" class="input" value="<?= e($current['event_date'] ?? '') ?>">
      </div>
      <div class="field" style="display:flex;flex-direction:column;gap:8px;padding-top:8px;">
        <label class="check"><input type="checkbox" name="is_published" value="1" <?= !empty($current['is_published'])?'checked':'' ?>> Опубликован</label>
        <label class="check"><input type="checkbox" name="is_current" value="1" <?= !empty($current['is_current'])?'checked':'' ?>> Текущий год</label>
      </div>
    </div>
    <div class="field-row--3">
      <div class="field">
        <label>Участников</label>
        <input type="number" name="participants_count" class="input" value="<?= e($current['participants_count'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Стран</label>
        <input type="number" name="countries_count" class="input" value="<?= e($current['countries_count'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Спикеров</label>
        <input type="number" name="speakers_count" class="input" value="<?= e($current['speakers_count'] ?? '') ?>">
      </div>
    </div>
    <div class="field">
      <label>Обложка (рекомендуется 1920×1080)</label>
      <?php if (!empty($current['cover_image'])): ?>
        <div class="thumb" style="width:200px;height:auto;aspect-ratio:16/9;margin-bottom:8px;"><img src="<?= e($current['cover_image']) ?>"></div>
        <input type="hidden" name="cover_existing" value="<?= e($current['cover_image']) ?>">
      <?php endif; ?>
      <input type="file" name="cover_image" accept="image/*" class="input">
    </div>

    <?php $t = $tr[$EL] ?? []; ?>
    <h3 style="margin:24px 0 12px;color:var(--gold-2);text-transform:uppercase;font-size:13px;letter-spacing:.1em;">Контент (<?= strtoupper($EL) ?>)</h3>
    <div class="field">
      <label>Название форума <span class="req">*</span></label>
      <input type="text" name="title[<?= $EL ?>]" class="input" required value="<?= e($t['title'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Слоган</label>
      <input type="text" name="tagline[<?= $EL ?>]" class="input" value="<?= e($t['tagline'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Место проведения</label>
      <input type="text" name="venue[<?= $EL ?>]" class="input" value="<?= e($t['venue'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Описание</label>
      <textarea name="description[<?= $EL ?>]" class="tinymce" rows="8"><?= e($t['description'] ?? '') ?></textarea>
    </div>

    <?php if ($action==='edit'): foreach (['en','ru','tj'] as $l): if ($l===$EL) continue; $ot=$tr[$l] ?? null; ?>
    <input type="hidden" name="title[<?= $l ?>]" value="<?= e($ot['title'] ?? '') ?>">
    <input type="hidden" name="tagline[<?= $l ?>]" value="<?= e($ot['tagline'] ?? '') ?>">
    <input type="hidden" name="venue[<?= $l ?>]" value="<?= e($ot['venue'] ?? '') ?>">
    <textarea name="description[<?= $l ?>]" style="display:none"><?= e($ot['description'] ?? '') ?></textarea>
    <?php endforeach; endif; ?>

    <button type="submit" class="btn btn--primary" style="margin-top:16px;">💾 Сохранить</button>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
