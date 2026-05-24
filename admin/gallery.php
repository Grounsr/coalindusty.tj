<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Галерея форумов';
$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id) {
    csrf_required();
    $r = DB::row('SELECT file_path, thumbnail FROM year_media WHERE id=?', [$id]);
    if ($r) { delete_file_safe($r['file_path']); delete_file_safe($r['thumbnail']); }
    DB::delete('year_media', 'id=?', [$id]);
    log_action('delete_media', 'year_media', $id);
    flash('success', 'Удалено');
    header('Location: /admin/gallery.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit') {
    csrf_required();
    $data = [
        'forum_year_id' => (int)$_POST['forum_year_id'],
        'caption_en'    => trim($_POST['caption_en'] ?? ''),
        'caption_ru'    => trim($_POST['caption_ru'] ?? ''),
        'caption_tj'    => trim($_POST['caption_tj'] ?? ''),
        'sort_order'    => (int)($_POST['sort_order'] ?? 0),
    ];
    DB::update('year_media', $data, 'id=:id', ['id'=>$id]);
    log_action('update_media', 'year_media', $id);
    flash('success', 'Сохранено');
    header('Location: /admin/gallery.php?a=edit&id='.$id); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    csrf_required();
    $yearId = (int)$_POST['forum_year_id'];
    $mediaType = $_POST['media_type'] === 'video' ? 'video' : 'photo';
    $captionEn = trim($_POST['caption_en'] ?? '');
    $captionRu = trim($_POST['caption_ru'] ?? '');
    $captionTj = trim($_POST['caption_tj'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $created = 0;

    if ($mediaType === 'photo' && !empty($_FILES['files']['name'][0])) {
        // Multiple file upload
        $count = count($_FILES['files']['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) continue;
            // Rebuild single-file array for admin_upload workaround
            $_FILES['file'] = [
                'name'     => $_FILES['files']['name'][$i],
                'type'     => $_FILES['files']['type'][$i],
                'tmp_name' => $_FILES['files']['tmp_name'][$i],
                'error'    => $_FILES['files']['error'][$i],
                'size'     => $_FILES['files']['size'][$i],
            ];
            $path = admin_upload('file', 'gallery', ['image/jpeg','image/png','image/webp']);
            if (!$path) continue;
            DB::insert('year_media', [
                'forum_year_id' => $yearId,
                'media_type'    => 'photo',
                'file_path'     => $path,
                'caption_en'    => $captionEn,
                'caption_ru'    => $captionRu,
                'caption_tj'    => $captionTj,
                'sort_order'    => $sortOrder + $i,
            ]);
            $created++;
        }
    } elseif ($mediaType === 'video') {
        $path = admin_upload('video_file', 'gallery', ['video/mp4','video/quicktime','video/x-msvideo']);
        if ($path) {
            $newId = DB::insert('year_media', [
                'forum_year_id' => $yearId,
                'media_type'    => 'video',
                'file_path'     => $path,
                'caption_en'    => $captionEn,
                'caption_ru'    => $captionRu,
                'caption_tj'    => $captionTj,
                'sort_order'    => $sortOrder,
            ]);
            log_action('create_media', 'year_media', $newId);
            $created = 1;
        }
    }
    if ($created > 0) { log_action('create_media_batch', 'year_media', $yearId, "count={$created}"); flash('success', "Добавлено: {$created}"); }
    else { flash('error', 'Файлы не загружены'); }
    header('Location: /admin/gallery.php'); exit;
}

if ($action === 'edit' && $id) {
    $current = DB::row('SELECT * FROM year_media WHERE id=?', [$id]);
}
$years = DB::all('SELECT id, year FROM forum_years ORDER BY year DESC');

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action === 'list'):
$list = DB::all('SELECT ym.*, fy.year FROM year_media ym LEFT JOIN forum_years fy ON fy.id=ym.forum_year_id ORDER BY fy.year DESC, ym.sort_order'); ?>
<div class="card">
  <div class="card-header"><h2>Галерея форумов</h2><a href="/admin/gallery.php?a=create" class="btn btn--primary">+ Добавить медиа</a></div>
  <?php if (!$list): ?><div class="empty">Медиафайлов нет.</div><?php else: ?>
  <table class="data">
    <thead><tr><th>Превью</th><th>Год</th><th>Тип</th><th>Подпись (RU)</th><th>Порядок</th><th class="actions">Действия</th></tr></thead>
    <tbody><?php
    $lastYear = null;
    foreach ($list as $m):
        if ($lastYear !== $m['year']):
            $lastYear = $m['year'];
    ?>
    <tr style="background:var(--surface-2);">
      <td colspan="6" style="font-size:12px;font-weight:600;color:var(--gold-2);padding:6px 12px;">Год <?= (int)$m['year'] ?></td>
    </tr>
    <?php endif; ?>
    <tr>
      <td>
        <div class="thumb">
          <?php if ($m['media_type'] === 'photo'): ?>
            <img src="<?= e($m['thumbnail'] ?: $m['file_path']) ?>" loading="lazy">
          <?php else: ?>
            <span style="font-size:20px;">🎬</span>
          <?php endif; ?>
        </div>
      </td>
      <td><span class="badge badge--gold"><?= (int)$m['year'] ?></span></td>
      <td><span class="badge <?= $m['media_type']==='video'?'badge--warn':'badge--ok' ?>"><?= $m['media_type'] ?></span></td>
      <td style="font-size:13px;"><?= e(mb_substr($m['caption_ru'] ?? '', 0, 60)) ?></td>
      <td><?= (int)$m['sort_order'] ?></td>
      <td class="actions">
        <a href="/admin/gallery.php?a=edit&id=<?= (int)$m['id'] ?>" class="btn btn--sm btn--primary">✎</a>
        <a href="/admin/gallery.php?a=delete&id=<?= (int)$m['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить медиафайл?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>
  <?php endif; ?>
</div>

<?php elseif ($action === 'create'): ?>
<div class="card">
  <div class="card-header"><h2>Загрузить медиафайлы</h2><a href="/admin/gallery.php" class="btn btn--sm">← Список</a></div>
  <form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="field-row">
      <div class="field">
        <label>Год форума <span class="req">*</span></label>
        <select name="forum_year_id" class="select" required>
          <?php foreach ($years as $y): ?><option value="<?= (int)$y['id'] ?>"><?= (int)$y['year'] ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>Тип медиа</label>
        <select name="media_type" class="select" id="mediaTypeSelect" onchange="toggleMediaInput(this.value)">
          <option value="photo">Фото</option>
          <option value="video">Видео (MP4)</option>
        </select>
      </div>
      <div class="field">
        <label>Начальный порядок</label>
        <input type="number" name="sort_order" class="input" value="0">
      </div>
    </div>

    <div id="photoInput" class="field">
      <label>Фотографии (можно несколько)</label>
      <input type="file" name="files[]" accept="image/*" multiple class="input">
      <p class="help">Выберите один или несколько файлов. Все получат одинаковую подпись.</p>
    </div>
    <div id="videoInput" class="field" style="display:none;">
      <label>Видеофайл (MP4)</label>
      <input type="file" name="video_file" accept="video/mp4,video/*" class="input">
    </div>

    <div class="field-row--3">
      <div class="field"><label>Подпись (RU)</label><input type="text" name="caption_ru" class="input" placeholder="Русская подпись"></div>
      <div class="field"><label>Подпись (EN)</label><input type="text" name="caption_en" class="input" placeholder="English caption"></div>
      <div class="field"><label>Подпись (TJ)</label><input type="text" name="caption_tj" class="input" placeholder="Тоҷикӣ"></div>
    </div>
    <button type="submit" class="btn btn--primary" style="margin-top:16px;">⬆ Загрузить</button>
  </form>
  <script>function toggleMediaInput(v){document.getElementById('photoInput').style.display=v==='photo'?'':'none';document.getElementById('videoInput').style.display=v==='video'?'':'none';}</script>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header"><h2>Редактирование медиафайла</h2><a href="/admin/gallery.php" class="btn btn--sm">← Список</a></div>
  <form method="POST">
    <?= csrf_field() ?>
    <?php if (!empty($current)): ?>
    <div class="field">
      <?php if ($current['media_type'] === 'photo'): ?>
        <div class="thumb" style="width:200px;height:150px;margin-bottom:12px;"><img src="<?= e($current['thumbnail'] ?: $current['file_path']) ?>"></div>
      <?php else: ?>
        <p>🎬 Видео: <a href="<?= e($current['file_path']) ?>" target="_blank">просмотреть</a></p>
      <?php endif; ?>
    </div>
    <div class="field-row">
      <div class="field">
        <label>Год форума</label>
        <select name="forum_year_id" class="select">
          <?php foreach ($years as $y): ?><option value="<?= (int)$y['id'] ?>" <?= $current['forum_year_id']==$y['id']?'selected':'' ?>><?= (int)$y['year'] ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>Порядок</label>
        <input type="number" name="sort_order" class="input" value="<?= (int)$current['sort_order'] ?>">
      </div>
    </div>
    <div class="field-row--3">
      <div class="field"><label>Подпись (RU)</label><input type="text" name="caption_ru" class="input" value="<?= e($current['caption_ru'] ?? '') ?>"></div>
      <div class="field"><label>Подпись (EN)</label><input type="text" name="caption_en" class="input" value="<?= e($current['caption_en'] ?? '') ?>"></div>
      <div class="field"><label>Подпись (TJ)</label><input type="text" name="caption_tj" class="input" value="<?= e($current['caption_tj'] ?? '') ?>"></div>
    </div>
    <?php endif; ?>
    <button type="submit" class="btn btn--primary" style="margin-top:16px;">💾 Сохранить</button>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
