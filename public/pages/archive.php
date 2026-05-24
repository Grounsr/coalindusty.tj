<?php /** @var string $LANG */ /** @var array $pageMeta */
$years = DB::all('
    SELECT fy.*, fyt.title, fyt.tagline
    FROM forum_years fy
    LEFT JOIN forum_year_translations fyt ON fyt.forum_year_id = fy.id AND fyt.lang = ?
    WHERE fy.is_published = 1
    ORDER BY fy.year DESC
', [$LANG]);

$selectedId = (int)($_GET['year'] ?? 0);
if ($selectedId) {
    $selected = DB::row('SELECT fy.*, fyt.title, fyt.tagline, fyt.description FROM forum_years fy LEFT JOIN forum_year_translations fyt ON fyt.forum_year_id=fy.id AND fyt.lang=? WHERE fy.id=?', [$LANG, $selectedId]);
    $media = $selected ? DB::all('SELECT * FROM year_media WHERE forum_year_id=? ORDER BY sort_order', [$selectedId]) : [];
}
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow"><?= e(t('archive.title', $LANG)) ?></span>
    <h1><?= e($pageMeta['title']) ?></h1>
    <p class="subtitle"><?= e($pageMeta['subtitle']) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (isset($selected) && $selected): ?>
      <a href="/?p=archive&lang=<?= e($LANG) ?>" class="btn btn--ghost btn--sm" style="margin-bottom:32px;">← <?= e(t('btn.back', $LANG)) ?></a>
      <div style="margin-bottom:48px;">
        <div style="font-family:var(--ff-display);font-size:88px;font-weight:700;color:var(--gold-1);line-height:1;"><?= (int)$selected['year'] ?></div>
        <h2 style="margin-top:8px;"><?= e($selected['title']) ?></h2>
        <p class="subtitle"><?= e($selected['tagline']) ?></p>
      </div>

      <?php if (!$media): ?>
      <div style="text-align:center;padding:48px;color:var(--ink-3);background:var(--bg-1);border-radius:var(--r-lg);border:1px dashed var(--line-strong);">
        <?= e(t('archive.no_data', $LANG)) ?>
      </div>
      <?php else: ?>
      <div class="gallery-grid">
        <?php foreach ($media as $m): ?>
          <?php if ($m['media_type'] === 'photo'): ?>
          <a href="<?= e(upload_url($m['file_path'])) ?>" class="gallery-item" target="_blank">
            <img src="<?= e(upload_url($m['file_path'])) ?>" alt="<?= e($m['caption_' . $LANG] ?? '') ?>" loading="lazy">
          </a>
          <?php else: ?>
          <div class="gallery-item">
            <video src="<?= e(upload_url($m['file_path'])) ?>" controls style="width:100%;height:100%;object-fit:cover;"></video>
          </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    <?php else: ?>
      <div class="years-grid">
        <?php foreach ($years as $y): ?>
        <a href="/?p=archive&year=<?= (int)$y['id'] ?>&lang=<?= e($LANG) ?>" class="year-card <?= $y['is_current'] ? 'is-current' : '' ?> reveal">
          <div class="year-number"><?= (int)$y['year'] ?></div>
          <div class="year-title"><?= e($y['title']) ?></div>
          <div class="year-tagline"><?= e($y['tagline']) ?></div>
          <div class="year-meta">
            <span><?= (int)$y['participants_count'] ?: '—' ?> <?= e(t('stats.participants', $LANG)) ?></span>
            <span><?= (int)$y['countries_count'] ?: '—' ?> <?= e(t('stats.countries', $LANG)) ?></span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
