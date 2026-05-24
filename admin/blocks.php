<?php
require_once __DIR__ . '/_bootstrap.php';
$adminTitle = 'Блоки контента';
$showLangSwitcher = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_required();
    $bid = (int)($_POST['block_id'] ?? 0);
    $values = $_POST['value'] ?? [];
    foreach ($values as $lang => $val) {
        if (!in_array($lang, ['en','ru','tj','_'], true)) continue;
        $exists = DB::value('SELECT block_id FROM content_block_values WHERE block_id=? AND lang=?', [$bid, $lang]);
        if ($exists) {
            DB::update('content_block_values', ['value' => $val], 'block_id=:b AND lang=:l', ['b'=>$bid,'l'=>$lang]);
        } else {
            DB::insert('content_block_values', ['block_id'=>$bid,'lang'=>$lang,'value'=>$val]);
        }
    }
    log_action('update_block', 'block', $bid);
    flash('success', 'Блок сохранён');
    header('Location: /admin/blocks.php?el=' . $EL . '#block-' . $bid);
    exit;
}

$blocks = DB::all('SELECT * FROM content_blocks ORDER BY id');
$values = [];
foreach (DB::all('SELECT * FROM content_block_values') as $v) {
    $values[$v['block_id']][$v['lang']] = $v['value'];
}

require __DIR__ . '/_layout_top.php';
?>

<div class="card">
  <p style="color:var(--ink-3);font-size:13px;margin:0 0 16px;">
    Блоки контента — это короткие фрагменты текста, заголовки и числа, отображаемые на разных страницах (главная, о форуме, инвесторам, статистика).
    Числовые блоки (вроде «500 участников») имеют одно значение на все языки.
  </p>
</div>

<?php
$groups = [
    'Главная — Hero'            => ['hero.badge','hero.title','hero.subtitle','hero.tagline','hero.cta'],
    'Главная — Статистика'      => ['stats.participants','stats.countries','stats.speakers','stats.days'],
    'Главная — Блок «О форуме»' => ['about.brief.title','about.brief.text','about.brief.text2'],
    'Главная — Темы'            => ['topics.title','topics.subtitle'],
    'Главная — Партнёры'        => ['partners.title','partners.subtitle'],
    'О форуме — Концепция'      => ['about.concept.p1','about.concept.p2','about.concept.p3'],
    'О форуме — Уголь Таджикистана' => ['about.coal.p1','about.coal.p2','about.coal.p3'],
    'О форуме — Руководство'    => ['about.leadership.title','about.leadership.subtitle'],
    'Инвесторам'                => ['investors.intro','investors.cta_note'],
];

foreach ($groups as $groupName => $keys): ?>
<div class="card">
  <div class="card-header"><h2><?= e($groupName) ?></h2></div>

  <?php foreach ($blocks as $b): if (!in_array($b['block_key'], $keys, true)) continue; ?>
  <div id="block-<?= (int)$b['id'] ?>" style="padding:16px 0;border-bottom:1px solid var(--line);">
    <form method="POST" action="" style="display:flex;flex-direction:column;gap:10px;">
      <?= csrf_field() ?>
      <input type="hidden" name="block_id" value="<?= (int)$b['id'] ?>">

      <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
        <div>
          <code style="background:var(--bg-3);padding:2px 8px;border-radius:4px;font-size:12px;color:var(--gold-2);"><?= e($b['block_key']) ?></code>
          <span style="color:var(--ink-3);font-size:12px;margin-left:8px;"><?= e($b['description']) ?></span>
        </div>
        <span class="badge badge--neutral"><?= e($b['block_type']) ?></span>
      </div>

      <?php if ($b['block_type'] === 'number'): ?>
        <input type="number" name="value[_]" class="input" value="<?= e($values[$b['id']]['_'] ?? '') ?>" style="max-width:200px;">
      <?php else:
        $val = $values[$b['id']][$EL] ?? '';
      ?>
        <?php if ($b['block_type'] === 'html'): ?>
          <textarea name="value[<?= $EL ?>]" class="tinymce" rows="5"><?= e($val) ?></textarea>
        <?php else: ?>
          <input type="text" name="value[<?= $EL ?>]" class="input" value="<?= e($val) ?>">
        <?php endif; ?>
      <?php endif; ?>

      <button type="submit" class="btn btn--primary btn--sm" style="align-self:flex-start;">💾 Сохранить</button>
    </form>
  </div>
  <?php endforeach; ?>
</div>
<?php endforeach; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
