<?php
require_once __DIR__ . '/includes/config.php';

function check_row($label, $ok, $detail = '') {
    $icon = $ok ? '✅' : '❌';
    echo '<tr><td>' . h($label) . '</td><td style="text-align:center;">' . $icon . '</td><td class="muted" style="font-size:13px;">' . h($detail) . '</td></tr>';
}

$phpOk = version_compare(PHP_VERSION, '8.0.0', '>=');

$criticalFiles = [
    'includes/header.php' => 'Шапка сайта',
    'includes/footer.php' => 'Футер сайта',
    'includes/auth.php' => 'Логика входа/регистрации',
    'includes/breadcrumbs.php' => 'Хлебные крошки',
    'images/logo-mark.png' => 'Логотип (PNG)',
    'images/favicon.svg' => 'Фавикон (SVG)',
    'style.css' => 'Стили сайта',
    'admin/style.css' => 'Стили админки',
    'data/courses.json' => 'Данные: курсы',
    'data/posts.json' => 'Данные: блог',
    'data/settings.json' => 'Данные: настройки ботов',
];

$dataWritable = is_writable(DATA_DIR);
$writeTestOk = false;
if ($dataWritable) {
    $testFile = DATA_DIR . '/.write_test';
    $writeTestOk = @file_put_contents($testFile, 'ok') !== false;
    if ($writeTestOk) @unlink($testFile);
}

$adminExists = file_exists(DATA_DIR . '/admin.json');
$leadsCount = count(read_json('leads.json', []));
$usersCount = count(read_json('users.json', []));
?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Проверка установки — <?= SITE_NAME ?></title>
<link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="/style.css">
<style>
  body{background:var(--ink);}
  .hc-wrap{max-width:720px;margin:0 auto;padding:48px 20px;}
  .hc-table{width:100%;border-collapse:collapse;background:var(--paper);}
  .hc-table td{padding:12px 16px;border-bottom:1px solid var(--line-soft);font-size:14.5px;}
  .hc-table tr:last-child td{border-bottom:none;}
  .hc-badge{display:inline-block;padding:4px 12px;border-radius:2px;font-size:13px;font-weight:600;}
  .hc-ok{background:#E7F0E7;color:#2E5E2E;}
  .hc-bad{background:#FBE7E7;color:#8B2E2E;}
</style>
</head>
<body>
<div class="hc-wrap">
  <div class="form-card" style="max-width:none;">
    <h1 style="font-size:22px;">Проверка установки сайта</h1>
    <p class="muted">
      Версия сборки: <b><?= h(SITE_VERSION) ?></b> от <?= h(BUILD_DATE) ?> ·
      PHP <?= h(PHP_VERSION) ?>
    </p>

    <h2 style="font-size:16px;margin-top:24px;">1. Окружение сервера</h2>
    <table class="hc-table">
      <?php check_row('PHP версия 8.0 или выше', $phpOk, PHP_VERSION); ?>
      <?php check_row('Папка data/ доступна на запись', $dataWritable && $writeTestOk, $dataWritable ? 'права на чтение есть, тест записи ' . ($writeTestOk ? 'успешен' : 'провален') : 'нет прав на запись'); ?>
    </table>

    <h2 style="font-size:16px;margin-top:24px;">2. Файлы сайта</h2>
    <table class="hc-table">
      <?php foreach ($criticalFiles as $path => $label): ?>
        <?php check_row($label, file_exists(ROOT_DIR . '/' . $path), $path); ?>
      <?php endforeach; ?>
    </table>

    <h2 style="font-size:16px;margin-top:24px;">3. Состояние данных</h2>
    <table class="hc-table">
      <?php if ($adminExists): ?>
        <?php check_row('Администратор создан', true, 'можно входить в /admin/login.php'); ?>
      <?php else: ?>
        <?php check_row('Администратор ещё не создан', false, 'откройте /install.php и задайте логин/пароль'); ?>
      <?php endif; ?>
      <?php check_row('Пользователей зарегистрировано', true, $usersCount . ' чел.'); ?>
      <?php check_row('Заявок в базе', true, $leadsCount . ' шт.'); ?>
    </table>

    <p class="muted" style="font-size:13px;margin-top:24px;">
      Если все пункты выше зелёные — сайт установлен верно и все функции (логотип,
      регистрация, админка, хлебные крошки) должны отображаться в браузере. Если
      что-то красное — именно это нужно поправить на хостинге (перезалить файл /
      выставить права на папку data/).
    </p>
    <p class="muted" style="font-size:12px;">
      Эту страницу стоит удалить или закрыть паролем после того, как убедитесь, что всё работает —
      она не хранит секретов, но не обязана быть видна всем посетителям.
    </p>
  </div>
</div>
</body>
</html>
