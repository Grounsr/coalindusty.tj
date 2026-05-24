<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Пакеты инвесторов';
$showLangSwitcher = true;
$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id) {
    csrf_required();
    DB::delete('investor_packages', 'id=?', [$id]);
    log_action('delete_investor_package', 'investor_package', $id);
    flash('success', 'Удалено');
    header('Location: /admin/investor_packages.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create','edit'], true)) {
    csrf_required();
    $data = [
        'icon'        => trim($_POST['icon'] ?? ''),
        'is_featured' => !empty($_POST['is_featured']) ? 1 : 0,
        'sort_order'  => (int)($_POST['sort_order'] ?? 0),
    ];
    if ($action === 'create') { $id = DB::insert('investor_packages', $data); log_action('create_investor_package','investor_package',$id); }
    else { DB::update('investor_packages', $data, 'id=:id', ['id'=>$id]); log_action('update_investor_package','investor_package',$id); }
    foreach (['en','ru','tj'] as $lang) {
        $td = [
            'name'     => trim($_POST['name'][$lang] ?? ''),
            'tagline'  => trim($_POST['tagline'][$lang] ?? ''),
            'benefits' => trim($_POST['benefits'][$lang] ?? ''),
        ];
        if (!$td['name']) continue;
        $exists = DB::value('SELECT package_id FROM investor_package_translations WHERE package_id=? AND lang=?', [$id, $lang]);
        if ($exists) DB::update('investor_package_translations', $td, 'package_id=:p AND lang=:l', ['p'=>$id,'l'=>$lang]);
        else DB::insert('investor_package_translations', array_merge($td, ['package_id'=>$id,'lang'=>$lang]));
    }
    flash('success', 'Сохранено');
    header('Location: /admin/investor_packages.php?a=edit&id='.$id.'&el='.$EL); exit;
}

if ($action === 'edit' && $id) {
    $current = DB::row('SELECT * FROM investor_packages WHERE id=?', [$id]);
    $tr = [];
    foreach (DB::all('SELECT * FROM investor_package_translations WHERE package_id=?', [$id]) as $t) $tr[$t['lang']] = $t;
}

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action === 'list'):
$list = DB::all('SELECT ip.*, ipt.name, ipt.tagline FROM investor_packages ip LEFT JOIN investor_package_translations ipt ON ipt.package_id=ip.id AND ipt.lang="ru" ORDER BY ip.sort_order'); ?>
<div class="card">
  <div class="card-header"><h2>Инвестиционные пакеты</h2><a href="/admin/investor_packages.php?a=create" class="btn btn--primary">+ Добавить пакет</a></div>
  <?php if (!$list): ?><div class="empty">Пакеты не добавлены.</div><?php else: ?>
  <table class="data">
    <thead><tr><th>#</th><th>Иконка</th><th>Название (RU)</th><th>Слоган</th><th>Избранный</th><th>Порядок</th><th class="actions">Действия</th></tr></thead>
    <tbody><?php foreach ($list as $i=>$p): ?>
    <tr>
      <td><?= str_pad((string)($i+1),2,'0',STR_PAD_LEFT) ?></td>
      <td><code><?= e($p['icon'] ?? '') ?></code></td>
      <td><strong><?= e($p['name'] ?? '—') ?></strong></td>
      <td style="color:var(--ink-3);font-size:13px;"><?= e($p['tagline'] ?? '') ?></td>
      <td><?= $p['is_featured'] ? '<span class="badge badge--ok">да</span>' : '<span class="badge badge--neutral">нет</span>' ?></td>
      <td><?= (int)$p['sort_order'] ?></td>
      <td class="actions">
        <a href="/admin/investor_packages.php?a=edit&id=<?= (int)$p['id'] ?>" class="btn btn--sm btn--primary">✎</a>
        <a href="/admin/investor_packages.php?a=delete&id=<?= (int)$p['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить пакет?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>
  <?php endif; ?>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header"><h2><?= $action==='edit' ? 'Редактирование пакета' : 'Новый пакет' ?></h2><a href="/admin/investor_packages.php" class="btn btn--sm">← Список</a></div>
  <form method="POST">
    <?= csrf_field() ?>
    <div class="field-row--3">
      <div class="field">
        <label>Иконка (slug)</label>
        <input type="text" name="icon" class="input" value="<?= e($current['icon'] ?? '') ?>" placeholder="diamond, star...">
      </div>
      <div class="field">
        <label>Порядок</label>
        <input type="number" name="sort_order" class="input" value="<?= e($current['sort_order'] ?? 0) ?>">
      </div>
      <div class="field">
        <label class="check" style="margin-top:28px;"><input type="checkbox" name="is_featured" value="1" <?= !empty($current['is_featured'])?'checked':'' ?>> Рекомендуемый (избранный)</label>
      </div>
    </div>

    <?php $t = $tr[$EL] ?? []; ?>
    <h3 style="margin:24px 0 12px;color:var(--gold-2);text-transform:uppercase;font-size:13px;letter-spacing:.1em;">Контент (<?= strtoupper($EL) ?>)</h3>
    <div class="field">
      <label>Название <span class="req">*</span></label>
      <input type="text" name="name[<?= $EL ?>]" class="input" required value="<?= e($t['name'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Слоган</label>
      <input type="text" name="tagline[<?= $EL ?>]" class="input" value="<?= e($t['tagline'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Преимущества (по одному на строку)</label>
      <textarea name="benefits[<?= $EL ?>]" class="textarea" rows="6" placeholder="Логотип на баннерах&#10;Участие в пресс-конференции&#10;..."><?= e($t['benefits'] ?? '') ?></textarea>
      <p class="help">Каждая строка — отдельное преимущество</p>
    </div>

    <?php if ($action==='edit'): foreach (['en','ru','tj'] as $l): if ($l===$EL) continue; $ot=$tr[$l] ?? null; ?>
    <input type="hidden" name="name[<?= $l ?>]" value="<?= e($ot['name'] ?? '') ?>">
    <input type="hidden" name="tagline[<?= $l ?>]" value="<?= e($ot['tagline'] ?? '') ?>">
    <textarea name="benefits[<?= $l ?>]" style="display:none"><?= e($ot['benefits'] ?? '') ?></textarea>
    <?php endforeach; endif; ?>

    <button type="submit" class="btn btn--primary" style="margin-top:16px;">💾 Сохранить</button>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
