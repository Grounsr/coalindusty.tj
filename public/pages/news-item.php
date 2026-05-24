<?php /** @var string $LANG */
$id = (int)($_GET['id'] ?? 0);
$item = DB::row('
    SELECT n.*, nt.title, nt.excerpt, nt.body, nt.meta_title, nt.meta_description
    FROM news n
    LEFT JOIN news_translations nt ON nt.news_id = n.id AND nt.lang = ?
    WHERE n.id = ? AND n.is_published = 1
', [$LANG, $id]);

if (!$item) {
    http_response_code(404);
    echo '<section class="page-hero"><div class="container"><h1>404</h1><p class="subtitle">News article not found.</p></div></section>';
    return;
}

DB::query('UPDATE news SET views = views + 1 WHERE id = ?', [$id]);

$pageMeta['title'] = $item['title'];
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow"><?= date('d F Y', strtotime($item['published_at'])) ?></span>
    <h1><?= e($item['title']) ?></h1>
    <p class="subtitle"><?= e($item['excerpt']) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="news-article">
      <?php if ($item['cover_image']): ?>
      <div class="news-article-cover">
        <img src="<?= e(upload_url($item['cover_image'])) ?>" alt="<?= e($item['title']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
      </div>
      <?php endif; ?>

      <div class="news-content">
        <?= clean_html($item['body']) ?>
      </div>

      <div style="margin-top:48px;padding-top:32px;border-top:1px solid var(--line);">
        <a href="/?p=news&lang=<?= e($LANG) ?>" class="btn btn--ghost">← <?= e(t('btn.all_news', $LANG)) ?></a>
      </div>
    </div>
  </div>
</section>
