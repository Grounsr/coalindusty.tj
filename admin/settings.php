<?php
require_once __DIR__ . '/_bootstrap.php';
$adminTitle = 'Настройки сайта';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_required();

    // settings (key-value)
    foreach (($_POST['settings'] ?? []) as $k => $v) {
        $k = preg_replace('/[^a-z0-9_\.]/i', '', $k);
        if (!$k) continue;
        $exists = DB::value('SELECT key_name FROM settings WHERE key_name = ?', [$k]);
        if ($exists) DB::update('settings', ['value' => (string)$v], 'key_name = :k', ['k' => $k]);
        else DB::insert('settings', ['key_name' => $k, 'value' => (string)$v]);
    }

    // settings_i18n
    foreach (($_POST['i18n'] ?? []) as $k => $byLang) {
        $k = preg_replace('/[^a-z0-9_\.]/i', '', $k);
        if (!$k) continue;
        foreach ($byLang as $lang => $v) {
            if (!in_array($lang, ['en','ru','tj'], true)) continue;
            $exists = DB::value('SELECT key_name FROM settings_i18n WHERE key_name=? AND lang=?', [$k, $lang]);
            if ($exists) DB::update('settings_i18n', ['value' => (string)$v], 'key_name=:k AND lang=:l', ['k'=>$k,'l'=>$lang]);
            else DB::insert('settings_i18n', ['key_name' => $k, 'lang' => $lang, 'value' => (string)$v]);
        }
    }

    log_action('update_settings');
    flash('success', 'Настройки сохранены');
    header('Location: /admin/settings.php');
    exit;
}

$settings = [];
foreach (DB::all('SELECT key_name, value FROM settings') as $s) $settings[$s['key_name']] = $s['value'];

$i18n = [];
foreach (DB::all('SELECT key_name, lang, value FROM settings_i18n') as $r) $i18n[$r['key_name']][$r['lang']] = $r['value'];

$get = fn($k, $d='') => $settings[$k] ?? $d;
$getI = fn($k, $l, $d='') => $i18n[$k][$l] ?? $d;

require __DIR__ . '/_layout_top.php';
?>

<form method="POST" action="">
  <?= csrf_field() ?>

  <div class="card">
    <div class="card-header"><h2>Контакты</h2></div>
    <div class="field-row">
      <div class="field">
        <label>Email для общих вопросов</label>
        <input type="email" name="settings[site_email_info]" class="input" value="<?= e($get('site_email_info')) ?>">
      </div>
      <div class="field">
        <label>Email для регистраций</label>
        <input type="email" name="settings[site_email_reg]" class="input" value="<?= e($get('site_email_reg')) ?>">
      </div>
    </div>
    <div class="field-row">
      <div class="field">
        <label>Email для рассылки (no-reply)</label>
        <input type="email" name="settings[site_email_noreply]" class="input" value="<?= e($get('site_email_noreply')) ?>">
      </div>
      <div class="field">
        <label>Телефон</label>
        <input type="text" name="settings[site_phone]" class="input" value="<?= e($get('site_phone')) ?>">
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Социальные сети</h2></div>
    <div class="field-row">
      <div class="field"><label>Telegram</label><input type="text" name="settings[site_telegram]" class="input" value="<?= e($get('site_telegram')) ?>" placeholder="https://t.me/..."></div>
      <div class="field"><label>Facebook</label><input type="text" name="settings[site_facebook]" class="input" value="<?= e($get('site_facebook')) ?>"></div>
    </div>
    <div class="field-row">
      <div class="field"><label>Instagram</label><input type="text" name="settings[site_instagram]" class="input" value="<?= e($get('site_instagram')) ?>"></div>
      <div class="field"><label>LinkedIn</label><input type="text" name="settings[site_linkedin]" class="input" value="<?= e($get('site_linkedin')) ?>"></div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>SMTP — отправка писем</h2></div>
    <div class="field-row">
      <div class="field"><label>SMTP сервер</label><input type="text" name="settings[smtp_host]" class="input" value="<?= e($get('smtp_host')) ?>" placeholder="smtp.timeweb.ru"></div>
      <div class="field"><label>Порт</label><input type="number" name="settings[smtp_port]" class="input" value="<?= e($get('smtp_port')) ?>" placeholder="465"></div>
    </div>
    <div class="field-row">
      <div class="field"><label>Шифрование</label>
        <select name="settings[smtp_encryption]" class="select">
          <option value="ssl" <?= $get('smtp_encryption') === 'ssl' ? 'selected' : '' ?>>SSL (порт 465)</option>
          <option value="tls" <?= $get('smtp_encryption') === 'tls' ? 'selected' : '' ?>>TLS (порт 587)</option>
        </select>
      </div>
      <div class="field"><label>Имя отправителя</label><input type="text" name="settings[smtp_from_name]" class="input" value="<?= e($get('smtp_from_name')) ?>"></div>
    </div>
    <div class="field-row">
      <div class="field"><label>Логин SMTP</label><input type="text" name="settings[smtp_user]" class="input" value="<?= e($get('smtp_user')) ?>"></div>
      <div class="field"><label>Пароль SMTP</label><input type="password" name="settings[smtp_password]" class="input" placeholder="<?= $get('smtp_password') ? '••• сохранён •••' : 'не задан' ?>"></div>
    </div>
    <p class="help">⚠️ Пароль SMTP также можно задать в файле <code>includes/config.php</code> — он переопределяет значение из БД.</p>
  </div>

  <div class="card">
    <div class="card-header"><h2>Общее</h2></div>
    <div class="field-row">
      <div class="field"><label>Язык по умолчанию</label>
        <select name="settings[default_lang]" class="select">
          <option value="en" <?= $get('default_lang') === 'en' ? 'selected' : '' ?>>English</option>
          <option value="ru" <?= $get('default_lang') === 'ru' ? 'selected' : '' ?>>Русский</option>
          <option value="tj" <?= $get('default_lang') === 'tj' ? 'selected' : '' ?>>Тоҷикӣ</option>
        </select>
      </div>
      <div class="field"><label>Дата проведения форума</label><input type="date" name="settings[forum_date]" class="input" value="<?= e($get('forum_date')) ?>"></div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Аналитика</h2></div>
    <div class="field-row">
      <div class="field"><label>Yandex.Metrika ID</label><input type="text" name="settings[analytics_yandex]" class="input" value="<?= e($get('analytics_yandex')) ?>" placeholder="12345678"></div>
      <div class="field"><label>Google Analytics ID</label><input type="text" name="settings[analytics_google]" class="input" value="<?= e($get('analytics_google')) ?>" placeholder="G-XXXXXXX"></div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Название и адрес (мультиязычные)</h2></div>
    <?php
    $i18nKeys = [
      'site_name' => 'Название сайта',
      'site_tagline' => 'Слоган',
      'site_venue' => 'Площадка форума',
      'site_address' => 'Полный адрес',
    ];
    foreach ($i18nKeys as $key => $label):
    ?>
    <div style="padding:14px 0;border-bottom:1px solid var(--line);">
      <div style="margin-bottom:8px;color:var(--gold-2);font-size:13px;font-weight:600;"><?= e($label) ?> <code style="color:var(--ink-4);font-weight:400;font-size:11px;">(<?= e($key) ?>)</code></div>
      <div class="field-row--3">
        <?php foreach (['en','ru','tj'] as $lang): ?>
        <div class="field">
          <label><?= strtoupper($lang) ?></label>
          <input type="text" name="i18n[<?= e($key) ?>][<?= $lang ?>]" class="input" value="<?= e($getI($key, $lang)) ?>">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <button type="submit" class="btn btn--primary" style="margin-top:8px;">💾 Сохранить все настройки</button>
</form>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
