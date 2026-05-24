<?php
require_once INCLUDES_DIR . '/email_templates.php';

$fullName = trim($_POST['full_name'] ?? '');
$email    = strtolower(trim($_POST['email'] ?? ''));
$phone    = trim($_POST['phone'] ?? '');
$company  = trim($_POST['company'] ?? '');
$position = trim($_POST['position'] ?? '');
$country  = trim($_POST['country'] ?? '');
$level    = trim($_POST['interest_level'] ?? '');
$message  = trim($_POST['message'] ?? '');
$agree    = !empty($_POST['agree']);

if (!$fullName || !$email || !$agree || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /?p=investors&error=' . urlencode('Please fill all required fields') . '&lang=' . $LANG);
    exit;
}

$id = DB::insert('investor_inquiries', [
    'full_name'      => mb_substr($fullName, 0, 250),
    'email'          => mb_substr($email, 0, 180),
    'phone'          => mb_substr($phone, 0, 60),
    'company'        => mb_substr($company, 0, 250),
    'position'       => mb_substr($position, 0, 250),
    'country'        => mb_substr($country, 0, 120),
    'interest_level' => mb_substr($level, 0, 120),
    'message'        => mb_substr($message, 0, 5000),
]);

$inq = DB::row('SELECT * FROM investor_inquiries WHERE id = ?', [$id]);
send_mail(setting('site_email_info', 'info@coalindustry.tj'), 'Запрос инвестора · форум 2026', email_investor_notify($inq), ['Reply-To' => $email]);

header('Location: /?p=investors&success=1&lang=' . $LANG . '#investor-contact');
exit;
