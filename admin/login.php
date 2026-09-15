<?php
require_once dirname(__DIR__) . '/includes/auth.php';
if (current_admin()) redirect('index.php');

if (!file_exists(DATA_DIR . '/admin.json')) {
    redirect('/install.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (login_admin(trim($_POST['login'] ?? ''), $_POST['password'] ?? '')) {
        redirect('index.php');
    }
    $error = 'Неверный логин или пароль.';
}
?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Вход в админку</title>
<link rel="icon" href="images/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="style.css">
</head>
<body class="admin-body">
<div class="admin-login-wrap">
  <div class="form-card" style="width:100%;max-width:400px;">
    <h1 style="font-size:20px;">Вход в панель управления</h1>
    <p class="muted"><?= SITE_NAME ?> — админка сайта</p>
    <?php if ($error): ?><p style="color:#8B2E2E;font-size:14.5px;"><?= h($error) ?></p><?php endif; ?>
    <form method="post">
      <div class="form-row">
        <label for="login">Логин</label>
        <input type="text" id="login" name="login" required autofocus>
      </div>
      <div class="form-row">
        <label for="password">Пароль</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center;">Войти</button>
    </form>
  </div>
</div>
</body>
</html>
