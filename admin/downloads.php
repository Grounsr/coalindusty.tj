<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Документы для скачивания';
$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id) {
    csrf_required();
    $r = DB::row('SELECT file_path FROM downloads WHERE id=?', [$id]);
    if ($r) delete_file_safe($r['file_path']);
    DB::delete('downloads', 'id=?', [$id]);
    log_action('delete_download', 'download', $id);
    flash('success', 'Удалено');
    header('Location: /admin/downloads.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create','edit'], true)) {
    csrf_required();
    $filePath = null;
    $origName = $_POST['original_name_existing'] ?? null;
    $fileSize = isset($current) ? ($current['file_size'] ?? null) : null;

    if (!empty($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
        $uploaded = admin_upload('file_upload', 'downloads', ['application/pdf','application/octet-stream','application/x-pdf']);
        if (!$uploaded) {
            // Try generic upload
            $file = $_FILES['file_upload'];
            $dir = UPLOAD_DIR . '/downloads';
            @mkdir($dir, 0755, true);
            $fname = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.pdf';
            if (move_uploaded_file($file['tmp_name'], $dir . '/' . $fname)) {
                $filePath = '/uploads/downloads/' . $fname;
                $origName = $file['name'];
                $fileSize = (int)$file['size'];
            }
        } else {
            $filePath = $uploaded;
            $origName = $_FILES['file_upload']['name'];
            $fileSize = (int)$_FILES['file_upload']['size'];
        }
    }
    if (!$filePath && $action === 'edit') {
        $filePath = $_POST['file_existing'] ?? null;
    }

    $data = [
        'forum_year_id' => (int)$_POST['forum_year_id'] ?: null,
        'doc_type'      => $_POST['doc_type'] ?? 'other',
        'lang'          => $_POST['lang'] ?? 'ru',
        'file_path'     => $filePath,
        'original_name' => $origName,
        'file_size'     => $fileSize,
    ];
    if ($action === 'create') {
        if (!$data['file_path']) { flash('error', 'Файл не загружен'); header('Location: /admin/downloads.php?a=create'); exit; }
        $id = DB::insert('downloads', $data);
        log_action('create_download', 'download', $id);
    } else {
        DB::update('downloads', array_filter($data, fn($v) => $v !== null), 'id=:id', ['id'=>$id]);
        log_action('update_download', 'download', $id);
    }
    flash('success', 'Сохранено');
    header('Location: /admin/downloads.php'); exit;
}

if ($action === 'edit' && $id) {
    $current = DB::row('SELECT * FROM downloads WHERE id=?', [$id]);
}
$years = DB::all('SELECT id, year FROM forum_years ORDER BY year DESC');

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action === 'list'):
$list = DB::all('SELECT d.*, fy.year FROM downloads d LEFT JOIN forum_years fy ON fy.id=d.forum_year_id ORDER BY fy.year DESC, d.doc_type, d.lang'); ?>
<div class="card">
  <div class="card-header"><h2>Документы для скачивания</h2><a href="/admin/downloads.php?a=create" class="btn btn--primary">+ Загрузить документ</a></div>
  <?php if (!$list): ?><div class="empty">Документов нет.</div><?php else: ?>
  <table class="data">
    <thead><tr><th>Год</th><th>Тип</th><th>Язык</th><th>Файл</th><th>Размер</th><th>Обновлён</th><th class="actions">Действия</th></tr></thead>
    <tbody><?php
    $lastKey = '';
    foreach ($list as $d):
        $key = ($d['year'] ?? '—') . '_' . $d['doc_type'];
        if ($key !== $lastKey):
            $lastKey = $key;
    ?>
    <tr style="background:var(--surface-2);">
      <td colspan="7" style="font-size:12px;font-weight:600;color:var(--gold-2);padding:6px 12px;">
        <?= $d['year'] ? (int)$d['year'] : 'Без года' ?> — <?= e($d['doc_type']) ?>
      </td>
    </tr>
    <?php endif; ?>
    <tr>
      <td></td>
      <td><?= e($d['doc_type']) ?></td>
      <td><span class="badge"><?= strtoupper(e($d['lang'])) ?></span></td>
      <td>
        <?php if ($d['file_path']): ?>
          <a href="<?= e($d['file_path']) ?>" target="_blank" style="font-size:12px;">
            📄 <?= e($d['original_name'] ?? basename($d['file_path'])) ?>
          </a>
        <?php else: ?>—<?php endif; ?>
      </td>
      <td style="font-size:12px;color:var(--ink-3);"><?= $d['file_size'] ? number_format($d['file_size']/1024, 1).' KB' : '—' ?></td>
      <td style="font-size:12px;"><?= $d['updated_at'] ? date('d.m.Y', strtotime($d['updated_at'])) : '—' ?></td>
      <td class="actions">
        <a href="/admin/downloads.php?a=edit&id=<?= (int)$d['id'] ?>" class="btn btn--sm btn--primary">✎</a>
        <a href="/admin/downloads.php?a=delete&id=<?= (int)$d['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить документ?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>
  <?php endif; ?>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header"><h2><?= $action==='edit' ? 'Редактирование документа' : 'Загрузить документ' ?></h2><a href="/admin/downloads.php" class="btn btn--sm">← Список</a></div>
  <form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="field-row--3">
      <div class="field">
        <label>Год форума</label>
        <select name="forum_year_id" class="select">
          <option value="">— Без привязки —</option>
          <?php foreach ($years as $y): ?><option value="<?= (int)$y['id'] ?>" <?= ($current['forum_year_id'] ?? 0)==$y['id']?'selected':'' ?>><?= (int)$y['year'] ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>Тип документа</label>
        <select name="doc_type" class="select">
          <?php foreach (['program'=>'Программа','concept'=>'Концепция','other'=>'Другое'] as $v=>$l): ?>
          <option value="<?= $v ?>" <?= ($current['doc_type'] ?? 'other')===$v?'selected':'' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>Язык документа</label>
        <select name="lang" class="select">
          <?php foreach (['ru'=>'Русский','en'=>'English','tj'=>'Тоҷикӣ'] as $v=>$l): ?>
          <option value="<?= $v ?>" <?= ($current['lang'] ?? 'ru')===$v?'selected':'' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="field">
      <label>PDF-файл <?= $action==='create' ? '<span class="req">*</span>' : '' ?></label>
      <?php if ($action==='edit' && !empty($current['file_path'])): ?>
        <p style="margin:0 0 8px;font-size:13px;">Текущий файл: <a href="<?= e($current['file_path']) ?>" target="_blank"><?= e($current['original_name'] ?? basename($current['file_path'])) ?></a></p>
        <input type="hidden" name="file_existing" value="<?= e($current['file_path']) ?>">
        <input type="hidden" name="original_name_existing" value="<?= e($current['original_name'] ?? '') ?>">
      <?php endif; ?>
      <input type="file" name="file_upload" accept=".pdf,application/pdf" class="input" <?= $action==='create'?'required':'' ?>>
      <p class="help">Принимается только PDF. Размер файла будет определён автоматически.</p>
    </div>
    <button type="submit" class="btn btn--primary" style="margin-top:16px;">💾 Сохранить</button>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
