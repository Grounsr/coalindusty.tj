<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';
$adminTitle = 'Администраторы';
$action = $_GET['a'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$me = admin();

// Only superadmin can manage admins
if ($me['role'] !== 'superadmin') {
    flash('error', 'Доступ запрещён: только суперадмин может управлять администраторами');
    header('Location: /admin/'); exit;
}

if ($action === 'delete' && $id) {
    csrf_required();
    if ($id === (int)$me['id']) { flash('error', 'Нельзя удалить себя'); header('Location: /admin/admins.php'); exit; }
    DB::delete('admins', 'id=?', [$id]);
    log_action('delete_admin', 'admin', $id);
    flash('success', 'Администратор удалён');
    header('Location: /admin/admins.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create','edit'], true)) {
    csrf_required();
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $fullName  = trim($_POST['full_name'] ?? '');
    $role      = $_POST['role'] ?? 'editor';
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$username || !$email || !$fullName) { flash('error', 'Заполните все обязательные поля'); header('Location: /admin/admins.php?a='.$action.($id?'&id='.$id:'')); exit; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { flash('error', 'Некорректный email'); header('Location: /admin/admins.php?a='.$action.($id?'&id='.$id:'')); exit; }
    if ($action === 'create' && !$password) { flash('error', 'Пароль обязателен при создании'); header('Location: /admin/admins.php?a=create'); exit; }
    if ($password && $password !== $password2) { flash('error', 'Пароли не совпадают'); header('Location: /admin/admins.php?a='.$action.($id?'&id='.$id:'')); exit; }
    if ($password && strlen($password) < 8) { flash('error', 'Пароль должен быть минимум 8 символов'); header('Location: /admin/admins.php?a='.$action.($id?'&id='.$id:'')); exit; }

    // Prevent last superadmin demoting self
    if ($action === 'edit' && $id === (int)$me['id'] && $role !== 'superadmin') {
        $superCount = (int)DB::value('SELECT COUNT(*) FROM admins WHERE role="superadmin"');
        if ($superCount <= 1) { flash('error', 'Нельзя понизить последнего суперадмина'); header('Location: /admin/admins.php?a=edit&id='.$id); exit; }
    }

    // Check unique username/email
    if ($action === 'create') {
        if (DB::value('SELECT id FROM admins WHERE username=?', [$username])) { flash('error', 'Логин уже занят'); header('Location: /admin/admins.php?a=create'); exit; }
        if (DB::value('SELECT id FROM admins WHERE email=?', [$email])) { flash('error', 'Email уже используется'); header('Location: /admin/admins.php?a=create'); exit; }
    } else {
        if (DB::value('SELECT id FROM admins WHERE username=? AND id!=?', [$username,$id])) { flash('error', 'Логин уже занят'); header('Location: /admin/admins.php?a=edit&id='.$id); exit; }
        if (DB::value('SELECT id FROM admins WHERE email=? AND id!=?', [$email,$id])) { flash('error', 'Email уже используется'); header('Location: /admin/admins.php?a=edit&id='.$id); exit; }
    }

    $data = ['username'=>$username, 'email'=>$email, 'full_name'=>$fullName, 'role'=>$role];
    if ($password) $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]);

    if ($action === 'create') { $id = DB::insert('admins', $data); log_action('create_admin','admin',$id); }
    else { DB::update('admins', $data, 'id=:id', ['id'=>$id]); log_action('update_admin','admin',$id); }
    flash('success', $action==='create' ? 'Администратор создан' : 'Сохранено');
    header('Location: /admin/admins.php'); exit;
}

if ($action === 'edit' && $id) {
    $current = DB::row('SELECT id, username, email, full_name, role, last_login_at, created_at FROM admins WHERE id=?', [$id]);
}

require __DIR__ . '/_layout_top.php';
?>

<?php if ($action === 'list'):
$list = DB::all('SELECT id, username, email, full_name, role, last_login_at, created_at FROM admins ORDER BY role DESC, full_name'); ?>
<div class="card">
  <div class="card-header"><h2>Администраторы</h2><a href="/admin/admins.php?a=create" class="btn btn--primary">+ Добавить</a></div>
  <?php if (!$list): ?><div class="empty">Администраторов нет.</div><?php else: ?>
  <table class="data">
    <thead><tr><th>Логин</th><th>ФИО</th><th>Email</th><th>Роль</th><th>Последний вход</th><th>Создан</th><th class="actions">Действия</th></tr></thead>
    <tbody><?php foreach ($list as $a): ?>
    <tr <?= $a['id']==$me['id']?'style="background:var(--surface-3,rgba(255,215,0,.05));"':'' ?>>
      <td><strong><?= e($a['username']) ?></strong> <?= $a['id']==$me['id']?'<span class="badge badge--ok" style="font-size:10px;">я</span>':'' ?></td>
      <td><?= e($a['full_name']) ?></td>
      <td><a href="mailto:<?= e($a['email']) ?>" style="font-size:13px;"><?= e($a['email']) ?></a></td>
      <td><span class="badge <?= $a['role']==='superadmin'?'badge--gold':'badge--neutral' ?>"><?= e($a['role']) ?></span></td>
      <td style="font-size:12px;color:var(--ink-3);"><?= $a['last_login_at'] ? date('d.m.Y H:i', strtotime($a['last_login_at'])) : '—' ?></td>
      <td style="font-size:12px;color:var(--ink-3);"><?= date('d.m.Y', strtotime($a['created_at'])) ?></td>
      <td class="actions">
        <a href="/admin/admins.php?a=edit&id=<?= (int)$a['id'] ?>" class="btn btn--sm btn--primary">✎</a>
        <?php if ($a['id'] != $me['id']): ?>
        <a href="/admin/admins.php?a=delete&id=<?= (int)$a['id'] ?>&_csrf=<?= e($_SESSION['csrf']) ?>" class="btn btn--sm btn--danger" onclick="return confirm('Удалить администратора <?= e(addslashes($a['username'])) ?>?')">🗑</a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?></tbody>
  </table>
  <?php endif; ?>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header"><h2><?= $action==='edit' ? 'Редактирование администратора' : 'Новый администратор' ?></h2><a href="/admin/admins.php" class="btn btn--sm">← Список</a></div>
  <?php if ($action==='edit' && !empty($current)): ?>
  <div style="padding:8px 16px;background:var(--surface-2);font-size:12px;color:var(--ink-3);">
    Создан: <?= date('d.m.Y H:i', strtotime($current['created_at'])) ?>
    | Последний вход: <?= $current['last_login_at'] ? date('d.m.Y H:i', strtotime($current['last_login_at'])) : 'никогда' ?>
  </div>
  <?php endif; ?>
  <form method="POST">
    <?= csrf_field() ?>
    <div class="field-row">
      <div class="field">
        <label>Логин <span class="req">*</span></label>
        <input type="text" name="username" class="input" required value="<?= e($current['username'] ?? '') ?>" pattern="[a-zA-Z0-9_\-]+" title="Только буквы, цифры, _ и -">
      </div>
      <div class="field">
        <label>Email <span class="req">*</span></label>
        <input type="email" name="email" class="input" required value="<?= e($current['email'] ?? '') ?>">
      </div>
    </div>
    <div class="field-row">
      <div class="field">
        <label>ФИО <span class="req">*</span></label>
        <input type="text" name="full_name" class="input" required value="<?= e($current['full_name'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Роль</label>
        <select name="role" class="select">
          <option value="editor" <?= ($current['role'] ?? 'editor')==='editor'?'selected':'' ?>>Редактор</option>
          <option value="superadmin" <?= ($current['role'] ?? '')==='superadmin'?'selected':'' ?>>Суперадмин</option>
        </select>
      </div>
    </div>
    <h3 style="margin:20px 0 12px;color:var(--gold-2);font-size:13px;text-transform:uppercase;letter-spacing:.1em;"><?= $action==='create' ? 'Пароль' : 'Изменить пароль (оставьте пустым, чтобы не менять)' ?></h3>
    <div class="field-row">
      <div class="field">
        <label>Пароль <?= $action==='create'?'<span class="req">*</span>':'' ?></label>
        <input type="password" name="password" class="input" autocomplete="new-password" minlength="8" <?= $action==='create'?'required':'' ?> placeholder="Минимум 8 символов">
      </div>
      <div class="field">
        <label>Повторите пароль</label>
        <input type="password" name="password2" class="input" autocomplete="new-password" placeholder="Повторите пароль">
      </div>
    </div>
    <button type="submit" class="btn btn--primary" style="margin-top:16px;">💾 Сохранить</button>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
