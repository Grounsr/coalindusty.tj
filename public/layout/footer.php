</main>

<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="/?lang=<?= e($LANG) ?>" class="logo">
          <svg class="logo-icon" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect width="40" height="40" rx="6" fill="#0c0e12"/>
            <circle cx="20" cy="15" r="9" fill="none" stroke="#c9a449" stroke-width="1.5"/>
            <path d="M11.5 15h17 M14 11 A8 8 0 0 1 26 11 M14 19 A8 8 0 0 0 26 19" fill="none" stroke="#c9a449" stroke-width="1.2"/>
            <path d="M8 27 L13 22 L18 24.5 L23 20 L29 25 L32 30 L8 30 Z" fill="#1b1f26"/>
          </svg>
          <span class="logo-text">Coal Forum<span><?= e(setting_i18n('site_tagline', $LANG)) ?></span></span>
        </a>
        <p><?= e(t('footer.desc', $LANG)) ?></p>
      </div>

      <div class="footer-col">
        <h4><?= e(t('footer.links', $LANG)) ?></h4>
        <ul>
          <li><a href="/?p=about&lang=<?= e($LANG) ?>"><?= e(t('nav.about', $LANG)) ?></a></li>
          <li><a href="/?p=program&lang=<?= e($LANG) ?>"><?= e(t('nav.program', $LANG)) ?></a></li>
          <li><a href="/?p=speakers&lang=<?= e($LANG) ?>"><?= e(t('nav.speakers', $LANG)) ?></a></li>
          <li><a href="/?p=investors&lang=<?= e($LANG) ?>"><?= e(t('nav.investors', $LANG)) ?></a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4><?= e(t('nav.news', $LANG)) ?></h4>
        <ul>
          <li><a href="/?p=news&lang=<?= e($LANG) ?>"><?= e(t('nav.news', $LANG)) ?></a></li>
          <li><a href="/?p=archive&lang=<?= e($LANG) ?>"><?= e(t('nav.archive', $LANG)) ?></a></li>
          <li><a href="/?p=register&lang=<?= e($LANG) ?>"><?= e(t('nav.register', $LANG)) ?></a></li>
          <li><a href="/?p=contacts&lang=<?= e($LANG) ?>"><?= e(t('nav.contacts', $LANG)) ?></a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4><?= e(t('footer.contact', $LANG)) ?></h4>
        <ul>
          <li><a href="mailto:<?= e(setting('site_email_info')) ?>"><?= e(setting('site_email_info')) ?></a></li>
          <li><a href="mailto:<?= e(setting('site_email_reg')) ?>"><?= e(setting('site_email_reg')) ?></a></li>
          <li><a href="tel:<?= e(preg_replace('/\s/', '', setting('site_phone'))) ?>"><?= e(setting('site_phone')) ?></a></li>
          <?php if (setting('site_telegram')): ?>
          <li><a href="<?= e(setting('site_telegram')) ?>" target="_blank" rel="noopener">Telegram</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p><?= sprintf(e(t('footer.rights', $LANG)), date('Y')) ?></p>
      <p class="footer-organizer"><?= e(t('footer.organizer', $LANG)) ?></p>
    </div>
  </div>
</footer>

<script src="/assets/js/app.js" defer></script>
<?php if (setting('analytics_yandex')): ?>
<!-- Yandex.Metrika -->
<script>
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
   ym(<?= (int)setting('analytics_yandex') ?>, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true });
</script>
<?php endif; ?>
<?php if (setting('analytics_google')): ?>
<!-- GA4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(setting('analytics_google')) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)};gtag('js',new Date());gtag('config','<?= e(setting('analytics_google')) ?>');</script>
<?php endif; ?>
</body>
</html>
