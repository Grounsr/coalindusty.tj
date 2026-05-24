<?php
require_once INCLUDES_DIR . '/email_templates.php';

$rid = (int)($_POST['rid'] ?? 0);
$reg = DB::row('SELECT * FROM registrations WHERE id = ?', [$rid]);
if (!$reg || $reg['email_verified']) {
    header('Location: /?p=register&lang=' . $LANG);
    exit;
}

$code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
DB::update('registrations', [
    'verification_code'    => $code,
    'verification_expires' => date('Y-m-d H:i:s', time() + 1800),
], 'id = :id', ['id' => $rid]);

send_mail($reg['email'], 'Email verification code · Coal Industry Forum', email_verification($reg['full_name'], $code, $reg['lang']));

header('Location: /?p=register&step=verify&rid=' . $rid . '&lang=' . $LANG);
exit;
