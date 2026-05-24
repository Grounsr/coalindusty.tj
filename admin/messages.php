<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Сообщения';
$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'set_status' && $id) {
    csrf_required();
    $ns = $_POST['status'] ?? '';
    if (in_array($ns, ['new','read','replied'], true)) {
        DB::update('contact_messages', ['status'=>$ns], 'id=:id', ['id'=>$id]);
        log_action('message_'.$ns, 'contact_message', $id);
    }
    header('Location: /admin/messages.php'); exit;
}

if ($action === 'delete' && $id) {
    csrf_required();
    DB::delete('contact_messages', 'id=?', [$id]);
    log_action('delete_message', 'contact_message', $id);
    flash('success', 'Сообщение удалено');
    header('Location: /admin/messages.php'); exit;
}

if ($action === 'view' && $id) {
    $msg = DB::row('SELECT * FROM contact_messages WHERE id=?', [$id]);
    if ($msg && $msg['status'] === 'new') {
        DB::update('contact_messages', ['status'=>'read'], 'id=:id', ['id'=>$id]);
        $msg['status'] = 'read';
    }
}

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action === 'view' && !empty($msg)): ?>
<div class="card">
  <div class="card-header"><h2>Сообщение #<?= (int)$msg['id'] ?></h2><a href="/admin/messages.php" class="btn btn--sm">← Список</a></div>
  <div style="padding:20px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
      <div>
        <p><strong>Отправитель:</strong> <?= e($msg['full_name']) ?></p>
        <p><strong>Email:</strong> <a href="mailto:<?= e($msg['email']) ?>"><?= e($msg['email']) ?></a></p>
        <p><strong>Телефон:</strong> <?= e($msg['phone'] ?? '—') ?></p>
      </div>
      <div>
        <p><strong>Тема:</strong> <?= e($msg['subject'] ?? '—') ?></p>
        <p><strong>Статус:</strong> <span class="badge"><?= e($msg['status']) ?></span></p>
        <p><strong>Дата:</strong> <?= date('d.m.Y H:i', strtotime($msg['created_at'])) ?></p>
      </div>
    </div>
    <strong>Текст сообщения:</strong>
    <div style="background:var(--surface-2);padding:16px;border-radius:6px;margin-top:8px;white-space:pre-wrap;"><?= e($msg['message']) ?></div>
  </div>
  <div style="padding:0 20px 20px;display:flex;gap:8px;">
    <form method="POST" action="/admin/messages.php?a=set_status&id=<?= (int)$msg['id'] ?>">
      <?= csrf_field() ?>
      <button type="submit" name="status" value="read" class="btn <?= $msg['status']==='read'?'btn--primary':'' ?>">✓ Прочитано</button>
      <button type="submit" name="status" value="replied" class="btn <?= $msg['status']==='replied'?'btn--primary':'' ?>">↩ Отвечено</button>
    </form>
    <a href="mailto:<?= e($msg['email']) ?>?subject=Re: <?= rawurlencode($msg['subject'] ?? 'Ваше сообщение') ?>" class="btn">✉ Ответить по email</a>
  </div>
</div>

<?php else:
$list = DB::all('SELECT * FROM contact_messages ORDER BY created_at DESC'); ?>
<div class="card">
  <div class="card-header"><h2>Сообщения с сайта <span class="badge badge--neutral"><?= count($list) ?></span></h2></div>
  <?php
  $newCount = count(array_filter($list, fn($m)=>$m['status']==='new'));
  if ($newCount): ?>
  <div style="padding:8px 16px;background:var(--gold-1);color:#000;font-size:13px;font-weight:600;">📬 Новых сообщений: <?= $newCount ?></div>
  <?php endif; ?>
  <?php if (!$list): ?><div class="empty">Сообщений нет.</div><?php else: ?>
  <table class="data" style="font-size:13px;">
    <thead><tr><th>#</th><th>Отправитель</th><th>Тема</th><th>Превью</th><th>Статус</th><th>Дата</th><th class="actions">Действия</th></tr></thead>
    <tbody><?php foreach ($list as $m): ?>
    <tr style="<?= $m['status']==='new' ? 'font-weight:600;' : '' ?>">
      <td><?= str_pad((string)$m['id'],4,'0',STR_PAD_LEFT) ?></td>
      <td>
        <?= e($m['full_name']) ?><br>
        <a href="mailto:<?= e($m['email']) ?>" style="color:var(--ink-3);font-size:12px;"><?= e($m['email']) ?></a>
      </td>
      <td><?= e($m['subject'] ?? '—') ?></td>
      <td style="color:var(--ink-3);font-size:12px;"><?= e(mb_substr($m['message'], 0, 80)) ?>…</td>
      <td>
        <?php $sc=['new'=>'badge--warn','read'=>'badge--neutral','replied'=>'badge--ok']; ?>
        <span class="badge <?= $sc[$m['status']] ?? '' ?>"><?= e($m['status']) ?></span>
      </td>
      <td style="white-space:nowrap;"><?= date('d.m.Y', strtotime($m['created_at'])) ?></td>
      <td class="actions">
        <a href="/admin/messages.php?a=view&id=<?= (int)$m['id'] ?>" class="btn btn--sm btn--primary">👁</a>
        <form method="POST" action="/admin/messages.php?a=set_status&id=<?= (int)$m['id'] ?>" style="display:inline;">
          <?= csrf_field() ?><button name="status" value="replied" class="btn btn--sm" title="Отметить отвеченным">↩</button>
        </form>
        <a href="/admin/messages.php?a=delete&id=<?= (int)$m['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить сообщение?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
