<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Запросы инвесторов';
$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'export') {
    csrf_required();
    $rows = DB::all('SELECT * FROM investor_inquiries ORDER BY created_at DESC');
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="investor_inquiries_'.date('Ymd').'.csv"');
    echo "\xEF\xBB\xBF";
    $out = fopen('php://output','w');
    fputcsv($out, ['ID','ФИО','Email','Телефон','Компания','Должность','Страна','Интерес','Сообщение','Статус','Дата']);
    foreach ($rows as $r) fputcsv($out, [$r['id'],$r['full_name'],$r['email'],$r['phone']??'',$r['company']??'',$r['position']??'',$r['country']??'',$r['interest_level']??'',$r['message']??'',$r['status'],$r['created_at']]);
    fclose($out); exit;
}

if ($action === 'set_status' && $id) {
    csrf_required();
    $ns = $_POST['status'] ?? '';
    if (in_array($ns, ['new','contacted','closed'], true)) {
        DB::update('investor_inquiries', ['status'=>$ns], 'id=:id', ['id'=>$id]);
        log_action('inquiry_status_'.$ns, 'investor_inquiry', $id);
        flash('success', 'Статус обновлён');
    }
    header('Location: /admin/investor_inquiries.php'); exit;
}

if ($action === 'delete' && $id) {
    csrf_required();
    DB::delete('investor_inquiries', 'id=?', [$id]);
    log_action('delete_inquiry', 'investor_inquiry', $id);
    flash('success', 'Удалено');
    header('Location: /admin/investor_inquiries.php'); exit;
}

if ($action === 'view' && $id) {
    $item = DB::row('SELECT * FROM investor_inquiries WHERE id=?', [$id]);
    if ($item && $item['status'] === 'new') {
        DB::update('investor_inquiries', ['status'=>'contacted'], 'id=:id', ['id'=>$id]);
        $item['status'] = 'contacted';
    }
}

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action === 'view' && !empty($item)): ?>
<div class="card">
  <div class="card-header"><h2>Запрос инвестора #<?= (int)$item['id'] ?></h2><a href="/admin/investor_inquiries.php" class="btn btn--sm">← Список</a></div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;padding:20px;">
    <div>
      <p><strong>ФИО:</strong> <?= e($item['full_name']) ?></p>
      <p><strong>Email:</strong> <a href="mailto:<?= e($item['email']) ?>"><?= e($item['email']) ?></a></p>
      <p><strong>Телефон:</strong> <?= e($item['phone'] ?? '—') ?></p>
      <p><strong>Компания:</strong> <?= e($item['company'] ?? '—') ?></p>
      <p><strong>Должность:</strong> <?= e($item['position'] ?? '—') ?></p>
    </div>
    <div>
      <p><strong>Страна:</strong> <?= e($item['country'] ?? '—') ?></p>
      <p><strong>Уровень интереса:</strong> <?= e($item['interest_level'] ?? '—') ?></p>
      <p><strong>Статус:</strong> <span class="badge"><?= e($item['status']) ?></span></p>
      <p><strong>Дата:</strong> <?= date('d.m.Y H:i', strtotime($item['created_at'])) ?></p>
    </div>
  </div>
  <?php if ($item['message']): ?>
  <div style="padding:0 20px 20px;">
    <strong>Сообщение:</strong>
    <div style="background:var(--surface-2);padding:16px;border-radius:6px;margin-top:8px;white-space:pre-wrap;"><?= e($item['message']) ?></div>
  </div>
  <?php endif; ?>
  <div style="padding:0 20px 20px;display:flex;gap:8px;">
    <form method="POST" action="/admin/investor_inquiries.php?a=set_status&id=<?= (int)$item['id'] ?>">
      <?= csrf_field() ?>
      <?php foreach (['new'=>'Новый','contacted'=>'Связались','closed'=>'Закрыт'] as $v=>$l): ?>
      <button type="submit" name="status" value="<?= $v ?>" class="btn <?= $item['status']===$v?'btn--primary':'' ?>"><?= $l ?></button>
      <?php endforeach; ?>
    </form>
  </div>
</div>

<?php else:
$list = DB::all('SELECT * FROM investor_inquiries ORDER BY created_at DESC'); ?>
<div class="card">
  <div class="card-header">
    <h2>Запросы инвесторов <span class="badge badge--neutral"><?= count($list) ?></span></h2>
    <form method="GET" style="margin-left:auto;">
      <input type="hidden" name="a" value="export">
      <input type="hidden" name="_csrf" value="<?= e($_SESSION['csrf']) ?>">
      <button type="submit" class="btn btn--primary" onclick="this.form.method='post'">⬇ CSV</button>
    </form>
  </div>
  <?php if (!$list): ?><div class="empty">Запросов нет.</div><?php else: ?>
  <table class="data" style="font-size:13px;">
    <thead><tr><th>#</th><th>ФИО / Email</th><th>Компания</th><th>Страна</th><th>Интерес</th><th>Статус</th><th>Дата</th><th class="actions">Действия</th></tr></thead>
    <tbody><?php foreach ($list as $r): ?>
    <tr>
      <td><?= str_pad((string)$r['id'],4,'0',STR_PAD_LEFT) ?></td>
      <td>
        <strong><?= e($r['full_name']) ?></strong><br>
        <a href="mailto:<?= e($r['email']) ?>" style="color:var(--ink-3);font-size:12px;"><?= e($r['email']) ?></a>
      </td>
      <td><?= e($r['company'] ?? '—') ?><?= $r['position']?'<br><small style="color:var(--ink-3)">'.e($r['position']).'</small>':'' ?></td>
      <td><?= e($r['country'] ?? '—') ?></td>
      <td style="font-size:12px;"><?= e(mb_substr($r['interest_level'] ?? '—', 0, 40)) ?></td>
      <td>
        <?php $sc=['new'=>'badge--warn','contacted'=>'badge--ok','closed'=>'badge--neutral']; ?>
        <span class="badge <?= $sc[$r['status']] ?? '' ?>"><?= e($r['status']) ?></span>
      </td>
      <td style="white-space:nowrap;"><?= date('d.m.Y', strtotime($r['created_at'])) ?></td>
      <td class="actions">
        <a href="/admin/investor_inquiries.php?a=view&id=<?= (int)$r['id'] ?>" class="btn btn--sm btn--primary">👁</a>
        <a href="/admin/investor_inquiries.php?a=delete&id=<?= (int)$r['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить запрос?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
