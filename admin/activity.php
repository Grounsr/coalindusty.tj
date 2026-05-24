<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Журнал действий';

// Filters
$fAdmin  = (int)($_GET['admin'] ?? 0);
$fAction = trim($_GET['action'] ?? '');
$fFrom   = trim($_GET['from'] ?? '');
$fTo     = trim($_GET['to'] ?? '');
$page    = max(1, (int)($_GET['p'] ?? 1));
$perPage = 50;

$where = ['1=1']; $params = [];
if ($fAdmin)  { $where[] = 'al.admin_id=?'; $params[] = $fAdmin; }
if ($fAction) { $where[] = 'al.action LIKE ?'; $params[] = '%'.$fAction.'%'; }
if ($fFrom)   { $where[] = 'al.created_at >= ?'; $params[] = $fFrom.' 00:00:00'; }
if ($fTo)     { $where[] = 'al.created_at <= ?'; $params[] = $fTo.' 23:59:59'; }

$whereStr = implode(' AND ', $where);
$total = (int)DB::value('SELECT COUNT(*) FROM activity_log al WHERE '.$whereStr, $params);
$totalPages = (int)ceil($total / $perPage);
$offset = ($page - 1) * $perPage;

$list = DB::all(
    'SELECT al.*, a.username, a.full_name FROM activity_log al LEFT JOIN admins a ON a.id=al.admin_id WHERE '.$whereStr.' ORDER BY al.created_at DESC LIMIT '.((int)$perPage).' OFFSET '.((int)$offset),
    $params
);

$admins = DB::all('SELECT id, username, full_name FROM admins ORDER BY full_name');

// Action type groups for coloring
$actionColors = [
    'create' => 'badge--ok',
    'update' => 'badge--gold',
    'delete' => 'badge--danger',
    'login'  => 'badge--neutral',
    'logout' => 'badge--neutral',
];
function actionBadge(string $action): string {
    global $actionColors;
    foreach ($actionColors as $prefix => $cls) {
        if (str_starts_with($action, $prefix) || str_contains($action, $prefix)) return $cls;
    }
    return 'badge';
}

require __DIR__ . '/_layout_top.php';
?>

<div class="card">
  <div class="card-header">
    <h2>Журнал действий <span class="badge badge--neutral"><?= number_format($total) ?></span></h2>
    <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <select name="admin" class="select" style="width:160px;" onchange="this.form.submit()">
        <option value="">— Все админы —</option>
        <?php foreach ($admins as $a): ?><option value="<?= (int)$a['id'] ?>" <?= $fAdmin===$a['id']?'selected':'' ?>><?= e($a['username']) ?></option><?php endforeach; ?>
      </select>
      <input type="text" name="action" class="input" style="width:160px;" value="<?= e($fAction) ?>" placeholder="Тип действия...">
      <input type="date" name="from" class="input" style="width:140px;" value="<?= e($fFrom) ?>">
      <input type="date" name="to" class="input" style="width:140px;" value="<?= e($fTo) ?>">
      <button type="submit" class="btn">Применить</button>
      <a href="/admin/activity.php" class="btn">Сбросить</a>
    </form>
  </div>

  <?php if (!$list): ?>
  <div class="empty">Действий не найдено.</div>
  <?php else: ?>
  <table class="data" style="font-size:13px;">
    <thead>
      <tr><th>Дата</th><th>Администратор</th><th>Действие</th><th>Тип объекта</th><th>ID объекта</th><th>Детали</th><th>IP</th></tr>
    </thead>
    <tbody><?php foreach ($list as $log): ?>
    <tr>
      <td style="white-space:nowrap;"><?= date('d.m.Y H:i:s', strtotime($log['created_at'])) ?></td>
      <td>
        <?php if ($log['username']): ?>
          <strong><?= e($log['username']) ?></strong><br>
          <small style="color:var(--ink-3);"><?= e($log['full_name']) ?></small>
        <?php else: ?><span style="color:var(--ink-3);">—</span><?php endif; ?>
      </td>
      <td><span class="badge <?= actionBadge($log['action']) ?>"><?= e($log['action']) ?></span></td>
      <td style="color:var(--ink-3);"><?= e($log['entity_type'] ?? '') ?></td>
      <td style="color:var(--ink-3);"><?= $log['entity_id'] ? '#'.e((string)$log['entity_id']) : '' ?></td>
      <td style="font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= e($log['details'] ?? '') ?>"><?= e(mb_substr($log['details'] ?? '', 0, 80)) ?></td>
      <td style="font-size:11px;color:var(--ink-3);font-family:monospace;"><?= e($log['ip_address'] ?? '') ?></td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>

  <?php if ($totalPages > 1): ?>
  <div style="padding:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <span style="color:var(--ink-3);font-size:13px;">Страница <?= $page ?> из <?= $totalPages ?> (<?= $total ?> записей)</span>
    <?php
    $qs = http_build_query(array_filter(['admin'=>$fAdmin,'action'=>$fAction,'from'=>$fFrom,'to'=>$fTo]));
    for ($pg = max(1,$page-3); $pg <= min($totalPages,$page+3); $pg++):
    ?><a href="/admin/activity.php?p=<?= $pg ?><?= $qs?'&'.$qs:'' ?>" class="btn btn--sm <?= $pg===$page?'btn--primary':'' ?>"><?= $pg ?></a><?php endfor; ?>
    <?php if ($page < $totalPages): ?><a href="/admin/activity.php?p=<?= $page+1 ?><?= $qs?'&'.$qs:'' ?>" class="btn btn--sm">→</a><?php endif; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
