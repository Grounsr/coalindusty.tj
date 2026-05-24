<?php
require_once __DIR__ . '/_bootstrap.php';
$adminTitle = 'Ключевые темы';
$showLangSwitcher = true;
$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id) {
    csrf_required();
    DB::delete('topics', 'id=?', [$id]);
    log_action('delete_topic','topic',$id);
    flash('success','Удалено'); header('Location: /admin/topics.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action,['create','edit'],true)) {
    csrf_required();
    $data = ['forum_year_id'=>(int)($_POST['forum_year_id'] ?? 0), 'icon'=>trim($_POST['icon'] ?? ''), 'sort_order'=>(int)($_POST['sort_order'] ?? 0)];
    if ($action==='create'){ $id=DB::insert('topics',$data); log_action('create_topic','topic',$id);} else { DB::update('topics',$data,'id=:id',['id'=>$id]); log_action('update_topic','topic',$id);}
    foreach (['en','ru','tj'] as $lang) {
        $td = ['title'=>trim($_POST['title'][$lang] ?? ''), 'description'=>trim($_POST['description'][$lang] ?? '')];
        if (!$td['title']) continue;
        $exists = DB::value('SELECT topic_id FROM topic_translations WHERE topic_id=? AND lang=?', [$id,$lang]);
        if ($exists) DB::update('topic_translations',$td,'topic_id=:t AND lang=:l',['t'=>$id,'l'=>$lang]);
        else DB::insert('topic_translations',array_merge($td,['topic_id'=>$id,'lang'=>$lang]));
    }
    flash('success','Сохранено'); header('Location: /admin/topics.php?a=edit&id='.$id.'&el='.$EL); exit;
}

if ($action==='edit') {
    $current = DB::row('SELECT * FROM topics WHERE id=?', [$id]);
    $tr = [];
    foreach (DB::all('SELECT * FROM topic_translations WHERE topic_id=?', [$id]) as $t) $tr[$t['lang']] = $t;
}
$years = DB::all('SELECT id, year FROM forum_years ORDER BY year DESC');

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action==='list'):
$list = DB::all('SELECT t.*, tt.title, fy.year FROM topics t LEFT JOIN topic_translations tt ON tt.topic_id=t.id AND tt.lang="ru" LEFT JOIN forum_years fy ON fy.id=t.forum_year_id ORDER BY t.forum_year_id DESC, t.sort_order'); ?>
<div class="card">
  <div class="card-header"><h2>Ключевые темы форума</h2><a href="/admin/topics.php?a=create" class="btn btn--primary">+ Добавить тему</a></div>
  <?php if (!$list): ?><div class="empty">Тем нет.</div><?php else: ?>
  <table class="data">
    <thead><tr><th>#</th><th>Год</th><th>Название (RU)</th><th>Порядок</th><th class="actions">Действия</th></tr></thead>
    <tbody>
    <?php foreach ($list as $i=>$t): ?>
    <tr>
      <td><?= str_pad((string)($i+1),2,'0',STR_PAD_LEFT) ?></td>
      <td><span class="badge badge--gold"><?= (int)$t['year'] ?></span></td>
      <td><strong><?= e($t['title'] ?? '—') ?></strong></td>
      <td><?= (int)$t['sort_order'] ?></td>
      <td class="actions">
        <a href="/admin/topics.php?a=edit&id=<?= (int)$t['id'] ?>" class="btn btn--sm btn--primary">✎</a>
        <a href="/admin/topics.php?a=delete&id=<?= (int)$t['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить тему?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header"><h2><?= $action==='edit' ? 'Редактирование темы' : 'Новая тема' ?></h2><a href="/admin/topics.php" class="btn btn--sm">← Список</a></div>

  <form method="POST">
    <?= csrf_field() ?>
    <div class="field-row--3">
      <div class="field">
        <label>Год форума <span class="req">*</span></label>
        <select name="forum_year_id" class="select" required>
          <?php foreach ($years as $y): ?>
          <option value="<?= (int)$y['id'] ?>" <?= ($current['forum_year_id'] ?? 0)==$y['id']?'selected':'' ?>><?= (int)$y['year'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>Иконка (slug)</label>
        <input type="text" name="icon" class="input" value="<?= e($current['icon'] ?? '') ?>" placeholder="mining, technology...">
      </div>
      <div class="field">
        <label>Порядок</label>
        <input type="number" name="sort_order" class="input" value="<?= e($current['sort_order'] ?? 0) ?>">
      </div>
    </div>

    <?php $t = $tr[$EL] ?? []; ?>
    <h3 style="margin:24px 0 12px;color:var(--gold-2);text-transform:uppercase;font-size:13px;letter-spacing:.1em;">Контент (<?= strtoupper($EL) ?>)</h3>
    <div class="field">
      <label>Название <span class="req">*</span></label>
      <input type="text" name="title[<?= $EL ?>]" class="input" required value="<?= e($t['title'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Описание</label>
      <textarea name="description[<?= $EL ?>]" class="textarea" rows="4"><?= e($t['description'] ?? '') ?></textarea>
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
