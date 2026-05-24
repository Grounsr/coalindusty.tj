<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Программа форума';
$showLangSwitcher = true;
$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$filterYear = (int)($_GET['year'] ?? 0);

if ($action === 'delete' && $id) {
    csrf_required();
    DB::delete('program_items', 'id=?', [$id]);
    log_action('delete_program_item', 'program_item', $id);
    flash('success', 'Удалено');
    header('Location: /admin/program.php' . ($filterYear ? '?year='.$filterYear : '')); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create','edit'], true)) {
    csrf_required();
    $data = [
        'forum_year_id' => (int)$_POST['forum_year_id'],
        'day_number'    => max(1, (int)($_POST['day_number'] ?? 1)),
        'time_start'    => trim($_POST['time_start'] ?? ''),
        'time_end'      => trim($_POST['time_end'] ?? ''),
        'block_label'   => trim($_POST['block_label'] ?? ''),
        'hall'          => trim($_POST['hall'] ?? ''),
        'tag'           => $_POST['tag'] ?? '',
        'sort_order'    => (int)($_POST['sort_order'] ?? 0),
    ];
    if ($action === 'create') { $id = DB::insert('program_items', $data); log_action('create_program_item','program_item',$id); }
    else { DB::update('program_items', $data, 'id=:id', ['id'=>$id]); log_action('update_program_item','program_item',$id); }
    foreach (['en','ru','tj'] as $lang) {
        $td = ['title'=>trim($_POST['title'][$lang] ?? ''), 'description'=>trim($_POST['description'][$lang] ?? '')];
        if (!$td['title']) continue;
        $exists = DB::value('SELECT program_item_id FROM program_item_translations WHERE program_item_id=? AND lang=?', [$id, $lang]);
        if ($exists) DB::update('program_item_translations', $td, 'program_item_id=:p AND lang=:l', ['p'=>$id,'l'=>$lang]);
        else DB::insert('program_item_translations', array_merge($td, ['program_item_id'=>$id,'lang'=>$lang]));
    }
    flash('success', 'Сохранено');
    header('Location: /admin/program.php?a=edit&id='.$id.'&el='.$EL); exit;
}

if ($action === 'edit' && $id) {
    $current = DB::row('SELECT * FROM program_items WHERE id=?', [$id]);
    $tr = [];
    foreach (DB::all('SELECT * FROM program_item_translations WHERE program_item_id=?', [$id]) as $t) $tr[$t['lang']] = $t;
}
$years = DB::all('SELECT id, year FROM forum_years ORDER BY year DESC');
$tags = ['registration'=>'Регистрация','opening'=>'Открытие','plenary'=>'Пленарное','panel'=>'Панель','break'=>'Перерыв','ceremony'=>'Церемония','closing'=>'Закрытие','press'=>'Пресс','reception'=>'Приём'];

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action === 'list'):
$sql = 'SELECT pi.*, pit.title, fy.year FROM program_items pi LEFT JOIN program_item_translations pit ON pit.program_item_id=pi.id AND pit.lang="ru" LEFT JOIN forum_years fy ON fy.id=pi.forum_year_id';
$params = [];
if ($filterYear) { $sql .= ' WHERE pi.forum_year_id=?'; $params[] = $filterYear; }
$sql .= ' ORDER BY pi.forum_year_id DESC, pi.day_number, pi.sort_order';
$list = DB::all($sql, $params); ?>
<div class="card">
  <div class="card-header">
    <h2>Программа форума</h2>
    <div style="display:flex;gap:8px;align-items:center;">
      <form method="GET" style="display:flex;gap:8px;align-items:center;">
        <select name="year" class="select" style="width:120px;" onchange="this.form.submit()">
          <option value="">— Все годы —</option>
          <?php foreach ($years as $y): ?><option value="<?= (int)$y['id'] ?>" <?= $filterYear===$y['id']?'selected':'' ?>><?= (int)$y['year'] ?></option><?php endforeach; ?>
        </select>
      </form>
      <a href="/admin/program.php?a=create" class="btn btn--primary">+ Добавить элемент</a>
    </div>
  </div>
  <?php if (!$list): ?><div class="empty">Элементов программы нет.</div><?php else: ?>
  <table class="data">
    <thead><tr><th>Год</th><th>День</th><th>Время</th><th>Блок</th><th>Тег</th><th>Название (RU)</th><th>Зал</th><th>Порядок</th><th class="actions">Действия</th></tr></thead>
    <tbody><?php foreach ($list as $p): ?>
    <tr>
      <td><span class="badge badge--gold"><?= (int)$p['year'] ?></span></td>
      <td><?= (int)$p['day_number'] ?></td>
      <td style="font-size:13px;white-space:nowrap;"><?= e($p['time_start'] ?? '') ?><?= $p['time_end'] ? '–'.e($p['time_end']) : '' ?></td>
      <td><?= e($p['block_label'] ?? '') ?></td>
      <td><?= $p['tag'] ? '<span class="badge">'.e($p['tag']).'</span>' : '—' ?></td>
      <td><?= e($p['title'] ?? '—') ?></td>
      <td style="font-size:12px;color:var(--ink-3);"><?= e($p['hall'] ?? '') ?></td>
      <td><?= (int)$p['sort_order'] ?></td>
      <td class="actions">
        <a href="/admin/program.php?a=edit&id=<?= (int)$p['id'] ?>" class="btn btn--sm btn--primary">✎</a>
        <a href="/admin/program.php?a=delete&id=<?= (int)$p['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?><?= $filterYear ? '&year='.$filterYear : '' ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить элемент программы?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>
  <?php endif; ?>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header"><h2><?= $action==='edit' ? 'Редактирование элемента' : 'Новый элемент программы' ?></h2><a href="/admin/program.php" class="btn btn--sm">← Список</a></div>
  <form method="POST">
    <?= csrf_field() ?>
    <div class="field-row--3">
      <div class="field">
        <label>Год форума <span class="req">*</span></label>
        <select name="forum_year_id" class="select" required>
          <?php foreach ($years as $y): ?><option value="<?= (int)$y['id'] ?>" <?= ($current['forum_year_id'] ?? 0)==$y['id']?'selected':'' ?>><?= (int)$y['year'] ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>День</label>
        <input type="number" name="day_number" class="input" min="1" max="5" value="<?= (int)($current['day_number'] ?? 1) ?>">
      </div>
      <div class="field">
        <label>Порядок</label>
        <input type="number" name="sort_order" class="input" value="<?= (int)($current['sort_order'] ?? 0) ?>">
      </div>
    </div>
    <div class="field-row">
      <div class="field">
        <label>Начало</label>
        <input type="text" name="time_start" class="input" value="<?= e($current['time_start'] ?? '') ?>" placeholder="09:00">
      </div>
      <div class="field">
        <label>Конец</label>
        <input type="text" name="time_end" class="input" value="<?= e($current['time_end'] ?? '') ?>" placeholder="10:30">
      </div>
      <div class="field">
        <label>Блок</label>
        <input type="text" name="block_label" class="input" value="<?= e($current['block_label'] ?? '') ?>" placeholder="I, II, ...">
      </div>
      <div class="field">
        <label>Зал</label>
        <input type="text" name="hall" class="input" value="<?= e($current['hall'] ?? '') ?>" placeholder="Главный зал">
      </div>
    </div>
    <div class="field">
      <label>Тег</label>
      <select name="tag" class="select">
        <option value="">— без тега —</option>
        <?php foreach ($tags as $v=>$l): ?>
        <option value="<?= $v ?>" <?= ($current['tag'] ?? '')===$v?'selected':'' ?>><?= $l ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <?php $t = $tr[$EL] ?? []; ?>
    <h3 style="margin:24px 0 12px;color:var(--gold-2);text-transform:uppercase;font-size:13px;letter-spacing:.1em;">Контент (<?= strtoupper($EL) ?>)</h3>
    <div class="field">
      <label>Название <span class="req">*</span></label>
      <input type="text" name="title[<?= $EL ?>]" class="input" required value="<?= e($t['title'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Описание</label>
      <textarea name="description[<?= $EL ?>]" class="textarea" rows="3"><?= e($t['description'] ?? '') ?></textarea>
    </div>

    <?php if ($action==='edit'): foreach (['en','ru','tj'] as $l): if ($l===$EL) continue; $ot=$tr[$l] ?? null; ?>
    <input type="hidden" name="title[<?= $l ?>]" value="<?= e($ot['title'] ?? '') ?>">
    <textarea name="description[<?= $l ?>]" style="display:none"><?= e($ot['description'] ?? '') ?></textarea>
    <?php endforeach; endif; ?>

    <button type="submit" class="btn btn--primary" style="margin-top:16px;">💾 Сохранить</button>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
