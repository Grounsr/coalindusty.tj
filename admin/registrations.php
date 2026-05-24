<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Регистрации';
$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Export CSV
if ($action === 'export') {
    csrf_required();
    $rows = DB::all('SELECT r.*, fy.year FROM registrations r LEFT JOIN forum_years fy ON fy.id=r.forum_year_id ORDER BY r.created_at DESC');
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="registrations_' . date('Ymd') . '.csv"');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID','Год','ФИО','Email','Телефон','Страна','Город','Организация','Должность','Тип участия','Статус','Интересы','Диета','Комментарии','Фото','Паспорт','IP','Дата создания']);
    foreach ($rows as $r) {
        fputcsv($out, [
            str_pad((string)$r['id'], 6, '0', STR_PAD_LEFT),
            $r['year'] ?? '',
            $r['full_name'], $r['email'], $r['phone'] ?? '',
            $r['country'] ?? '', $r['city'] ?? '',
            $r['organization'] ?? '', $r['position'] ?? '',
            $r['participation_type'], $r['status'],
            $r['interests'] ?? '', $r['dietary'] ?? '', $r['comments'] ?? '',
            $r['photo_path'] ?? '', $r['passport_path'] ?? '',
            $r['ip_address'] ?? '', $r['created_at'],
        ]);
    }
    fclose($out);
    exit;
}

if ($action === 'set_status' && $id) {
    csrf_required();
    $newStatus = $_POST['status'] ?? '';
    $allowed = ['pending','verified','confirmed','rejected','attended'];
    if (in_array($newStatus, $allowed, true)) {
        DB::update('registrations', ['status'=>$newStatus], 'id=:id', ['id'=>$id]);
        if (in_array($newStatus, ['confirmed','rejected'], true)) {
            log_action('registration_'.$newStatus, 'registration', $id);
        }
        flash('success', 'Статус обновлён');
    }
    header('Location: /admin/registrations.php?' . http_build_query(array_filter(['status'=>$_POST['_back_status']??'','type'=>$_POST['_back_type']??'','q'=>$_POST['_back_q']??'','year'=>$_POST['_back_year']??'']))); exit;
}

if ($action === 'delete' && $id) {
    csrf_required();
    $r = DB::row('SELECT photo_path, passport_path FROM registrations WHERE id=?', [$id]);
    if ($r) { delete_file_safe($r['photo_path']); delete_file_safe($r['passport_path']); }
    DB::delete('registrations', 'id=?', [$id]);
    log_action('delete_registration', 'registration', $id);
    flash('success', 'Запись удалена');
    header('Location: /admin/registrations.php'); exit;
}

// Filters
$fStatus = $_GET['status'] ?? '';
$fType   = $_GET['type'] ?? '';
$fYear   = (int)($_GET['year'] ?? 0);
$fQ      = trim($_GET['q'] ?? '');

$years = DB::all('SELECT id, year FROM forum_years ORDER BY year DESC');
$statusLabels = ['pending'=>'На рассмотрении','verified'=>'Подтверждён email','confirmed'=>'Подтверждён','rejected'=>'Отклонён','attended'=>'Присутствовал'];
$typeBadge = ['delegate'=>'badge','speaker'=>'badge--ok','press'=>'badge--warn','investor'=>'badge--gold','sponsor'=>'badge--gold','observer'=>'badge--neutral'];

require __DIR__ . '/_layout_top.php';
?>

<?php
$where = ['1=1']; $params = [];
if ($fStatus) { $where[] = 'r.status=?'; $params[] = $fStatus; }
if ($fType)   { $where[] = 'r.participation_type=?'; $params[] = $fType; }
if ($fYear)   { $where[] = 'r.forum_year_id=?'; $params[] = $fYear; }
if ($fQ)      { $where[] = '(r.full_name LIKE ? OR r.email LIKE ?)'; $params[] = '%'.$fQ.'%'; $params[] = '%'.$fQ.'%'; }
$sql = 'SELECT r.*, fy.year FROM registrations r LEFT JOIN forum_years fy ON fy.id=r.forum_year_id WHERE '.implode(' AND ',$where).' ORDER BY r.created_at DESC';
$list = DB::all($sql, $params);
$total = DB::value('SELECT COUNT(*) FROM registrations r WHERE '.implode(' AND ',$where), $params);
?>

<div class="card">
  <div class="card-header">
    <h2>Регистрации <span class="badge badge--neutral"><?= (int)$total ?></span></h2>
    <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <input type="text" name="q" class="input" style="width:160px;" value="<?= e($fQ) ?>" placeholder="Поиск по имени/email">
      <select name="status" class="select" style="width:150px;" onchange="this.form.submit()">
        <option value="">— Все статусы —</option>
        <?php foreach ($statusLabels as $v=>$l): ?><option value="<?= $v ?>" <?= $fStatus===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
      </select>
      <select name="type" class="select" style="width:140px;" onchange="this.form.submit()">
        <option value="">— Все типы —</option>
        <?php foreach (['delegate','speaker','press','investor','sponsor','observer'] as $v): ?><option value="<?= $v ?>" <?= $fType===$v?'selected':'' ?>><?= $v ?></option><?php endforeach; ?>
      </select>
      <select name="year" class="select" style="width:100px;" onchange="this.form.submit()">
        <option value="">— Год —</option>
        <?php foreach ($years as $y): ?><option value="<?= (int)$y['id'] ?>" <?= $fYear===$y['id']?'selected':'' ?>><?= (int)$y['year'] ?></option><?php endforeach; ?>
      </select>
      <button type="submit" class="btn">Найти</button>
    </form>
    <form method="GET" style="margin-left:auto;">
      <input type="hidden" name="a" value="export">
      <input type="hidden" name="_csrf" value="<?= e($_SESSION['csrf']) ?>">
      <button type="submit" class="btn btn--primary" onclick="this.form.method='post'">⬇ CSV</button>
    </form>
  </div>
  <?php if (!$list): ?><div class="empty">Регистраций не найдено.</div><?php else: ?>
  <table class="data" style="font-size:13px;">
    <thead><tr><th>#</th><th>ФИО / Email</th><th>Страна</th><th>Организация</th><th>Тип</th><th>Статус</th><th>Фото</th><th>Паспорт</th><th>Дата</th><th class="actions">Действия</th></tr></thead>
    <tbody><?php foreach ($list as $r): ?>
    <tr>
      <td><?= str_pad((string)$r['id'], 6, '0', STR_PAD_LEFT) ?></td>
      <td>
        <strong><?= e($r['full_name']) ?></strong><br>
        <a href="mailto:<?= e($r['email']) ?>" style="font-size:12px;color:var(--ink-3);"><?= e($r['email']) ?></a>
        <?php if ($r['phone']): ?><br><small><?= e($r['phone']) ?></small><?php endif; ?>
      </td>
      <td><?= e($r['country'] ?? '') ?><?= $r['city'] ? '<br><small style="color:var(--ink-3)">'.e($r['city']).'</small>' : '' ?></td>
      <td><?= e($r['organization'] ?? '') ?><?= $r['position'] ? '<br><small style="color:var(--ink-3)">'.e($r['position']).'</small>' : '' ?></td>
      <td><span class="badge <?= $typeBadge[$r['participation_type']] ?? 'badge' ?>"><?= e($r['participation_type']) ?></span></td>
      <td>
        <form method="POST" action="/admin/registrations.php?a=set_status&id=<?= (int)$r['id'] ?>" style="display:flex;gap:4px;align-items:center;">
          <?= csrf_field() ?>
          <input type="hidden" name="_back_status" value="<?= e($fStatus) ?>">
          <input type="hidden" name="_back_type" value="<?= e($fType) ?>">
          <input type="hidden" name="_back_q" value="<?= e($fQ) ?>">
          <input type="hidden" name="_back_year" value="<?= $fYear ?>">
          <select name="status" class="select" style="font-size:12px;padding:2px 4px;" onchange="this.form.submit()">
            <?php foreach ($statusLabels as $v=>$l): ?><option value="<?= $v ?>" <?= $r['status']===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </form>
      </td>
      <td>
        <?php if ($r['photo_path']): ?>
          <a href="<?= e($r['photo_path']) ?>" target="_blank"><div class="thumb" style="width:32px;height:32px;display:inline-block;"><img src="<?= e($r['photo_path']) ?>"></div></a>
        <?php else: ?>—<?php endif; ?>
      </td>
      <td>
        <?php if ($r['passport_path']): ?>
          <a href="<?= e($r['passport_path']) ?>" target="_blank" class="btn btn--sm">📄</a>
        <?php else: ?>—<?php endif; ?>
      </td>
      <td style="white-space:nowrap;"><?= date('d.m.Y', strtotime($r['created_at'])) ?></td>
      <td class="actions">
        <a href="/admin/registrations.php?a=delete&id=<?= (int)$r['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить регистрацию?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
