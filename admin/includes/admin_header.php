<?php
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_admin();
$__active = $admin_active ?? '';
function anav($key, $active) { return $key === $active ? 'active' : ''; }
?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($admin_title) ? h($admin_title) . ' — Админка' : 'Админка' ?></title>
<link rel="icon" href="images/favicon.svg" type="image/svg+xml">
<link rel="alternate icon" href="images/logo-mark.png" type="image/png">
<link rel="stylesheet" href="style.css">
</head>
<body class="admin-body">
<div class="admin-shell">
  <aside class="admin-sidebar">
    <div class="brand"><span class="brand-mark"><img src="images/logo-mark.png" alt="Ishonchli Buxgalter"></span><span class="brand-name">Админка</span></div>
    <nav>
      <a href="index.php" class="<?= anav('dashboard', $__active) ?>">Дашборд</a>
      <a href="leads.php" class="<?= anav('leads', $__active) ?>">Заявки</a>
      <a href="courses.php" class="<?= anav('courses', $__active) ?>">Курсы</a>
      <a href="posts.php" class="<?= anav('posts', $__active) ?>">Блог</a>
      <a href="users.php" class="<?= anav('users', $__active) ?>">Пользователи</a>
      <a href="settings.php" class="<?= anav('settings', $__active) ?>">Настройки / Боты</a>
    </nav>
    <div class="sidebar-foot">
      <a href="<?= h(MAIN_SITE_URL) ?>" target="_blank">↗ Открыть сайт</a>
      <a href="<?= h(MAIN_SITE_URL) ?>/healthcheck.php" target="_blank">🩺 Проверка установки</a>
      <a href="logout.php">Выйти (<?= h(current_admin()) ?>)</a>
    </div>
  </aside>
  <main class="admin-main">
