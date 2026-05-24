<?php
require_once __DIR__ . '/_bootstrap.php';
$adminTitle = 'Дашборд';

$stats = [
    'reg_total'    => (int)DB::value('SELECT COUNT(*) FROM registrations'),
    'reg_verified' => (int)DB::value('SELECT COUNT(*) FROM registrations WHERE status IN ("verified","confirmed","attended")'),
    'reg_pending'  => (int)DB::value('SELECT COUNT(*) FROM registrations WHERE status="pending"'),
    'reg_today'    => (int)DB::value('SELECT COUNT(*) FROM registrations WHERE DATE(created_at) = CURDATE()'),
    'investors'    => (int)DB::value('SELECT COUNT(*) FROM investor_inquiries WHERE status="new"'),
    'messages'     => (int)DB::value('SELECT COUNT(*) FROM contact_messages WHERE status="new"'),
    'news_total'   => (int)DB::value('SELECT COUNT(*) FROM news'),
    'news_pub'     => (int)DB::value('SELECT COUNT(*) FROM news WHERE is_published=1'),
    'views_today'  => (int)DB::value('SELECT COUNT(*) FROM page_views WHERE DATE(created_at) = CURDATE()'),
    'views_total'  => (int)DB::value('SELECT COUNT(*) FROM page_views'),
];

$recentReg = DB::all('SELECT id, full_name, email, country, participation_type, status, created_at FROM registrations ORDER BY created_at DESC LIMIT 8');
$recentLog = DB::all('
    SELECT al.*, a.full_name as admin_name
    FROM activity_log al
    LEFT JOIN admins a ON a.id = al.admin_id
    ORDER BY al.created_at DESC LIMIT 10
');

$year = current_year();
$forumDate = $year['event_date'] ?? '2026-11-25';
$daysToForum = max(0, floor((strtotime($forumDate) - time()) / 86400));

require __DIR__ . '/_layout_top.php';
?>

<div class="kpi-grid">
  <div class="kpi">
    <div class="kpi-icon">📅</div>
    <div class="kpi-label">До форума</div>
    <div class="kpi-value"><?= $daysToForum ?></div>
    <div class="kpi-delta">дней · <?= date('d.m.Y', strtotime($forumDate)) ?></div>
  </div>
  <div class="kpi">
    <div class="kpi-icon">✅</div>
    <div class="kpi-label">Регистраций всего</div>
    <div class="kpi-value"><?= $stats['reg_total'] ?></div>
    <div class="kpi-delta">+<?= $stats['reg_today'] ?> сегодня · <?= $stats['reg_verified'] ?> подтверждены</div>
  </div>
  <div class="kpi">
    <div class="kpi-icon">💰</div>
    <div class="kpi-label">Запросы инвесторов</div>
    <div class="kpi-value"><?= $stats['investors'] ?></div>
    <div class="kpi-delta">новых · требуют ответа</div>
  </div>
  <div class="kpi">
    <div class="kpi-icon">💬</div>
    <div class="kpi-label">Сообщения</div>
    <div class="kpi-value"><?= $stats['messages'] ?></div>
    <div class="kpi-delta">не прочитано</div>
  </div>
  <div class="kpi">
    <div class="kpi-icon">📰</div>
    <div class="kpi-label">Новости</div>
    <div class="kpi-value"><?= $stats['news_pub'] ?></div>
    <div class="kpi-delta">опубликовано · из <?= $stats['news_total'] ?></div>
  </div>
  <div class="kpi">
    <div class="kpi-icon">👁</div>
    <div class="kpi-label">Просмотры</div>
    <div class="kpi-value"><?= $stats['views_today'] ?></div>
    <div class="kpi-delta">сегодня · всего <?= $stats['views_total'] ?></div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h2>Последние регистрации</h2>
    <a href="/admin/registrations.php" class="btn btn--sm">Все заявки</a>
  </div>
  <?php if (!$recentReg): ?>
    <div class="empty">Регистраций пока нет.</div>
  <?php else: ?>
  <div class="table-wrap">
    <table class="data">
      <thead><tr><th>#</th><th>ФИО</th><th>Email</th><th>Страна</th><th>Тип</th><th>Статус</th><th>Дата</th></tr></thead>
      <tbody>
      <?php foreach ($recentReg as $r):
        $statusMap = ['pending'=>['warn','ожидает'], 'verified'=>['ok','подтверждена'], 'confirmed'=>['ok','подтверждена'], 'rejected'=>['warn','отклонена'], 'attended'=>['info','присутствовал']];
        $sm = $statusMap[$r['status']] ?? ['neutral', $r['status']];
      ?>
      <tr>
        <td>#<?= str_pad((string)$r['id'], 5, '0', STR_PAD_LEFT) ?></td>
        <td><?= e($r['full_name']) ?></td>
        <td><a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a></td>
        <td><?= e($r['country']) ?></td>
        <td><span class="badge"><?= e($r['participation_type']) ?></span></td>
        <td><span class="badge badge--<?= $sm[0] ?>"><?= e($sm[1]) ?></span></td>
        <td><?= date('d.m H:i', strtotime($r['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header"><h2>Журнал последних действий</h2><a href="/admin/activity.php" class="btn btn--sm">Полный журнал</a></div>
  <?php if (!$recentLog): ?>
    <div class="empty">Журнал пуст.</div>
  <?php else: ?>
  <table class="data">
    <thead><tr><th>Время</th><th>Администратор</th><th>Действие</th><th>Объект</th><th>IP</th></tr></thead>
    <tbody>
      <?php foreach ($recentLog as $l): ?>
      <tr>
        <td><?= date('d.m H:i', strtotime($l['created_at'])) ?></td>
        <td><?= e($l['admin_name'] ?? '—') ?></td>
        <td><?= e($l['action']) ?></td>
        <td><?= e(($l['entity_type'] ?? '') . ($l['entity_id'] ? ' #' . $l['entity_id'] : '')) ?></td>
        <td style="color:#5b6072;font-size:12px;"><?= e($l['ip_address'] ?? '—') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
