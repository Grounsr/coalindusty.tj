<?php /** @var string $LANG */
$year = current_year();
$topics = DB::all('SELECT t.*, tt.title, tt.description FROM topics t LEFT JOIN topic_translations tt ON tt.topic_id=t.id AND tt.lang=? WHERE t.forum_year_id=? ORDER BY t.sort_order', [$LANG, $year['id'] ?? 0]);
$partners = DB::all('SELECT p.*, pt.name FROM partners p LEFT JOIN partner_translations pt ON pt.partner_id=p.id AND pt.lang=? WHERE p.forum_year_id=? ORDER BY p.sort_order', [$LANG, $year['id'] ?? 0]);
$recentNews = DB::all('
    SELECT n.id, n.slug, n.cover_image, n.published_at, nt.title, nt.excerpt
    FROM news n LEFT JOIN news_translations nt ON nt.news_id=n.id AND nt.lang=?
    WHERE n.is_published=1
    ORDER BY n.published_at DESC LIMIT 3', [$LANG]);
$leaders = DB::all('SELECT l.*, lt.full_name, lt.position, lt.quote FROM leadership l LEFT JOIN leadership_translations lt ON lt.leadership_id=l.id AND lt.lang=? WHERE l.is_visible=1 ORDER BY l.sort_order', [$LANG]);
?>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-badge"><?= e(block('hero.badge', $LANG)) ?></div>
    <h1 class="hero-title"><?= e(block('hero.title', $LANG)) ?></h1>
    <p class="hero-subtitle"><?= e(block('hero.subtitle', $LANG)) ?></p>
    <p class="hero-date"><?= e(setting_i18n('site_venue', $LANG)) ?></p>
    <p class="hero-tagline"><?= e(block('hero.tagline', $LANG)) ?></p>

    <div class="hero-actions">
      <a href="/?p=register&lang=<?= e($LANG) ?>" class="btn btn--primary"><?= e(block('hero.cta', $LANG)) ?></a>
      <a href="/?p=about&lang=<?= e($LANG) ?>" class="btn btn--ghost"><?= e(t('btn.learn_more', $LANG)) ?></a>
    </div>

    <div class="countdown" aria-label="Countdown">
      <div class="countdown-item"><span class="countdown-number" id="cd-days">000</span><span class="countdown-label"><?= e(t('countdown.days', $LANG)) ?></span></div>
      <div class="countdown-item"><span class="countdown-number" id="cd-hours">00</span><span class="countdown-label"><?= e(t('countdown.hours', $LANG)) ?></span></div>
      <div class="countdown-item"><span class="countdown-number" id="cd-minutes">00</span><span class="countdown-label"><?= e(t('countdown.minutes', $LANG)) ?></span></div>
      <div class="countdown-item"><span class="countdown-number" id="cd-seconds">00</span><span class="countdown-label"><?= e(t('countdown.seconds', $LANG)) ?></span></div>
    </div>
  </div>
  <div class="hero-scroll" aria-hidden="true">Scroll</div>
</section>

<!-- STATS -->
<section class="stats">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item reveal"><span class="stat-number" data-count="<?= (int)block('stats.participants', $LANG, '500') ?>" data-suffix="+">0</span><div class="stat-label"><?= e(t('stats.participants', $LANG)) ?></div></div>
      <div class="stat-item reveal"><span class="stat-number" data-count="<?= (int)block('stats.countries', $LANG, '30') ?>" data-suffix="+">0</span><div class="stat-label"><?= e(t('stats.countries', $LANG)) ?></div></div>
      <div class="stat-item reveal"><span class="stat-number" data-count="<?= (int)block('stats.speakers', $LANG, '50') ?>" data-suffix="+">0</span><div class="stat-label"><?= e(t('stats.speakers', $LANG)) ?></div></div>
      <div class="stat-item reveal"><span class="stat-number" data-count="<?= (int)block('stats.days', $LANG, '1') ?>" data-suffix="">0</span><div class="stat-label"><?= e(t('stats.days', $LANG)) ?></div></div>
    </div>
  </div>
</section>

<!-- ABOUT BRIEF -->
<section class="section section--surface">
  <div class="container">
    <div class="about-brief">
      <div class="about-text reveal">
        <span class="eyebrow"><?= e(t('nav.about', $LANG)) ?></span>
        <h2 class="section-title"><?= e(block('about.brief.title', $LANG)) ?></h2>
        <?= clean_html(block('about.brief.text', $LANG)) ?>
        <?= clean_html(block('about.brief.text2', $LANG)) ?>
        <a href="/?p=about&lang=<?= e($LANG) ?>" class="btn btn--outline"><?= e(t('btn.learn_more', $LANG)) ?></a>
      </div>
      <div class="about-image placeholder placeholder--portrait reveal" aria-label="<?= e(t('placeholder.image', $LANG)) ?>">
        <?= e(t('placeholder.image', $LANG)) ?>
      </div>
    </div>
  </div>
</section>

<!-- LEADERSHIP -->
<?php if ($leaders): ?>
<section class="section leadership">
  <div class="container">
    <div style="text-align:center;margin-bottom:64px;">
      <span class="eyebrow"><?= e($year['year'] ?? '2026') ?></span>
      <h2 class="section-title section-title--center reveal"><?= e(block('about.leadership.title', $LANG)) ?></h2>
      <p class="section-subtitle section-subtitle--center reveal"><?= e(block('about.leadership.subtitle', $LANG)) ?></p>
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

<!-- TOPICS -->
<section class="section">
  <div class="container">
    <div style="text-align:center;margin-bottom:48px;">
      <span class="eyebrow"><?= e($year['year'] ?? '2026') ?></span>
      <h2 class="section-title section-title--center reveal"><?= e(block('topics.title', $LANG)) ?></h2>
      <p class="section-subtitle section-subtitle--center reveal"><?= e(block('topics.subtitle', $LANG)) ?></p>
    </div>
    <div class="topics-grid">
      <?php foreach ($topics as $i => $top): ?>
      <article class="topic-card reveal">
        <div class="topic-icon"><?= sprintf('%02d', $i + 1) ?></div>
        <div class="topic-body">
          <h3><?= e($top['title']) ?></h3>
          <p><?= e($top['description']) ?></p>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- LATEST NEWS -->
<?php if ($recentNews): ?>
<section class="section section--deep">
  <div class="container">
    <div style="display:flex;align-items:end;justify-content:space-between;flex-wrap:wrap;gap:24px;margin-bottom:48px;">
      <div>
        <span class="eyebrow"><?= e(t('news.title', $LANG)) ?></span>
        <h2 class="section-title reveal"><?= e(t('news.title', $LANG)) ?></h2>
      </div>
      <a href="/?p=news&lang=<?= e($LANG) ?>" class="btn btn--outline btn--sm"><?= e(t('btn.all_news', $LANG)) ?></a>
    </div>
    <div class="news-grid">
      <?php foreach ($recentNews as $n): ?>
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
  </div>
</section>
<?php endif; ?>

<!-- PARTNERS -->
<?php if ($partners): ?>
<section class="section">
  <div class="container">
    <div style="text-align:center;margin-bottom:48px;">
      <span class="eyebrow">Network</span>
      <h2 class="section-title section-title--center reveal"><?= e(block('partners.title', $LANG)) ?></h2>
      <p class="section-subtitle section-subtitle--center reveal"><?= e(block('partners.subtitle', $LANG)) ?></p>
    </div>
    <div class="partners-grid reveal">
      <?php foreach ($partners as $p): ?>
      <div class="partner-item"><?= e($p['name']) ?></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
