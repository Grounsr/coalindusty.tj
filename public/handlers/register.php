<?php
/**
 * Handler: register
 * Creates a participant registration, validates uploaded photo + passport,
 * sends 6-digit verification code, redirects to ?step=verify.
 */
declare(strict_types=1);

require_once INCLUDES_DIR . '/email_templates.php';

$year = current_year();
if (!$year) {
    header('Location: /?p=register&error=' . urlencode('Forum year not configured') . '&lang=' . $LANG);
    exit;
}

$lang = $LANG;
$err = function(string $msg) use ($lang) {
    $_SESSION['old_post'] = $_POST;
    header('Location: /?p=register&error=' . urlencode($msg) . '&lang=' . $lang);
    exit;
};

// --- Basic fields ---
$fullName  = trim($_POST['full_name'] ?? '');
$email     = strtolower(trim($_POST['email'] ?? ''));
$phone     = trim($_POST['phone'] ?? '');
$country   = trim($_POST['country'] ?? '');
$city      = trim($_POST['city'] ?? '');
$org       = trim($_POST['organization'] ?? '');
$position  = trim($_POST['position'] ?? '');
$part      = $_POST['participation_type'] ?? 'delegate';
$interests = trim($_POST['interests'] ?? '');
$dietary   = trim($_POST['dietary'] ?? '');
$comments  = trim($_POST['comments'] ?? '');
$agree     = !empty($_POST['agree']);

$messages = [
    'en' => ['fields' => 'Please fill all required fields.', 'email' => 'Invalid email address.', 'agree' => 'You must accept the data processing terms.', 'photo' => 'Photo upload failed.', 'passport' => 'Passport upload failed.', 'size_photo' => 'Photo size exceeds 5 MB.', 'size_passport' => 'Passport size exceeds 10 MB.', 'type_photo' => 'Photo must be JPG or PNG.', 'type_passport' => 'Passport must be JPG, PNG or PDF.', 'dup' => 'You have already registered with this email.'],
    'ru' => ['fields' => 'Заполните все обязательные поля.', 'email' => 'Некорректный email.', 'agree' => 'Необходимо согласие на обработку данных.', 'photo' => 'Не удалось загрузить фото.', 'passport' => 'Не удалось загрузить копию паспорта.', 'size_photo' => 'Размер фото больше 5 МБ.', 'size_passport' => 'Размер паспорта больше 10 МБ.', 'type_photo' => 'Фото должно быть JPG или PNG.', 'type_passport' => 'Паспорт должен быть JPG, PNG или PDF.', 'dup' => 'Вы уже зарегистрированы с этим email.'],
    'tj' => ['fields' => 'Майдонҳои ҳатмиро пур кунед.', 'email' => 'Почтаи нодуруст.', 'agree' => 'Розигӣ ҳатмист.', 'photo' => 'Бор кардани сурат натиҷа надод.', 'passport' => 'Бор кардани шиноснома натиҷа надод.', 'size_photo' => 'Андозаи сурат аз 5 МБ зиёд аст.', 'size_passport' => 'Андозаи шиноснома аз 10 МБ зиёд аст.', 'type_photo' => 'Сурат бояд JPG ё PNG бошад.', 'type_passport' => 'Шиноснома бояд JPG, PNG ё PDF бошад.', 'dup' => 'Шумо аллакай бо ин почта сабти ном кардаед.'],
];
$M = $messages[$lang];

if (!$fullName || !$email || !$phone || !$country || !$org || !$position) $err($M['fields']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err($M['email']);
if (!$agree) $err($M['agree']);
if (!in_array($part, ['delegate','speaker','press','investor','sponsor','observer'], true)) $part = 'delegate';

// --- Check duplicate ---
$exists = DB::value('SELECT id FROM registrations WHERE email = ? AND forum_year_id = ?', [$email, $year['id']]);
if ($exists) $err($M['dup']);

// --- File uploads ---
if (empty($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) $err($M['photo']);
if (empty($_FILES['passport']) || $_FILES['passport']['error'] !== UPLOAD_ERR_OK) $err($M['passport']);

if ($_FILES['photo']['size'] > 5 * 1024 * 1024) $err($M['size_photo']);
if ($_FILES['passport']['size'] > 10 * 1024 * 1024) $err($M['size_passport']);

$photoMime = mime_content_type($_FILES['photo']['tmp_name']);
$passportMime = mime_content_type($_FILES['passport']['tmp_name']);

if (!in_array($photoMime, ['image/jpeg','image/png'], true)) $err($M['type_photo']);
if (!in_array($passportMime, ['image/jpeg','image/png','application/pdf'], true)) $err($M['type_passport']);

// --- Insert registration row first to get an ID ---
$code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

$id = DB::insert('registrations', [
    'forum_year_id'        => $year['id'],
    'full_name'            => mb_substr($fullName, 0, 250),
    'email'                => mb_substr($email, 0, 180),
    'phone'                => mb_substr($phone, 0, 60),
    'country'              => mb_substr($country, 0, 120),
    'city'                 => mb_substr($city, 0, 120),
    'organization'         => mb_substr($org, 0, 250),
    'position'             => mb_substr($position, 0, 250),
    'participation_type'   => $part,
    'interests'            => mb_substr($interests, 0, 500),
    'dietary'              => mb_substr($dietary, 0, 250),
    'comments'             => mb_substr($comments, 0, 2000),
    'verification_code'    => $code,
    'verification_expires' => date('Y-m-d H:i:s', time() + 1800),
    'status'               => 'pending',
    'lang'                 => $lang,
    'ip_address'           => $_SERVER['REMOTE_ADDR'] ?? null,
    'user_agent'           => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
]);

// --- Save files ---
$dir = UPLOAD_DIR . '/registrations/' . $id;
@mkdir($dir, 0755, true);

$photoExt = $photoMime === 'image/png' ? 'png' : 'jpg';
$passportExt = $passportMime === 'application/pdf' ? 'pdf' : ($passportMime === 'image/png' ? 'png' : 'jpg');

$photoRel    = '/uploads/registrations/' . $id . '/photo-3x4.' . $photoExt;
$passportRel = '/uploads/registrations/' . $id . '/passport.' . $passportExt;

move_uploaded_file($_FILES['photo']['tmp_name'],    ROOT_DIR . $photoRel);
move_uploaded_file($_FILES['passport']['tmp_name'], ROOT_DIR . $passportRel);

DB::update('registrations', [
    'photo_path'    => $photoRel,
    'passport_path' => $passportRel,
], 'id = :id', ['id' => $id]);

// --- Send verification code ---
$html = email_verification($fullName, $code, $lang);
send_mail($email, 'Email verification code · Coal Industry Forum', $html);

// --- Redirect to verify step ---
unset($_SESSION['old_post']);
header('Location: /?p=register&step=verify&rid=' . $id . '&lang=' . $lang);
exit;
