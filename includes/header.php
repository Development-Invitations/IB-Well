<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/breadcrumbs.php';
$__user = current_user();
$__active = $active ?? '';
function nav_class($key, $active) { return $key === $active ? 'active' : ''; }
?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? h($page_title) . ' — ' . SITE_NAME : SITE_NAME ?></title>
<link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
<link rel="alternate icon" href="/images/logo-mark.png" type="image/png">
<link rel="stylesheet" href="/style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <a href="/index.php" class="brand">
      <span class="brand-mark"><img src="/images/logo-mark.png" alt="Ishonchli Buxgalter"></span>
      <span class="brand-name"><span class="brand-main">Ishonchli Buxgalter</span><span class="brand-sub">Academy</span></span>
    </a>
    <nav class="main-nav">
      <a href="/index.php" class="<?= nav_class('home', $__active) ?>">Главная</a>
      <a href="/courses.php" class="<?= nav_class('courses', $__active) ?>">Курсы</a>
      <a href="/about.php" class="<?= nav_class('about', $__active) ?>">О нас</a>
      <a href="/blog.php" class="<?= nav_class('blog', $__active) ?>">Блог</a>
      <a href="/contacts.php" class="<?= nav_class('contacts', $__active) ?>">Контакты</a>
      <?php if ($__user): ?>
        <div class="nav-extra-group">
          <a href="/account.php" class="nav-extra <?= nav_class('account', $__active) ?>">Личный кабинет</a>
          <a href="/admin/login.php" class="nav-extra">Админ-панель</a>
        </div>
      <?php else: ?>
        <div class="nav-extra-group">
          <a href="/register.php" class="nav-extra">Регистрация</a>
          <a href="/login.php" class="nav-extra">Войти</a>
          <a href="/admin/login.php" class="nav-extra">Админ-панель</a>
        </div>
      <?php endif; ?>
    </nav>
    <div class="header-actions">
      <span class="header-phone">+998 99 918 00 10</span>
      <a href="/admin/login.php" class="admin-entry-link header-auth-btn">Админ</a>
      <?php if ($__user): ?>
        <a href="/account.php" class="btn btn-ghost-light header-auth-btn" style="border-color:rgba(250,248,244,.35);">
          <?= h($__user['name']) ?>
        </a>
      <?php else: ?>
        <a href="/register.php" class="btn btn-ghost-light header-auth-btn" style="border-color:rgba(250,248,244,.35);">Регистрация</a>
        <a href="/login.php" class="btn btn-ghost-light header-auth-btn" style="border-color:rgba(250,248,244,.35);">Войти</a>
      <?php endif; ?>
      <a href="/contacts.php" class="btn btn-gold">Записаться</a>
      <button class="nav-toggle" aria-label="Меню">☰</button>
    </div>
  </div>
</header>
