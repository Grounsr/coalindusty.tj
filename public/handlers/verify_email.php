<?php
/**
 * Handler: verify_email
 * Confirms registration via 6-digit code, then sends welcome email
 * to the participant and notification email to reg@coalindustry.tj.
 */
declare(strict_types=1);

require_once INCLUDES_DIR . '/email_templates.php';

$lang = $LANG;
$rid = (int)($_POST['rid'] ?? 0);
$code = preg_replace('/\D/', '', (string)($_POST['code'] ?? ''));

$messages = [
    'en' => ['notfound' => 'Registration not found.', 'expired' => 'Verification code expired. Please request a new one.', 'invalid' => 'Invalid verification code.'],
    'ru' => ['notfound' => 'Заявка не найдена.', 'expired' => 'Срок действия кода истёк. Запросите новый.', 'invalid' => 'Неверный код подтверждения.'],
    'tj' => ['notfound' => 'Ариза ёфт нашуд.', 'expired' => 'Муҳлати рамз гузашт. Аз нав дархост кунед.', 'invalid' => 'Рамзи нодуруст.'],
];
$M = $messages[$lang];

$reg = DB::row('SELECT * FROM registrations WHERE id = ?', [$rid]);
if (!$reg) {
    header('Location: /?p=register&step=verify&rid=' . $rid . '&error=' . urlencode($M['notfound']) . '&lang=' . $lang);
    exit;
}

if (strtotime((string)$reg['verification_expires']) < time()) {
    header('Location: /?p=register&step=verify&rid=' . $rid . '&error=' . urlencode($M['expired']) . '&lang=' . $lang);
    exit;
}

if (!hash_equals((string)$reg['verification_code'], (string)$code)) {
    header('Location: /?p=register&step=verify&rid=' . $rid . '&error=' . urlencode($M['invalid']) . '&lang=' . $lang);
    exit;
}

// Mark verified
DB::update('registrations', [
    'email_verified' => 1,
    'status' => 'verified',
    'verified_at' => date('Y-m-d H:i:s'),
    'verification_code' => null,
], 'id = :id', ['id' => $rid]);

$reg = DB::row('SELECT * FROM registrations WHERE id = ?', [$rid]);

// --- Send notification to reg@coalindustry.tj ---
$regMail = setting('site_email_reg', 'reg@coalindustry.tj');
$notifyHtml = email_registration_notify($reg, $lang);
send_mail($regMail, 'Новая регистрация на форум · #' . str_pad((string)$rid, 6, '0', STR_PAD_LEFT), $notifyHtml);

// --- Send premium welcome to participant ---
$welcomeHtml = email_welcome_participant($reg['full_name'], $reg, $lang);
$subjects = [
    'en' => 'Welcome to the International Coal Industry Forum 2026',
    'ru' => 'Добро пожаловать на Международный форум угольной промышленности 2026',
    'tj' => 'Хуш омадед ба Форуми байналмилалии саноати ангишт 2026',
];
send_mail($reg['email'], $subjects[$lang], $welcomeHtml);

header('Location: /?p=register&step=done&lang=' . $lang);
exit;
