<?php
/**
 * Coal Industry Forum — Configuration
 * Copy this file to config.php and fill in your Timeweb hosting credentials.
 * НИКОГДА не коммитьте config.php в публичный репозиторий.
 */

return [
    // --- Database (Timeweb -> Панель -> Базы данных MySQL) ---
    'db' => [
        'host'    => 'localhost',
        'port'    => 3306,
        'name'    => 'cu12345_coalfor',   // Замените на имя вашей БД
        'user'    => 'cu12345_coalfor',   // Замените на пользователя БД
        'pass'    => 'СюдаПарольБД',      // Пароль БД
        'charset' => 'utf8mb4',
    ],

    // --- Site URL (без слэша в конце) ---
    'site_url'  => 'https://coalindustry.tj',
    'admin_url' => 'https://coalindustry.tj/admin',

    // --- Languages ---
    'languages'    => ['en','ru','tj'],
    'default_lang' => 'en',

    // --- Security ---
    'session_name' => 'coalforum_sid',
    'csrf_secret'  => 'ИЗМЕНИТЕ_ЭТОТ_СЕКРЕТНЫЙ_КЛЮЧ_МИНИМУМ_32_СИМВОЛА',
    'cookie_secure'   => true,   // true для HTTPS
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',

    // --- Email / SMTP (Timeweb mail) ---
    'mail' => [
        'enabled'    => true,
        'host'       => 'smtp.timeweb.ru',
        'port'       => 465,
        'encryption' => 'ssl',         // 'ssl' (465) или 'tls' (587)
        'username'   => 'no-reply@coalindustry.tj',
        'password'   => 'ПарольПочтыNoReply',
        'from_email' => 'no-reply@coalindustry.tj',
        'from_name'  => 'Coal Industry Forum',
        'reg_email'  => 'reg@coalindustry.tj',
        'info_email' => 'info@coalindustry.tj',
    ],

    // --- Paths ---
    'upload_dir' => __DIR__ . '/../uploads',
    'upload_url' => '/uploads',

    'debug' => false,
];
