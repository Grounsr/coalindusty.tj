<?php /** @var string $LANG */ /** @var array $pageMeta */
$news = DB::all('
    SELECT n.id, n.slug, n.cover_image, n.published_at, nt.title, nt.excerpt
    FROM news n
    LEFT JOIN news_translations nt ON nt.news_id = n.id AND nt.lang = ?
    WHERE n.is_published = 1
    ORDER BY n.published_at DESC
', [$LANG]);
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow"><?= e(t('news.title', $LANG)) ?></span>
    <h1><?= e($pageMeta['title']) ?></h1>
    <p class="subtitle"><?= e($pageMeta['subtitle']) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!$news): ?>
    <p style="text-align:center;color:var(--ink-3);"><?= e(t('news.empty', $LANG)) ?></p>
    <?php else: ?>
    <div class="news-grid">
      <?php foreach ($news as $n): ?>
      <article class="news-card reveal">
        <div class="news-cover <?= $n['cover_image'] ? '' : 'placeholder' ?>">
          <?php if ($n['cover_image']): ?>
            <img src="<?= e(upload_url($n['cover_image'])) ?>" alt="<?= e($n['title']) ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          <?php endif; ?>
        </div>
        <div class="news-body">
          <span class="news-date"><?= date('d M Y', strtotime($n['published_at'])) ?></span>
          <h3><a href="/?p=news-item&id=<?= (int)$n['id'] ?>&lang=<?= e($LANG) ?>"><?= e($n['title']) ?></a></h3>
          <p class="news-excerpt"><?= e($n['excerpt']) ?></p>
          <a href="/?p=news-item&id=<?= (int)$n['id'] ?>&lang=<?= e($LANG) ?>" class="news-more"><?= e(t('news.read', $LANG)) ?> →</a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
