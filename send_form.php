<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$to = 'reg@coalindustry.tj';
$subject = 'Новая регистрация на Coal Forum 2026';

// Сбор данных формы
$firstName = htmlspecialchars($_POST['firstName'] ?? '');
$lastName = htmlspecialchars($_POST['lastName'] ?? '');
$email = htmlspecialchars($_POST['email'] ?? '');
$phone = htmlspecialchars($_POST['phone'] ?? '');
$company = htmlspecialchars($_POST['company'] ?? '');
$jobTitle = htmlspecialchars($_POST['jobTitle'] ?? '');
$country = htmlspecialchars($_POST['country'] ?? '');
$sector = htmlspecialchars($_POST['sector'] ?? '');
$package = htmlspecialchars($_POST['package'] ?? '');
$days = isset($_POST['days']) ? implode(', ', $_POST['days']) : '';
$dietary = htmlspecialchars($_POST['dietary'] ?? '');
$visa = htmlspecialchars($_POST['visa'] ?? '');
$comments = htmlspecialchars($_POST['comments'] ?? '');

// Формирование письма
$message = "
===========================================
НОВАЯ РЕГИСТРАЦИЯ НА COAL FORUM 2026
===========================================

ЛИЧНЫЕ ДАННЫЕ:
--------------
Имя: $firstName
Фамилия: $lastName
Email: $email
Телефон: $phone

ОРГАНИЗАЦИЯ:
------------
Компания: $company
Должность: $jobTitle
Страна: $country
Сектор: $sector

УЧАСТИЕ:
--------
Пакет: $package
Дни участия: $days
Питание: $dietary
Визовая поддержка: $visa

КОММЕНТАРИИ:
------------
$comments

===========================================
Отправлено: " . date('d.m.Y H:i:s') . "
===========================================
";

$headers = [
    'From: noreply@coalindustry.tj',
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion()
];

$success = mail($to, $subject, $message, implode("\r\n", $headers));

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Registration submitted']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to send email']);
}
?>
