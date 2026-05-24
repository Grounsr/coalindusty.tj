<?php
require_once __DIR__ . '/_bootstrap.php';

$error = '';
if (!empty($_SESSION['admin_id'])) { header('Location: /admin/'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_required();
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $admin = DB::row('SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1', [$u, $u]);

    if ($admin && $admin['locked_until'] && strtotime($admin['locked_until']) > time()) {
        $error = 'Аккаунт временно заблокирован после нескольких неудачных попыток. Попробуйте позже.';
    } elseif ($admin && password_verify($p, $admin['password_hash'])) {
        DB::update('admins', [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'failed_attempts' => 0,
            'locked_until' => null,
        ], 'id = :id', ['id' => $admin['id']]);
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        log_action('login');
        header('Location: /admin/');
        exit;
    } else {
        if ($admin) {
            $attempts = $admin['failed_attempts'] + 1;
            $locked = $attempts >= 5 ? date('Y-m-d H:i:s', time() + 600) : null;
            DB::update('admins', ['failed_attempts' => $attempts, 'locked_until' => $locked], 'id = :id', ['id' => $admin['id']]);
        }
        $error = 'Неверный логин или пароль.';
        usleep(500000);
    }
}
?>
<!DOCTYPE html>
<html lang="ru"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Вход · Coal Forum CMS</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='6' fill='%2307080a'/%3E%3Ccircle cx='20' cy='15' r='9' fill='none' stroke='%23c9a449' stroke-width='1.5'/%3E%3C/svg%3E">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/admin/assets/admin.css">
</head><body>
<div class="login-bg">
  <div class="login-card">
    <div class="login-logo">
      <svg viewBox="0 0 40 40" width="40" height="40"><rect width="40" height="40" rx="6" fill="#0c0e12"/><circle cx="20" cy="15" r="9" fill="none" stroke="#c9a449" stroke-width="1.5"/><path d="M11.5 15h17 M14 11 A8 8 0 0 1 26 11 M14 19 A8 8 0 0 0 26 19" fill="none" stroke="#c9a449" stroke-width="1.2"/><path d="M8 27 L13 22 L18 24.5 L23 20 L29 25 L32 30 L8 30 Z" fill="#1b1f26"/></svg>
      <div style="font-family:'Playfair Display',serif;font-size:20px;color:#f1f3f7;font-weight:700;">Coal Forum CMS</div>
    </div>
    <h1>Вход в админ-панель</h1>
    <p class="sub">Управление сайтом Международного форума угольной промышленности</p>

    <?php if ($error): ?><div class="flash flash--error" style="margin:0 0 20px;"><?= e($error) ?></div><?php endif; ?>

    <form method="POST" action="">
      <?= csrf_field() ?>
      <div class="field">
        <label>Логин или Email</label>
        <input type="text" name="username" class="input" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Пароль</label>
        <input type="password" name="password" class="input" required>
      </div>
      <button type="submit" class="btn btn--primary" style="width:100%;justify-content:center;padding:14px;font-size:14px;">Войти</button>
    </form>

    <p style="text-align:center;margin-top:24px;color:#5b6072;font-size:12px;">
      Защищённый вход · Все попытки фиксируются
    </p>
  </div>
</div>
</body></html>
