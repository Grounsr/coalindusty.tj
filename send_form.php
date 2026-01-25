<?php
// Настройки
$to = 'reg@coalindustry.tj';
$subject_prefix = 'Coal Forum 2026 - Новая регистрация: ';

// Защита от спама
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'error' => 'Method not allowed']));
}

// Проверка обязательных полей
$required = ['firstName', 'lastName', 'email', 'phone', 'company', 'jobTitle', 'country', 'sector', 'package'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        die(json_encode(['success' => false, 'error' => "Missing field: $field"]));
    }
}

// Сбор данных
$firstName = htmlspecialchars(trim($_POST['firstName']));
$lastName = htmlspecialchars(trim($_POST['lastName']));
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($_POST['phone']));
$company = htmlspecialchars(trim($_POST['company']));
$jobTitle = htmlspecialchars(trim($_POST['jobTitle']));
$country = htmlspecialchars(trim($_POST['country']));
$sector = htmlspecialchars(trim($_POST['sector']));
$package = htmlspecialchars(trim($_POST['package']));
$days = isset($_POST['days']) ? (is_array($_POST['days']) ? implode(', ', $_POST['days']) : $_POST['days']) : 'Не указано';
$dietary = htmlspecialchars(trim($_POST['dietary'] ?? 'Не указано'));
$visa = htmlspecialchars(trim($_POST['visa'] ?? 'Не указано'));
$comments = htmlspecialchars(trim($_POST['comments'] ?? ''));

// Проверка email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Invalid email']));
}

// Названия пакетов
$packageNames = [
    'virtual' => 'Онлайн-участие ($150)',
    'full' => 'Очное участие ($450)',
    'gov' => 'Госструктуры/Академия ($250)',
    'vip' => 'VIP-делегация ($1,200)',
    'booth' => 'Выставочный стенд ($2,500)'
];
$packageDisplay = $packageNames[$package] ?? $package;

// Названия стран
$countryNames = [
    'TJ' => 'Таджикистан',
    'UZ' => 'Узбекистан',
    'KZ' => 'Казахстан',
    'KG' => 'Кыргызстан',
    'RU' => 'Россия',
    'CN' => 'Китай',
    'TR' => 'Турция',
    'AE' => 'ОАЭ',
    'other' => 'Другая'
];
$countryDisplay = $countryNames[$country] ?? $country;

// Формирование письма
$subject = $subject_prefix . $firstName . ' ' . $lastName;

$message = "
══════════════════════════════════════════
   МЕЖДУНАРОДНЫЙ УГОЛЬНЫЙ ФОРУМ 2026
        НОВАЯ ЗАЯВКА НА УЧАСТИЕ
══════════════════════════════════════════

▶ ЛИЧНЫЕ ДАННЫЕ
──────────────────────────────────────────
   Имя:        $firstName
   Фамилия:    $lastName
   Email:      $email
   Телефон:    $phone

▶ ОРГАНИЗАЦИЯ
──────────────────────────────────────────
   Компания:   $company
   Должность:  $jobTitle
   Страна:     $countryDisplay
   Сектор:     $sector

▶ ДЕТАЛИ УЧАСТИЯ
──────────────────────────────────────────
   Пакет:              $packageDisplay
   Дни участия:        $days
   Питание:            $dietary
   Визовая поддержка:  $visa

▶ КОММЕНТАРИИ
──────────────────────────────────────────
$comments

══════════════════════════════════════════
   Дата заявки: " . date('d.m.Y в H:i') . "
   IP: " . $_SERVER['REMOTE_ADDR'] . "
══════════════════════════════════════════
";

// Заголовки письма
$headers = "From: Coal Forum <noreply@coalindustry.tj>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Отправка
$success = mail($to, "=?UTF-8?B?" . base64_encode($subject) . "?=", $message, $headers);

// Ответ
header('Content-Type: application/json; charset=utf-8');

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Заявка отправлена']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Ошибка отправки email']);
}
?>
