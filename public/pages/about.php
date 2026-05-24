<?php /** @var string $LANG */ /** @var array $pageMeta */
$year = current_year();
$leaders = DB::all('SELECT l.*, lt.full_name, lt.position, lt.quote FROM leadership l LEFT JOIN leadership_translations lt ON lt.leadership_id=l.id AND lt.lang=? WHERE l.is_visible=1 ORDER BY l.sort_order', [$LANG]);
$conceptDl = DB::row('SELECT * FROM downloads WHERE forum_year_id=? AND doc_type="concept" AND lang=?', [$year['id'] ?? 0, $LANG]);
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
    <div class="about-brief">
      <div class="about-text reveal">
        <h2>Forum Concept</h2>
        <?= clean_html(block('about.concept.p1', $LANG)) ?>
        <?= clean_html(block('about.concept.p2', $LANG)) ?>
        <?= clean_html(block('about.concept.p3', $LANG)) ?>
        <?php if ($conceptDl): ?>
        <a href="/?p=download&id=<?= (int)$conceptDl['id'] ?>" class="btn btn--primary" download>
          <?= e(t('btn.download_concept', $LANG)) ?>
        </a>
        <?php endif; ?>
      </div>
      <div class="about-image placeholder placeholder--portrait reveal"><?= e(t('placeholder.image', $LANG)) ?></div>
    </div>
  </div>
</section>

<?php if ($leaders): ?>
<section class="section leadership">
  <div class="container">
    <div style="text-align:center;margin-bottom:64px;">
      <span class="eyebrow"><?= e(block('about.leadership.title', $LANG)) ?></span>
      <h2 class="section-title section-title--center"><?= e(block('about.leadership.title', $LANG)) ?></h2>
      <p class="section-subtitle section-subtitle--center"><?= e(block('about.leadership.subtitle', $LANG)) ?></p>
    </div>
    <div class="leadership-grid">
      <?php foreach ($leaders as $l): ?>
      <article class="leader-card reveal">
        <div class="leader-photo">
          <?php if ($l['photo']): ?>
            <img src="<?= e(upload_url($l['photo'])) ?>" alt="<?= e($l['full_name']) ?>" loading="lazy">
          <?php else: ?>
            <div class="placeholder" style="width:100%;height:100%"></div>
          <?php endif; ?>
        </div>
        <h3 class="leader-name"><?= e($l['full_name']) ?></h3>
        <p class="leader-position"><?= e($l['position']) ?></p>
        <?php if ($l['quote']): ?>
        <p class="leader-quote"><?= e($l['quote']) ?></p>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section">
  <div class="container">
    <div class="about-brief">
      <div class="about-image placeholder placeholder--portrait reveal"><?= e(t('placeholder.image', $LANG)) ?></div>
      <div class="about-text reveal">
        <h2>Coal Industry of Tajikistan</h2>
        <?= clean_html(block('about.coal.p1', $LANG)) ?>
        <?= clean_html(block('about.coal.p2', $LANG)) ?>
        <?= clean_html(block('about.coal.p3', $LANG)) ?>
      </div>
    </div>
  </div>
</section>
