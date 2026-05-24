<?php /** @var string $LANG */ /** @var array $pageMeta */
$year = current_year();
$items = DB::all('
    SELECT pi.*, pit.title, pit.description
    FROM program_items pi
    LEFT JOIN program_item_translations pit ON pit.program_item_id = pi.id AND pit.lang = ?
    WHERE pi.forum_year_id = ?
    ORDER BY pi.day_number, pi.sort_order
', [$LANG, $year['id'] ?? 0]);
$programDl = DB::row('SELECT * FROM downloads WHERE forum_year_id=? AND doc_type="program" AND lang=?', [$year['id'] ?? 0, $LANG]);

$tagLabels = [
    'registration' => ['en'=>'Registration','ru'=>'Регистрация','tj'=>'Сабти ном'],
    'opening'      => ['en'=>'Opening','ru'=>'Открытие','tj'=>'Кушоиш'],
    'plenary'      => ['en'=>'Plenary','ru'=>'Пленарная','tj'=>'Пленарӣ'],
    'panel'        => ['en'=>'Panel','ru'=>'Панель','tj'=>'Панел'],
    'break'        => ['en'=>'Break','ru'=>'Перерыв','tj'=>'Танаффус'],
    'ceremony'     => ['en'=>'Ceremony','ru'=>'Церемония','tj'=>'Маросим'],
    'closing'      => ['en'=>'Closing','ru'=>'Закрытие','tj'=>'Хотима'],
    'press'        => ['en'=>'Press','ru'=>'Пресса','tj'=>'Матбуот'],
    'reception'    => ['en'=>'Reception','ru'=>'Приём','tj'=>'Қабул'],
];
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

    <?php if ($programDl): ?>
    <div class="program-downloads reveal">
      <div style="flex:1;min-width:240px;">
        <div style="font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--gold-2);margin-bottom:6px;">Official document</div>
        <div style="font-family:var(--ff-display);font-size:22px;color:var(--ink-1);"><?= e($programDl['original_name'] ?: 'Programme.pdf') ?></div>
      </div>
      <a href="/?p=download&id=<?= (int)$programDl['id'] ?>" class="btn btn--primary" download>
        <?= e(t('btn.download_program', $LANG)) ?>
      </a>
    </div>
    <?php endif; ?>

    <div class="program-day">
      <div class="program-day-header">
        <h2 class="program-day-title"><?= e(date('d F Y', strtotime($year['event_date'] ?? '2026-11-25'))) ?></h2>
        <span class="program-day-date"><?= e(setting_i18n('site_venue', $LANG)) ?></span>
      </div>

      <div class="program-timeline">
        <?php foreach ($items as $it):
          $isBreak = $it['tag'] === 'break';
          $tagLabel = $tagLabels[$it['tag']][$LANG] ?? ucfirst((string)$it['tag']);
        ?>
        <div class="program-item <?= $isBreak ? 'program-item--break' : '' ?> reveal">
          <div class="program-time">
            <?= e($it['time_start']) ?>
            <small><?= e($it['time_end']) ?></small>
          </div>
          <div class="program-content">
            <?php if ($it['tag'] && !$isBreak): ?>
            <span class="program-tag"><?= e($tagLabel) ?></span>
            <?php endif; ?>
            <h4><?= e($it['title']) ?></h4>
            <?php if ($it['description']): ?>
            <p class="program-desc"><?= e($it['description']) ?></p>
            <?php endif; ?>
            <?php if ($it['hall']): ?>
            <div class="program-hall">📍 <?= e($it['hall']) ?></div>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</section>
