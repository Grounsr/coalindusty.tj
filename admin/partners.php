<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Партнёры';
$showLangSwitcher = true;
$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id) {
    csrf_required();
    $r = DB::row('SELECT logo FROM partners WHERE id=?', [$id]);
    if ($r) delete_file_safe($r['logo']);
    DB::delete('partners', 'id=?', [$id]);
    log_action('delete_partner', 'partner', $id);
    flash('success', 'Удалено');
    header('Location: /admin/partners.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create','edit'], true)) {
    csrf_required();
    $logo = admin_upload('logo', 'partners') ?? ($_POST['logo_existing'] ?? null);
    $data = [
        'forum_year_id' => (int)$_POST['forum_year_id'],
        'logo'          => $logo,
        'url'           => trim($_POST['url'] ?? ''),
        'tier'          => $_POST['tier'] ?? 'partner',
        'sort_order'    => (int)($_POST['sort_order'] ?? 0),
    ];
    if ($action === 'create') { $id = DB::insert('partners', $data); log_action('create_partner','partner',$id); }
    else { DB::update('partners', $data, 'id=:id', ['id'=>$id]); log_action('update_partner','partner',$id); }
    foreach (['en','ru','tj'] as $lang) {
        $td = ['name'=>trim($_POST['name'][$lang] ?? ''), 'description'=>trim($_POST['description'][$lang] ?? '')];
        if (!$td['name']) continue;
        $exists = DB::value('SELECT partner_id FROM partner_translations WHERE partner_id=? AND lang=?', [$id, $lang]);
        if ($exists) DB::update('partner_translations', $td, 'partner_id=:p AND lang=:l', ['p'=>$id,'l'=>$lang]);
        else DB::insert('partner_translations', array_merge($td, ['partner_id'=>$id,'lang'=>$lang]));
    }
    flash('success', 'Сохранено');
    header('Location: /admin/partners.php?a=edit&id='.$id.'&el='.$EL); exit;
}

if ($action === 'edit' && $id) {
    $current = DB::row('SELECT * FROM partners WHERE id=?', [$id]);
    $tr = [];
    foreach (DB::all('SELECT * FROM partner_translations WHERE partner_id=?', [$id]) as $t) $tr[$t['lang']] = $t;
}
$years = DB::all('SELECT id, year FROM forum_years ORDER BY year DESC');

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action === 'list'):
$list = DB::all('SELECT p.*, pt.name, fy.year FROM partners p LEFT JOIN partner_translations pt ON pt.partner_id=p.id AND pt.lang="ru" LEFT JOIN forum_years fy ON fy.id=p.forum_year_id ORDER BY p.forum_year_id DESC, p.sort_order'); ?>
<div class="card">
  <div class="card-header"><h2>Партнёры форума</h2><a href="/admin/partners.php?a=create" class="btn btn--primary">+ Добавить партнёра</a></div>
  <?php if (!$list): ?><div class="empty">Партнёров нет.</div><?php else: ?>
  <table class="data">
    <thead><tr><th>Лого</th><th>Название (RU)</th><th>Уровень</th><th>Год</th><th>Сайт</th><th>Порядок</th><th class="actions">Действия</th></tr></thead>
    <tbody><?php foreach ($list as $p): ?>
    <tr>
      <td><div class="thumb"><?php if ($p['logo']): ?><img src="<?= e($p['logo']) ?>"><?php endif; ?></div></td>
      <td><strong><?= e($p['name'] ?? '—') ?></strong></td>
      <td><span class="badge badge--gold"><?= e($p['tier']) ?></span></td>
      <td><?= (int)$p['year'] ?></td>
      <td><?php if ($p['url']): ?><a href="<?= e($p['url']) ?>" target="_blank" style="font-size:12px;">↗</a><?php else: ?>—<?php endif; ?></td>
      <td><?= (int)$p['sort_order'] ?></td>
      <td class="actions">
        <a href="/admin/partners.php?a=edit&id=<?= (int)$p['id'] ?>" class="btn btn--sm btn--primary">✎</a>
        <a href="/admin/partners.php?a=delete&id=<?= (int)$p['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить партнёра?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>
  <?php endif; ?>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header"><h2><?= $action==='edit' ? 'Редактирование партнёра' : 'Новый партнёр' ?></h2><a href="/admin/partners.php" class="btn btn--sm">← Список</a></div>
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
        <label>Уровень партнёрства</label>
        <select name="tier" class="select">
          <?php foreach (['strategic'=>'Стратегический','general'=>'Генеральный','media'=>'Медиа','partner'=>'Партнёр'] as $v=>$l): ?>
          <option value="<?= $v ?>" <?= ($current['tier'] ?? 'partner')===$v?'selected':'' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>Порядок</label>
        <input type="number" name="sort_order" class="input" value="<?= e($current['sort_order'] ?? 0) ?>">
      </div>
    </div>
    <div class="field">
      <label>Сайт партнёра (URL)</label>
      <input type="url" name="url" class="input" value="<?= e($current['url'] ?? '') ?>" placeholder="https://...">
    </div>
    <div class="field">
      <label>Логотип</label>
      <?php if (!empty($current['logo'])): ?>
        <div class="thumb" style="width:160px;height:80px;margin-bottom:8px;"><img src="<?= e($current['logo']) ?>"></div>
        <input type="hidden" name="logo_existing" value="<?= e($current['logo']) ?>">
      <?php endif; ?>
      <input type="file" name="logo" accept="image/*" class="input">
    </div>

    <?php $t = $tr[$EL] ?? []; ?>
    <h3 style="margin:24px 0 12px;color:var(--gold-2);text-transform:uppercase;font-size:13px;letter-spacing:.1em;">Контент (<?= strtoupper($EL) ?>)</h3>
    <div class="field">
      <label>Название <span class="req">*</span></label>
      <input type="text" name="name[<?= $EL ?>]" class="input" required value="<?= e($t['name'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Описание</label>
      <textarea name="description[<?= $EL ?>]" class="textarea" rows="3"><?= e($t['description'] ?? '') ?></textarea>
    </div>

    <?php if ($action==='edit'): foreach (['en','ru','tj'] as $l): if ($l===$EL) continue; $ot=$tr[$l] ?? null; ?>
    <input type="hidden" name="name[<?= $l ?>]" value="<?= e($ot['name'] ?? '') ?>">
    <textarea name="description[<?= $l ?>]" style="display:none"><?= e($ot['description'] ?? '') ?></textarea>
    <?php endforeach; endif; ?>

    <button type="submit" class="btn btn--primary" style="margin-top:16px;">💾 Сохранить</button>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
