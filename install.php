<?php
require_once __DIR__ . '/includes/config.php';

$adminExists = file_exists(DATA_DIR . '/admin.json');
$error = '';
$done = false;

if ($adminExists) {
    // Установка уже выполнена — из соображений безопасности форму больше не показываем.
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    if ($login === '' || $password === '') {
        $error = 'Заполните логин и пароль.';
    } elseif ($password !== $password2) {
        $error = 'Пароли не совпадают.';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не короче 6 символов.';
    } else {
        write_json('admin.json', [
            'login' => $login,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        $done = true;
    }
}
?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Установка — <?= SITE_NAME ?></title>
<link rel="stylesheet" href="/style.css">
</head>
<body style="background:var(--ink);min-height:100vh;display:flex;align-items:center;justify-content:center;">
<div class="form-card" style="width:100%;max-width:440px;">
  <h1 style="font-size:22px;">Первичная настройка</h1>

  <?php if ($adminExists && !$done): ?>
    <p class="muted">Администратор уже создан. Этот файл можно удалить с сервера — им больше нельзя воспользоваться повторно.</p>
    <a href="/admin/login.php" class="btn btn-gold" style="width:100%;justify-content:center;">Перейти к входу в админку</a>

  <?php elseif ($done): ?>
    <p style="color:#2E4A2F;">Аккаунт администратора создан. Теперь удалите файл <code>install.php</code> с сервера в целях безопасности.</p>
    <a href="/admin/login.php" class="btn btn-gold" style="width:100%;justify-content:center;">Войти в админку</a>

  <?php else: ?>
    <p class="muted">Задайте логин и пароль администратора панели. Это разовая настройка — после её выполнения страница отключится.</p>
    <?php if ($error): ?><p style="color:#8B2E2E;"><?= h($error) ?></p><?php endif; ?>
    <form method="post">
      <div class="form-row">
        <label for="login">Логин администратора</label>
        <input type="text" id="login" name="login" required>
      </div>
      <div class="form-row">
        <label for="password">Пароль</label>
        <input type="password" id="password" name="password" required minlength="6">
      </div>
      <div class="form-row">
        <label for="password2">Повторите пароль</label>
        <input type="password" id="password2" name="password2" required minlength="6">
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center;">Создать администратора</button>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
