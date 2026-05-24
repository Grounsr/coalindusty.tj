<?php
require_once INCLUDES_DIR . '/email_templates.php';

$fullName = trim($_POST['full_name'] ?? '');
$email    = strtolower(trim($_POST['email'] ?? ''));
$phone    = trim($_POST['phone'] ?? '');
$subject  = trim($_POST['subject'] ?? '');
$message  = trim($_POST['message'] ?? '');
$agree    = !empty($_POST['agree']);

if (!$fullName || !$email || !$message || !$agree || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /?p=contacts&error=' . urlencode('Please fill all required fields') . '&lang=' . $LANG);
    exit;
}

$id = DB::insert('contact_messages', [
    'full_name' => mb_substr($fullName, 0, 250),
    'email'     => mb_substr($email, 0, 180),
    'phone'     => mb_substr($phone, 0, 60),
    'subject'   => mb_substr($subject, 0, 250),
    'message'   => mb_substr($message, 0, 5000),
]);

$msg = DB::row('SELECT * FROM contact_messages WHERE id = ?', [$id]);
send_mail(setting('site_email_info', 'info@coalindustry.tj'), 'Сообщение с сайта: ' . ($subject ?: 'без темы'), email_contact_notify($msg), ['Reply-To' => $email]);

header('Location: /?p=contacts&success=1&lang=' . $LANG);
exit;
