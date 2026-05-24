<?php /** @var string $LANG */ /** @var array $pageMeta */
$year = current_year();
$speakers = DB::all('
    SELECT s.*, st.full_name, st.position, st.organization, st.country
    FROM speakers s
    LEFT JOIN speaker_translations st ON st.speaker_id = s.id AND st.lang = ?
    WHERE s.forum_year_id = ? AND s.is_visible = 1
    ORDER BY s.sort_order
', [$LANG, $year['id'] ?? 0]);
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow"><?= e($year['year'] ?? '2026') ?></span>
    <h1><?= e($pageMeta['title']) ?></h1>
    <p class="subtitle"><?= e($pageMeta['subtitle']) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!$speakers): ?>
    <div style="text-align:center;padding:64px 24px;color:var(--ink-3);">
      <h3 style="color:var(--ink-1);">Speakers announcement coming soon</h3>
      <p>The list of confirmed speakers will be published shortly.</p>
    </div>
    <?php else: ?>
    <div class="speakers-grid">
      <?php foreach ($speakers as $sp): ?>
      <article class="speaker-card reveal">
        <div class="speaker-photo <?= $sp['photo'] ? '' : 'placeholder' ?>">
          <?php if ($sp['photo']): ?>
            <img src="<?= e(upload_url($sp['photo'])) ?>" alt="<?= e($sp['full_name']) ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          <?php endif; ?>
        </div>
        <div class="speaker-info">
          <h3 class="speaker-name"><?= e($sp['full_name']) ?></h3>
          <p class="speaker-position"><?= e($sp['position']) ?></p>
          <p class="speaker-org"><?= e($sp['organization']) ?><?= $sp['country'] ? ' · ' . e($sp['country']) : '' ?></p>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
