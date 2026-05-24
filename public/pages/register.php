<?php /** @var string $LANG */ /** @var array $pageMeta */
$step = $_GET['step'] ?? 'form';
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$regId = (int)($_GET['rid'] ?? 0);

$verifyTexts = [
    'en' => ['photo_hint' => '3×4 portrait photo · JPG/PNG · max 5 MB',
             'passport_hint' => 'Scanned copy of passport ID page · JPG/PNG/PDF · max 10 MB',
             'photo_label' => '3×4 Portrait Photo',
             'passport_label' => 'Passport Copy'],
    'ru' => ['photo_hint' => 'Портретное фото 3×4 · JPG/PNG · до 5 МБ',
             'passport_hint' => 'Скан страницы паспорта с фото · JPG/PNG/PDF · до 10 МБ',
             'photo_label' => 'Фото 3×4',
             'passport_label' => 'Копия паспорта'],
    'tj' => ['photo_hint' => 'Сурати портретии 3×4 · JPG/PNG · то 5 МБ',
             'passport_hint' => 'Нусхаи саҳифаи паспорт · JPG/PNG/PDF · то 10 МБ',
             'photo_label' => 'Сурати 3×4',
             'passport_label' => 'Нусхаи шиноснома'],
];
$labels = $verifyTexts[$LANG];
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow"><?= e(t('nav.register', $LANG)) ?></span>
    <h1><?= e(t('reg.title', $LANG)) ?></h1>
    <p class="subtitle"><?= e(t('reg.subtitle', $LANG)) ?></p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:880px;">

    <?php if ($step === 'verify'): ?>
      <!-- VERIFICATION STEP -->
      <div class="form-card">
        <h2 style="font-size:28px;margin-bottom:12px;text-align:center;"><?= e(t('reg.verify.title', $LANG)) ?></h2>
        <p style="text-align:center;color:var(--ink-3);margin-bottom:8px;"><?= e(t('reg.verify.text', $LANG)) ?></p>

        <?php if ($error): ?><div class="form-alert form-alert--error"><?= e($error) ?></div><?php endif; ?>

        <form method="POST" action="/" id="verifyForm">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="verify_email">
          <input type="hidden" name="rid" value="<?= (int)$regId ?>">

          <div class="code-input">
            <input type="text" maxlength="1" class="code-digit" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="code-digit" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="code-digit" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="code-digit" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="code-digit" inputmode="numeric" pattern="[0-9]">
            <input type="text" maxlength="1" class="code-digit" inputmode="numeric" pattern="[0-9]">
          </div>
          <input type="hidden" name="code" id="codeFinal">

          <button type="submit" class="btn btn--primary btn--block"><?= e(t('btn.verify', $LANG)) ?></button>
        </form>

        <form method="POST" action="/" style="margin-top:16px;text-align:center;">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="resend_code">
          <input type="hidden" name="rid" value="<?= (int)$regId ?>">
          <button type="submit" class="btn btn--ghost btn--sm"><?= e(t('btn.resend_code', $LANG)) ?></button>
        </form>
      </div>

    <?php elseif ($step === 'done'): ?>
      <!-- SUCCESS STEP -->
      <div class="form-card" style="text-align:center;">
        <div style="width:96px;height:96px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--gold-d));margin:0 auto 24px;display:flex;align-items:center;justify-content:center;font-size:48px;color:var(--bg-0);box-shadow:0 0 60px rgba(201,164,73,.4);">✓</div>
        <h2 style="font-size:32px;margin-bottom:12px;"><?= e(t('reg.verified.title', $LANG)) ?></h2>
        <p style="color:var(--ink-3);font-size:17px;line-height:1.7;max-width:520px;margin:0 auto 32px;">
          <?= e(t('reg.verified.text', $LANG)) ?>
        </p>
        <a href="/?lang=<?= e($LANG) ?>" class="btn btn--primary"><?= e(t('nav.home', $LANG)) ?></a>
      </div>

    <?php else: ?>
      <!-- REGISTRATION FORM -->
      <?php if ($error): ?><div class="form-alert form-alert--error"><?= e($error) ?></div><?php endif; ?>

      <form method="POST" action="/" enctype="multipart/form-data" class="form-card">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="register">

        <h2 style="font-size:22px;margin-bottom:24px;border-bottom:1px solid var(--line);padding-bottom:16px;">
          <?= $LANG === 'ru' ? 'Личные данные' : ($LANG === 'tj' ? 'Маълумоти шахсӣ' : 'Personal information') ?>
        </h2>

        <div class="form-row">
          <div class="form-group">
            <label><?= e(t('form.full_name', $LANG)) ?> <span class="req">*</span></label>
            <input type="text" name="full_name" class="form-control" required value="<?= e($_POST['full_name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label><?= e(t('form.email', $LANG)) ?> <span class="req">*</span></label>
            <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label><?= e(t('form.phone', $LANG)) ?> <span class="req">*</span></label>
            <input type="tel" name="phone" class="form-control" required value="<?= e($_POST['phone'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label><?= e(t('form.country', $LANG)) ?> <span class="req">*</span></label>
            <select name="country" class="form-control" required>
              <option value="">—</option>
              <?php foreach (countries_list($LANG) as $code => $n): ?>
              <option value="<?= e($code) ?>" <?= ($_POST['country'] ?? '') === $code ? 'selected' : '' ?>><?= e($n) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label><?= e(t('form.city', $LANG)) ?></label>
            <input type="text" name="city" class="form-control" value="<?= e($_POST['city'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label><?= e(t('form.participation', $LANG)) ?> <span class="req">*</span></label>
            <select name="participation_type" class="form-control" required>
              <option value="delegate"><?= e(t('participation.delegate', $LANG)) ?></option>
              <option value="speaker"><?= e(t('participation.speaker', $LANG)) ?></option>
              <option value="press"><?= e(t('participation.press', $LANG)) ?></option>
              <option value="investor"><?= e(t('participation.investor', $LANG)) ?></option>
              <option value="sponsor"><?= e(t('participation.sponsor', $LANG)) ?></option>
              <option value="observer"><?= e(t('participation.observer', $LANG)) ?></option>
            </select>
          </div>
        </div>

        <h2 style="font-size:22px;margin:32px 0 24px;border-bottom:1px solid var(--line);padding-bottom:16px;">
          <?= $LANG === 'ru' ? 'Профессиональная информация' : ($LANG === 'tj' ? 'Маълумоти касбӣ' : 'Professional information') ?>
        </h2>

        <div class="form-row">
          <div class="form-group">
            <label><?= e(t('form.organization', $LANG)) ?> <span class="req">*</span></label>
            <input type="text" name="organization" class="form-control" required value="<?= e($_POST['organization'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label><?= e(t('form.position', $LANG)) ?> <span class="req">*</span></label>
            <input type="text" name="position" class="form-control" required value="<?= e($_POST['position'] ?? '') ?>">
          </div>
        </div>

        <div class="form-group">
          <label><?= e(t('form.interests', $LANG)) ?></label>
          <input type="text" name="interests" class="form-control" placeholder="<?= $LANG==='ru'?'Например: инвестиции, технологии добычи, ESG':'e.g. investment, mining tech, ESG' ?>" value="<?= e($_POST['interests'] ?? '') ?>">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label><?= e(t('form.dietary', $LANG)) ?></label>
            <input type="text" name="dietary" class="form-control" value="<?= e($_POST['dietary'] ?? '') ?>">
          </div>
        </div>

        <div class="form-group">
          <label><?= e(t('form.comments', $LANG)) ?></label>
          <textarea name="comments" class="form-control" rows="4"><?= e($_POST['comments'] ?? '') ?></textarea>
        </div>

        <h2 style="font-size:22px;margin:32px 0 8px;border-bottom:1px solid var(--line);padding-bottom:16px;">
          <?= $LANG === 'ru' ? 'Документы участника' : ($LANG === 'tj' ? 'Ҳуҷҷатҳои иштирокчӣ' : 'Participant documents') ?>
        </h2>
        <p style="color:var(--ink-3);font-size:13px;margin-bottom:24px;">
          <?= $LANG === 'ru'
              ? 'Для аккредитации и оформления бейджа необходимо загрузить портретное фото 3×4 и копию паспорта. Документы хранятся конфиденциально и используются только оргкомитетом форума.'
              : ($LANG === 'tj'
                ? 'Барои аккредитатсия ва тайёр кардани нишон сурати 3×4 ва нусхаи шиносномаро бор кунед. Ҳуҷҷатҳо махфӣ нигоҳ дошта мешаванд.'
                : 'For accreditation and badge issuance, please upload a 3×4 portrait photo and a copy of your passport. Documents are stored confidentially and used only by the Organizing Committee.') ?>
        </p>

        <div class="form-row">
          <div class="form-group">
            <label><?= e($labels['photo_label']) ?> <span class="req">*</span></label>
            <label class="form-file" for="photo_file">
              <span id="photo_label_text">📷 <?= $LANG === 'ru' ? 'Выберите файл' : ($LANG === 'tj' ? 'Файлро интихоб кунед' : 'Choose file') ?></span>
              <input type="file" id="photo_file" name="photo" accept="image/jpeg,image/png" required>
            </label>
            <div class="form-file-hint"><?= e($labels['photo_hint']) ?></div>
            <div class="form-file-preview" id="photo_preview"></div>
          </div>

          <div class="form-group">
            <label><?= e($labels['passport_label']) ?> <span class="req">*</span></label>
            <label class="form-file" for="passport_file">
              <span id="passport_label_text">📄 <?= $LANG === 'ru' ? 'Выберите файл' : ($LANG === 'tj' ? 'Файлро интихоб кунед' : 'Choose file') ?></span>
              <input type="file" id="passport_file" name="passport" accept="image/jpeg,image/png,application/pdf" required>
            </label>
            <div class="form-file-hint"><?= e($labels['passport_hint']) ?></div>
            <div class="form-file-preview" id="passport_preview"></div>
          </div>
        </div>

        <label class="form-check" style="margin:32px 0 24px;">
          <input type="checkbox" name="agree" value="1" required>
          <span><?= e(t('form.agree', $LANG)) ?></span>
        </label>

        <button type="submit" class="btn btn--primary btn--block"><?= e(t('btn.register_now', $LANG)) ?></button>
      </form>
    <?php endif; ?>
  </div>
</section>
