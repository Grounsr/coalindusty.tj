<?php
/**
 * HTML email templates — premium dark theme with gold accents,
 * "forum banner" header, inline CSS only (best email-client compatibility).
 */
declare(strict_types=1);

function email_wrap(string $title, string $bodyHtml, string $lang = 'ru'): string
{
    $siteName = setting_i18n('site_name', $lang, 'Coal Industry Forum');
    $tagline  = setting_i18n('site_tagline', $lang, '');
    $venue    = setting_i18n('site_venue', $lang, '');
    $year     = current_year()['year'] ?? date('Y');
    $infoMail = setting('site_email_info', 'info@coalindustry.tj');
    $phone    = setting('site_phone', '+992 37 232-00-00');

    $bannerLabels = [
        'en' => ['date' => 'November 25, ' . $year, 'venue' => $venue],
        'ru' => ['date' => '25 ноября ' . $year, 'venue' => $venue],
        'tj' => ['date' => '25 ноябри ' . $year, 'venue' => $venue],
    ];
    $b = $bannerLabels[$lang] ?? $bannerLabels['en'];

    return <<<HTML
<!DOCTYPE html>
<html lang="$lang">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>$title</title>
</head>
<body style="margin:0;padding:0;background:#0a0b0d;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;color:#e6e6e6;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#0a0b0d;padding:32px 16px;">
  <tr><td align="center">
    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#13151a;border-radius:16px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,.45);">

      <!-- Premium banner -->
      <tr><td style="background:linear-gradient(135deg,#1a1d24 0%,#0a0b0d 60%,#1a1d24 100%);padding:40px 36px;border-bottom:2px solid #c9a449;">
        <table width="100%"><tr>
          <td>
            <div style="display:inline-block;padding:6px 14px;background:rgba(201,164,73,.12);border:1px solid rgba(201,164,73,.4);border-radius:999px;color:#e8c878;font-size:11px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;">{$b['date']}</div>
            <h1 style="margin:18px 0 6px;font-family:'Playfair Display',Georgia,serif;font-size:28px;font-weight:700;color:#f5f3ef;line-height:1.2;">{$siteName}</h1>
            <p style="margin:0;color:#a8a8a8;font-size:14px;line-height:1.5;">$tagline</p>
          </td>
        </tr></table>
      </td></tr>

      <!-- Body -->
      <tr><td style="padding:36px 36px 24px 36px;color:#e0e0e0;font-size:15px;line-height:1.7;">
        $bodyHtml
      </td></tr>

      <!-- Footer -->
      <tr><td style="background:#0a0b0d;padding:24px 36px;border-top:1px solid #22262e;color:#888;font-size:12px;line-height:1.6;">
        <p style="margin:0 0 6px;color:#c9a449;font-weight:600;">{$siteName}</p>
        <p style="margin:0 0 4px;">{$b['venue']}</p>
        <p style="margin:0 0 4px;"><a href="mailto:$infoMail" style="color:#c9a449;text-decoration:none;">$infoMail</a> · $phone</p>
        <p style="margin:12px 0 0;color:#5a5a5a;font-size:11px;">© $year Coal Industry Forum — Ministry of Industry and New Technologies of the Republic of Tajikistan</p>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
}

function email_verification(string $name, string $code, string $lang = 'ru'): string
{
    $titles = [
        'en' => 'Verify your email address',
        'ru' => 'Подтверждение электронной почты',
        'tj' => 'Тасдиқи почтаи электронӣ',
    ];
    $intros = [
        'en' => "Hello, <b>$name</b>. Thank you for registering for the International Coal Industry Forum. Please use the code below to confirm your email address:",
        'ru' => "Здравствуйте, <b>$name</b>. Благодарим за регистрацию на Международный форум угольной промышленности. Используйте код ниже для подтверждения вашей электронной почты:",
        'tj' => "Салом, <b>$name</b>. Барои сабти ном дар Форуми байналмилалии саноати ангишт ташаккур. Лутфан рамзи зеринро барои тасдиқ ворид кунед:",
    ];
    $note = [
        'en' => 'The code is valid for 30 minutes. If you did not initiate this registration, simply ignore this email.',
        'ru' => 'Код действителен 30 минут. Если вы не регистрировались — просто проигнорируйте это письмо.',
        'tj' => 'Рамз 30 дақиқа эътибор дорад. Агар шумо сабти ном накарда бошед — ин паёмро нодида гиред.',
    ];

    $body = '<p style="margin:0 0 16px;">' . $intros[$lang] . '</p>'
          . '<div style="margin:28px 0;padding:24px;background:#0a0b0d;border:1px solid #2a2e36;border-radius:12px;text-align:center;">'
          .   '<div style="font-family:Georgia,serif;font-size:36px;font-weight:700;color:#e8c878;letter-spacing:10px;">' . e($code) . '</div>'
          . '</div>'
          . '<p style="margin:16px 0 0;color:#9a9a9a;font-size:13px;">' . $note[$lang] . '</p>';

    return email_wrap($titles[$lang], $body, $lang);
}

function email_welcome_participant(string $name, array $reg, string $lang = 'ru'): string
{
    $titles = [
        'en' => 'Welcome to the Coal Industry Forum',
        'ru' => 'Добро пожаловать на Угольный форум',
        'tj' => 'Хуш омадед ба Форуми ангишт',
    ];
    $intros = [
        'en' => "Dear <b>$name</b>,<br>your registration for the International Coal Industry Forum has been successfully confirmed. Welcome to a premier gathering of ministers, investors, and industry leaders.",
        'ru' => "Уважаемый(ая) <b>$name</b>,<br>ваша регистрация на Международный форум угольной промышленности успешно подтверждена. Добро пожаловать на ключевое событие года в Центральной Азии.",
        'tj' => "Муҳтарам <b>$name</b>,<br>сабти номи шумо дар Форуми байналмилалии саноати ангишт бо муваффақият тасдиқ карда шуд. Хуш омадед ба чорабинии калидӣ.",
    ];
    $details = [
        'en' => ['Date','Venue','Type','Reference'],
        'ru' => ['Дата','Место','Тип участия','Номер заявки'],
        'tj' => ['Сана','Ҷой','Намуди иштирок','Рақами ариза'],
    ];
    $footers = [
        'en' => 'You will receive practical information (badge collection, dress code, transfer details) closer to the event date. The forum Secretariat remains at your service for any questions.',
        'ru' => 'Практическая информация (получение бейджа, дресс-код, трансфер) будет направлена ближе к дате мероприятия. Секретариат форума остаётся на связи по любым вопросам.',
        'tj' => 'Иттилооти амалӣ (гирифтани нишон, дресс-код, нақлиёт) наздиктар ба сана ирсол хоҳад шуд. Котиботи форум барои саволҳои шумо дастрас аст.',
    ];

    $partLabels = ['delegate' => t('participation.delegate', $lang), 'speaker' => t('participation.speaker', $lang),
                   'press' => t('participation.press', $lang), 'investor' => t('participation.investor', $lang),
                   'sponsor' => t('participation.sponsor', $lang), 'observer' => t('participation.observer', $lang)];
    $part = $partLabels[$reg['participation_type']] ?? $reg['participation_type'];
    $venue = setting_i18n('site_venue', $lang);
    $dates = ['en' => 'November 25, 2026', 'ru' => '25 ноября 2026', 'tj' => '25 ноябри 2026'];

    $d = $details[$lang];
    $body  = '<p style="margin:0 0 20px;">' . $intros[$lang] . '</p>';
    $body .= '<table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;border-radius:12px;overflow:hidden;background:#0a0b0d;border:1px solid #2a2e36;">';
    $rows = [
        [$d[0], $dates[$lang]],
        [$d[1], e($venue)],
        [$d[2], e($part)],
        [$d[3], '#' . str_pad((string)$reg['id'], 6, '0', STR_PAD_LEFT)],
    ];
    foreach ($rows as $i => $r) {
        $bg = $i % 2 ? '#0e1014' : '#0a0b0d';
        $body .= '<tr><td style="padding:14px 20px;background:' . $bg . ';color:#9a9a9a;font-size:13px;width:40%;">' . e($r[0]) . '</td>'
              .  '<td style="padding:14px 20px;background:' . $bg . ';color:#f0f0f0;font-size:14px;font-weight:600;">' . $r[1] . '</td></tr>';
    }
    $body .= '</table>';

    // Premium "ticket" banner
    $body .= '<div style="margin:28px 0;padding:32px 24px;background:linear-gradient(135deg,#c9a449 0%,#8a6b1f 100%);border-radius:12px;text-align:center;color:#0a0b0d;">';
    $body .= '<div style="font-size:11px;letter-spacing:3px;text-transform:uppercase;font-weight:700;opacity:.8;">OFFICIAL CONFIRMATION</div>';
    $body .= '<div style="margin-top:8px;font-family:Georgia,serif;font-size:24px;font-weight:700;">' . ($lang === 'ru' ? 'Вы в списке участников' : ($lang === 'tj' ? 'Шумо дар рӯйхати иштирокчиён' : 'You\'re on the list')) . '</div>';
    $body .= '<div style="margin-top:6px;font-size:13px;opacity:.85;">Coal Industry Forum · Tajikistan 2026</div>';
    $body .= '</div>';

    $body .= '<p style="margin:20px 0 0;color:#9a9a9a;font-size:13px;">' . $footers[$lang] . '</p>';

    return email_wrap($titles[$lang], $body, $lang);
}

function email_registration_notify(array $reg, string $lang = 'ru'): string
{
    $body  = '<p style="margin:0 0 16px;color:#e8c878;font-size:13px;text-transform:uppercase;letter-spacing:2px;font-weight:600;">Новая регистрация на форум</p>';
    $body .= '<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">';
    $fields = [
        ['ФИО', $reg['full_name']],
        ['Email', $reg['email']],
        ['Телефон', $reg['phone'] ?: '—'],
        ['Страна', $reg['country'] ?: '—'],
        ['Город', $reg['city'] ?: '—'],
        ['Организация', $reg['organization'] ?: '—'],
        ['Должность', $reg['position'] ?: '—'],
        ['Тип участия', $reg['participation_type']],
        ['Интересы', $reg['interests'] ?: '—'],
        ['Особенности питания', $reg['dietary'] ?: '—'],
        ['Комментарий', $reg['comments'] ?: '—'],
        ['Язык заявки', strtoupper($reg['lang'])],
        ['Номер заявки', '#' . str_pad((string)$reg['id'], 6, '0', STR_PAD_LEFT)],
        ['IP', $reg['ip_address'] ?: '—'],
    ];
    foreach ($fields as $i => $r) {
        $bg = $i % 2 ? '#0e1014' : '#0a0b0d';
        $body .= '<tr><td style="padding:10px 14px;background:' . $bg . ';color:#9a9a9a;font-size:12px;width:38%;border-bottom:1px solid #1a1d24;">' . e($r[0]) . '</td>'
              .  '<td style="padding:10px 14px;background:' . $bg . ';color:#e8e8e8;font-size:13px;border-bottom:1px solid #1a1d24;">' . e((string)$r[1]) . '</td></tr>';
    }
    $body .= '</table>';

    $body .= '<p style="margin:18px 0 0;color:#7a7a7a;font-size:12px;">Заявка автоматически добавлена в админ-панель: <a href="https://coalindustry.tj/admin/registrations.php" style="color:#c9a449;">просмотр заявок</a></p>';

    return email_wrap('Новая регистрация · форум 2026', $body, 'ru');
}

function email_investor_notify(array $inq): string
{
    $body  = '<p style="margin:0 0 16px;color:#e8c878;font-size:13px;text-transform:uppercase;letter-spacing:2px;font-weight:600;">Новый запрос инвестора</p>';
    $body .= '<table width="100%" cellpadding="0" cellspacing="0">';
    $fields = [
        ['ФИО', $inq['full_name']],
        ['Email', $inq['email']],
        ['Телефон', $inq['phone'] ?: '—'],
        ['Компания', $inq['company'] ?: '—'],
        ['Должность', $inq['position'] ?: '—'],
        ['Страна', $inq['country'] ?: '—'],
        ['Уровень интереса', $inq['interest_level'] ?: '—'],
        ['Сообщение', nl2br(e($inq['message'] ?: '—'))],
    ];
    foreach ($fields as $i => $r) {
        $bg = $i % 2 ? '#0e1014' : '#0a0b0d';
        $body .= '<tr><td style="padding:10px 14px;background:' . $bg . ';color:#9a9a9a;font-size:12px;width:38%;border-bottom:1px solid #1a1d24;vertical-align:top;">' . e($r[0]) . '</td>'
              .  '<td style="padding:10px 14px;background:' . $bg . ';color:#e8e8e8;font-size:13px;border-bottom:1px solid #1a1d24;">' . $r[1] . '</td></tr>';
    }
    $body .= '</table>';
    return email_wrap('Запрос инвестора · форум 2026', $body, 'ru');
}

function email_contact_notify(array $msg): string
{
    $body  = '<p style="margin:0 0 16px;color:#e8c878;font-size:13px;text-transform:uppercase;letter-spacing:2px;font-weight:600;">Сообщение через форму контактов</p>';
    $body .= '<table width="100%" cellpadding="0" cellspacing="0">';
    $fields = [
        ['ФИО', $msg['full_name']],
        ['Email', $msg['email']],
        ['Телефон', $msg['phone'] ?: '—'],
        ['Тема', $msg['subject'] ?: '—'],
        ['Сообщение', nl2br(e($msg['message']))],
    ];
    foreach ($fields as $i => $r) {
        $bg = $i % 2 ? '#0e1014' : '#0a0b0d';
        $body .= '<tr><td style="padding:10px 14px;background:' . $bg . ';color:#9a9a9a;font-size:12px;width:38%;border-bottom:1px solid #1a1d24;vertical-align:top;">' . e($r[0]) . '</td>'
              .  '<td style="padding:10px 14px;background:' . $bg . ';color:#e8e8e8;font-size:13px;border-bottom:1px solid #1a1d24;">' . $r[1] . '</td></tr>';
    }
    $body .= '</table>';
    return email_wrap('Сообщение через форму контактов', $body, 'ru');
}
