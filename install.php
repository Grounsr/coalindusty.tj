<?php
/**
 * One-time installer.
 * Run this from your browser after uploading files and creating config.php.
 * Delete this file after successful install.
 */
declare(strict_types=1);

$configFile = __DIR__ . '/includes/config.php';
if (!file_exists($configFile)) {
    die('1) Скопируйте includes/config.sample.php → includes/config.php и заполните данные БД.');
}

$CONFIG = require $configFile;
$msg = '';
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $CONFIG['db']['host'], $CONFIG['db']['port'] ?? 3306, $CONFIG['db']['name']),
            $CONFIG['db']['user'], $CONFIG['db']['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // Run schema
        $schema = file_get_contents(__DIR__ . '/database/schema.sql');
        foreach (explode(';', $schema) as $stmt) {
            $stmt = trim($stmt);
            if ($stmt) $pdo->exec($stmt);
        }

        // Run seed (skip admin & duplicates)
        $seed = file_get_contents(__DIR__ . '/database/seed.sql');
        // Remove admin INSERT — we'll do it with the form password
        $seed = preg_replace("/INSERT INTO `admins`[^;]+;/s", '', $seed);
        foreach (explode(';', $seed) as $stmt) {
            $stmt = trim($stmt);
            if (!$stmt) continue;
            try { $pdo->exec($stmt); } catch (Throwable $e) { /* ignore duplicates */ }
        }

        // Create admin from form
        $u = trim($_POST['admin_user'] ?? 'admin');
        $em = trim($_POST['admin_email'] ?? 'info@coalindustry.tj');
        $pw = $_POST['admin_pass'] ?? '';
        $name = trim($_POST['admin_name'] ?? 'Администратор');

        if (strlen($pw) < 8) throw new RuntimeException('Пароль должен быть не короче 8 символов.');

        $hash = password_hash($pw, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $pdo->prepare('INSERT INTO admins (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, "superadmin")
            ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), full_name = VALUES(full_name), role = "superadmin"');
        $stmt->execute([$u, $em, $hash, $name]);

        // Ensure upload dirs
        foreach (['news','years','speakers','leadership','partners','gallery','downloads','registrations'] as $cat) {
            @mkdir(__DIR__ . '/uploads/' . $cat, 0755, true);
        }
        @mkdir(__DIR__ . '/logs', 0755, true);

        $ok = true;
        $msg = 'Установка завершена. Удалите файл <code>install.php</code> с сервера.';
    } catch (Throwable $e) {
        $msg = 'Ошибка: ' . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Установка · Coal Forum CMS</title>
<style>
body{font-family:-apple-system,sans-serif;background:#0a0c10;color:#e6e6e6;margin:0;padding:40px 20px;min-height:100vh;}
.box{max-width:520px;margin:0 auto;background:#11141a;border:1px solid rgba(255,255,255,.07);border-radius:16px;padding:36px;}
h1{font-size:22px;margin:0 0 16px;}
label{display:block;font-size:11px;color:#8b91a0;text-transform:uppercase;letter-spacing:.1em;margin:14px 0 6px;}
input{width:100%;padding:10px 12px;background:#0a0c10;border:1px solid rgba(255,255,255,.12);border-radius:8px;color:#fff;font-size:14px;box-sizing:border-box;}
button{margin-top:24px;width:100%;padding:14px;background:linear-gradient(135deg,#c9a449,#8a6b1f);color:#0a0c10;border:0;border-radius:8px;font-weight:700;cursor:pointer;font-size:14px;}
.msg{padding:12px 14px;border-radius:8px;margin:16px 0;font-size:13px;}
.ok{background:rgba(76,175,120,.15);color:#7ed4a6;border:1px solid rgba(76,175,120,.3);}
.err{background:rgba(226,106,60,.15);color:#ec9374;border:1px solid rgba(226,106,60,.3);}
</style></head><body>
<div class="box">
  <h1>🛠 Установка Coal Forum CMS</h1>
  <p style="color:#8b91a0;font-size:13.5px;">Создайте первую учётную запись администратора. Этот шаг выполняется один раз.</p>
  <?php if ($msg): ?><div class="msg <?= $ok ? 'ok' : 'err' ?>"><?= $msg ?></div><?php endif; ?>
  <?php if (!$ok): ?>
  <form method="POST">
    <label>Логин</label><input type="text" name="admin_user" value="admin" required>
    <label>ФИО</label><input type="text" name="admin_name" value="Администратор форума" required>
    <label>Email</label><input type="email" name="admin_email" value="info@coalindustry.tj" required>
    <label>Пароль (мин. 8 символов)</label><input type="password" name="admin_pass" required minlength="8">
    <button type="submit">Установить</button>
  </form>
  <?php else: ?>
  <p style="text-align:center;margin-top:24px;"><a href="/admin/" style="color:#c9a449;font-weight:600;">→ Войти в админ-панель</a></p>
  <?php endif; ?>
</div>
</body></html>
