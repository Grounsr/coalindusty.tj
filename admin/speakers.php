<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Спикеры';
$showLangSwitcher = true;
$action = $_GET['a'] ?? 'list'; $id = (int)($_GET['id'] ?? 0);

if ($action==='delete' && $id) { csrf_required(); $r=DB::row('SELECT photo FROM speakers WHERE id=?',[$id]); if ($r) delete_file_safe($r['photo']); DB::delete('speakers','id=?',[$id]); log_action('delete_speaker','speaker',$id); flash('success','Удалено'); header('Location: /admin/speakers.php'); exit; }

if ($_SERVER['REQUEST_METHOD']==='POST' && in_array($action,['create','edit'],true)) {
    csrf_required();
    $photo = admin_upload('photo','speakers') ?? ($_POST['photo_existing'] ?? null);
    $data = ['forum_year_id'=>(int)$_POST['forum_year_id'], 'photo'=>$photo, 'sort_order'=>(int)($_POST['sort_order'] ?? 0), 'is_visible'=>!empty($_POST['is_visible'])?1:0];
    if ($action==='create'){$id=DB::insert('speakers',$data); log_action('create_speaker','speaker',$id);} else { DB::update('speakers',$data,'id=:id',['id'=>$id]); log_action('update_speaker','speaker',$id);}
    foreach (['en','ru','tj'] as $lang) {
        $td=['full_name'=>trim($_POST['full_name'][$lang] ?? ''), 'position'=>trim($_POST['position'][$lang] ?? ''), 'organization'=>trim($_POST['organization'][$lang] ?? ''), 'country'=>trim($_POST['country'][$lang] ?? ''), 'bio'=>trim($_POST['bio'][$lang] ?? '')];
        if (!$td['full_name']) continue;
        $exists=DB::value('SELECT speaker_id FROM speaker_translations WHERE speaker_id=? AND lang=?',[$id,$lang]);
        if ($exists) DB::update('speaker_translations',$td,'speaker_id=:s AND lang=:l',['s'=>$id,'l'=>$lang]);
        else DB::insert('speaker_translations',array_merge($td,['speaker_id'=>$id,'lang'=>$lang]));
    }
    flash('success','Сохранено'); header('Location: /admin/speakers.php?a=edit&id='.$id.'&el='.$EL); exit;
}
if ($action==='edit'){$current=DB::row('SELECT * FROM speakers WHERE id=?',[$id]); $tr=[]; foreach (DB::all('SELECT * FROM speaker_translations WHERE speaker_id=?',[$id]) as $t) $tr[$t['lang']]=$t;}
$years = DB::all('SELECT id, year FROM forum_years ORDER BY year DESC');
require __DIR__ . '/_layout_top.php';
?>

<?php if ($action==='list'): $list=DB::all('SELECT s.*, st.full_name, st.position, st.organization, fy.year FROM speakers s LEFT JOIN speaker_translations st ON st.speaker_id=s.id AND st.lang="ru" LEFT JOIN forum_years fy ON fy.id=s.forum_year_id ORDER BY s.forum_year_id DESC, s.sort_order'); ?>
<div class="card">
  <div class="card-header"><h2>Спикеры форума</h2><a href="/admin/speakers.php?a=create" class="btn btn--primary">+ Добавить спикера</a></div>
  <?php if (!$list): ?><div class="empty">Спикеры ещё не добавлены.</div><?php else: ?>
  <table class="data">
    <thead><tr><th>Фото</th><th>ФИО</th><th>Должность</th><th>Год</th><th>Видимость</th><th class="actions">Действия</th></tr></thead>
    <tbody><?php foreach ($list as $s): ?>
    <tr>
      <td><div class="thumb"><?php if ($s['photo']): ?><img src="<?= e($s['photo']) ?>"><?php endif; ?></div></td>
      <td><strong><?= e($s['full_name'] ?? '—') ?></strong><br><small style="color:var(--ink-3)"><?= e($s['organization'] ?? '') ?></small></td>
      <td style="color:var(--ink-3);font-size:13px;"><?= e($s['position'] ?? '') ?></td>
      <td><span class="badge badge--gold"><?= (int)$s['year'] ?></span></td>
      <td><?= $s['is_visible'] ? '<span class="badge badge--ok">видим</span>' : '<span class="badge badge--neutral">скрыт</span>' ?></td>
      <td class="actions">
        <a href="/admin/speakers.php?a=edit&id=<?= (int)$s['id'] ?>" class="btn btn--sm btn--primary">✎</a>
        <a href="/admin/speakers.php?a=delete&id=<?= (int)$s['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>
  <?php endif; ?>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header"><h2><?= $action==='edit'?'Редактирование спикера':'Новый спикер' ?></h2><a href="/admin/speakers.php" class="btn btn--sm">← Список</a></div>
  <form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="field-row--3">
      <div class="field">
        <label>Год форума <span class="req">*</span></label>
        <select name="forum_year_id" class="select" required>
          <?php foreach ($years as $y): ?><option value="<?= (int)$y['id'] ?>" <?= ($current['forum_year_id'] ?? 0)==$y['id']?'selected':'' ?>><?= (int)$y['year'] ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>Порядок</label>
        <input type="number" name="sort_order" class="input" value="<?= e($current['sort_order'] ?? 0) ?>">
      </div>
      <div class="field"><label class="check" style="margin-top:28px;"><input type="checkbox" name="is_visible" value="1" <?= !isset($current) || $current['is_visible']?'checked':'' ?>> Видимый</label></div>
    </div>

    <div class="field">
      <label>Фото 3×4 (рекомендуется 600×800)</label>
      <?php if (!empty($current['photo'])): ?><div class="thumb" style="width:90px;height:120px;margin-bottom:8px;"><img src="<?= e($current['photo']) ?>"></div><input type="hidden" name="photo_existing" value="<?= e($current['photo']) ?>"><?php endif; ?>
      <input type="file" name="photo" accept="image/*" class="input">
    </div>

    <?php $t=$tr[$EL] ?? []; ?>
    <h3 style="margin:24px 0 12px;color:var(--gold-2);text-transform:uppercase;font-size:13px;letter-spacing:.1em;">Контент (<?= strtoupper($EL) ?>)</h3>
    <div class="field-row">
      <div class="field"><label>ФИО <span class="req">*</span></label><input type="text" name="full_name[<?= $EL ?>]" class="input" required value="<?= e($t['full_name'] ?? '') ?>"></div>
      <div class="field"><label>Страна</label><input type="text" name="country[<?= $EL ?>]" class="input" value="<?= e($t['country'] ?? '') ?>"></div>
    </div>
    <div class="field-row">
      <div class="field"><label>Должность</label><input type="text" name="position[<?= $EL ?>]" class="input" value="<?= e($t['position'] ?? '') ?>"></div>
      <div class="field"><label>Организация</label><input type="text" name="organization[<?= $EL ?>]" class="input" value="<?= e($t['organization'] ?? '') ?>"></div>
    </div>
    <div class="field"><label>Биография</label><textarea name="bio[<?= $EL ?>]" class="textarea" rows="4"><?= e($t['bio'] ?? '') ?></textarea></div>

    <?php if ($action==='edit'): foreach (['en','ru','tj'] as $l): if ($l===$EL) continue; $ot=$tr[$l] ?? null; ?>
    <input type="hidden" name="full_name[<?= $l ?>]" value="<?= e($ot['full_name'] ?? '') ?>">
    <input type="hidden" name="position[<?= $l ?>]" value="<?= e($ot['position'] ?? '') ?>">
    <input type="hidden" name="organization[<?= $l ?>]" value="<?= e($ot['organization'] ?? '') ?>">
    <input type="hidden" name="country[<?= $l ?>]" value="<?= e($ot['country'] ?? '') ?>">
    <textarea name="bio[<?= $l ?>]" style="display:none"><?= e($ot['bio'] ?? '') ?></textarea>
    <?php endforeach; endif; ?>

    <button type="submit" class="btn btn--primary" style="margin-top:16px;">💾 Сохранить</button>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
