<?php /** @var string $LANG */ /** @var array $pageMeta */
$packages = DB::all('
    SELECT ip.*, ipt.name, ipt.tagline, ipt.benefits
    FROM investor_packages ip
    LEFT JOIN investor_package_translations ipt ON ipt.package_id = ip.id AND ipt.lang = ?
    ORDER BY ip.sort_order
', [$LANG]);

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>

<section class="investors-hero">
  <div class="container">
    <span class="eyebrow"><?= e(t('nav.investors', $LANG)) ?></span>
    <h1><?= e(t('investors.title', $LANG)) ?></h1>
    <p class="subtitle"><?= e(t('investors.subtitle', $LANG)) ?></p>
  </div>
</section>

<section class="section section--surface">
  <div class="container">
    <div style="max-width:760px;margin:0 auto 48px;text-align:center;">
      <?= clean_html(block('investors.intro', $LANG)) ?>
    </div>

    <div class="investors-packages">
      <?php foreach ($packages as $p):
        $benefits = array_filter(array_map('trim', explode("\n", (string)$p['benefits'])));
      ?>
      <article class="investor-package <?= $p['is_featured'] ? 'is-featured' : '' ?> reveal">
        <h3><?= e($p['name']) ?></h3>
        <p class="tagline"><?= e($p['tagline']) ?></p>
        <ul>
          <?php foreach ($benefits as $b): ?>
          <li><?= e($b) ?></li>
          <?php endforeach; ?>
        </ul>
        <a href="#investor-contact" class="btn btn--outline btn--block"><?= e(t('investors.contact_btn', $LANG)) ?></a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="investor-contact">
  <div class="container">
    <div class="investor-cta">
      <div class="investor-cta-inner">
        <span class="eyebrow" style="justify-content:center;">Partnership</span>
        <h2 style="margin:16px 0;"><?= e(t('investors.cta', $LANG)) ?></h2>
        <p style="color:var(--ink-3);margin-bottom:32px;"><?= e(block('investors.cta_note', $LANG)) ?></p>

        <?php if ($success): ?>
        <div class="form-alert form-alert--success" style="text-align:left;">
          <?= $LANG === 'ru' ? 'Спасибо. Ваш запрос принят. Оргкомитет свяжется с вами в течение 2 рабочих дней.' : ($LANG === 'tj' ? 'Ташаккур. Дархости шумо қабул шуд. Оргкомитет дар муддати 2 рӯзи корӣ бо шумо тамос мегирад.' : 'Thank you. Your enquiry has been received. The Organizing Committee will contact you within 2 business days.') ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="form-alert form-alert--error" style="text-align:left;"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/" class="form-card" style="text-align:left;margin-top:24px;">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="investor_inquiry">

          <div class="form-row">
            <div class="form-group">
              <label><?= e(t('form.full_name', $LANG)) ?> <span class="req">*</span></label>
              <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="form-group">
              <label><?= e(t('form.email', $LANG)) ?> <span class="req">*</span></label>
              <input type="email" name="email" class="form-control" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label><?= e(t('form.phone', $LANG)) ?></label>
              <input type="tel" name="phone" class="form-control">
            </div>
            <div class="form-group">
              <label><?= e(t('form.company', $LANG)) ?></label>
              <input type="text" name="company" class="form-control">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label><?= e(t('form.position', $LANG)) ?></label>
              <input type="text" name="position" class="form-control">
            </div>
            <div class="form-group">
              <label><?= e(t('form.country', $LANG)) ?></label>
              <select name="country" class="form-control">
                <option value="">—</option>
                <?php foreach (countries_list($LANG) as $code => $n): ?>
                <option value="<?= e($code) ?>"><?= e($n) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label><?= e(t('form.interest_level', $LANG)) ?></label>
            <select name="interest_level" class="form-control">
              <option value="">—</option>
              <option>Strategic Partner</option>
              <option>General Partner</option>
              <option>Media Partner</option>
              <option>Session Sponsor</option>
              <option>Custom proposal</option>
            </select>
          </div>

          <div class="form-group">
            <label><?= e(t('form.message', $LANG)) ?></label>
            <textarea name="message" class="form-control" rows="5"></textarea>
          </div>

          <label class="form-check" style="margin-bottom:24px;">
            <input type="checkbox" name="agree" value="1" required>
            <span><?= e(t('form.agree', $LANG)) ?></span>
          </label>

          <button type="submit" class="btn btn--primary btn--block"><?= e(t('btn.contact_committee', $LANG)) ?></button>
        </form>
      </div>
    </div>
  </div>
</section>
