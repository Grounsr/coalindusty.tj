<?php /** @var string $LANG */ /** @var array $pageMeta */
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow"><?= e(t('nav.contacts', $LANG)) ?></span>
    <h1><?= e($pageMeta['title']) ?></h1>
    <p class="subtitle"><?= e($pageMeta['subtitle']) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="contacts-grid">

      <aside class="contact-info">
        <div class="contact-block reveal">
          <h3><?= e(t('contacts.org_committee', $LANG)) ?></h3>
          <p><strong style="color:var(--ink-1);">Email</strong></p>
          <p><a href="mailto:<?= e(setting('site_email_info')) ?>"><?= e(setting('site_email_info')) ?></a></p>
          <p><a href="mailto:<?= e(setting('site_email_reg')) ?>"><?= e(setting('site_email_reg')) ?></a></p>
        </div>
        <div class="contact-block reveal">
          <h3>Phone</h3>
          <p><a href="tel:<?= e(preg_replace('/\s/', '', setting('site_phone'))) ?>"><?= e(setting('site_phone')) ?></a></p>
        </div>
        <div class="contact-block reveal">
          <h3><?= e(t('contacts.address', $LANG)) ?></h3>
          <p><?= e(setting_i18n('site_venue', $LANG)) ?></p>
          <p style="color:var(--ink-3);font-size:14px;"><?= e(setting_i18n('site_address', $LANG)) ?></p>
        </div>
        <?php if (setting('site_telegram')): ?>
        <div class="contact-block reveal">
          <h3>Telegram</h3>
          <p><a href="<?= e(setting('site_telegram')) ?>" target="_blank" rel="noopener"><?= e(setting('site_telegram')) ?></a></p>
        </div>
        <?php endif; ?>
      </aside>

      <div class="form-card reveal">
        <h2 style="font-size:24px;margin-bottom:8px;"><?= e(t('contacts.send_message', $LANG)) ?></h2>
        <p style="color:var(--ink-3);font-size:14px;margin-bottom:24px;">
          <?= $LANG === 'ru' ? 'Опишите ваш вопрос — мы ответим в течение 1 рабочего дня.' : ($LANG === 'tj' ? 'Саволи худро шарҳ диҳед — мо дар муддати як рӯзи корӣ ҷавоб медиҳем.' : 'Describe your question — we will respond within 1 business day.') ?>
        </p>

        <?php if ($success): ?>
        <div class="form-alert form-alert--success">
          <?= $LANG === 'ru' ? 'Сообщение отправлено. Спасибо за обращение.' : ($LANG === 'tj' ? 'Паём фиристода шуд. Ташаккур.' : 'Message sent. Thank you for reaching out.') ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="form-alert form-alert--error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="contact">

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
              <label><?= e(t('form.subject', $LANG)) ?></label>
              <input type="text" name="subject" class="form-control">
            </div>
          </div>

          <div class="form-group">
            <label><?= e(t('form.message', $LANG)) ?> <span class="req">*</span></label>
            <textarea name="message" class="form-control" rows="6" required></textarea>
          </div>

          <label class="form-check" style="margin-bottom:24px;">
            <input type="checkbox" name="agree" value="1" required>
            <span><?= e(t('form.agree', $LANG)) ?></span>
          </label>

          <button type="submit" class="btn btn--primary"><?= e(t('btn.send', $LANG)) ?></button>
        </form>
      </div>
    </div>
  </div>
</section>
