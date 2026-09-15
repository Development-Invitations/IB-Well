<?php
// ---------------------------------------------------------------
// Ishonchli Buxgalter — общий конфиг
// Хранилище: JSON-файлы в /data (без СУБД, по требованию проекта)
// ---------------------------------------------------------------
error_reporting(E_ALL & ~E_DEPRECATED);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_DIR', dirname(__DIR__));
define('DATA_DIR', ROOT_DIR . '/data');
define('SITE_NAME', 'Ishonchli Buxgalter Academy');
define('SITE_VERSION', '1.6.0');
define('BUILD_DATE', '2026-09-14');

// Базовый URL (для ссылок в письмах/telegram), подставьте при необходимости
if (!defined('SITE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('SITE_URL', $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
}

// Основной домен сайта (не поддомена админки) — используется только для
// ссылки "Открыть сайт" в админке, если админка живёт на отдельном поддомене.
// Поменяйте на свой домен после переноса на хостинг.
if (!defined('MAIN_SITE_URL')) {
    define('MAIN_SITE_URL', 'https://ishonchli-buxgalter.uz');
}

/**
 * Прочитать JSON-файл из /data. Возвращает $default, если файла нет.
 */
function read_json($filename, $default = []) {
    $path = DATA_DIR . '/' . $filename;
    if (!file_exists($path)) {
        return $default;
    }
    $fh = fopen($path, 'r');
    if (!$fh) return $default;
    flock($fh, LOCK_SH);
    $raw = stream_get_contents($fh);
    flock($fh, LOCK_UN);
    fclose($fh);
    $data = json_decode($raw, true);
    return is_null($data) ? $default : $data;
}

/**
 * Записать данные в JSON-файл /data (с блокировкой, чтобы не было гонок
 * между одновременными заявками).
 */
function write_json($filename, $data) {
    $path = DATA_DIR . '/' . $filename;
    $fh = fopen($path, 'c+');
    if (!$fh) return false;
    flock($fh, LOCK_EX);
    ftruncate($fh, 0);
    rewind($fh);
    fwrite($fh, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    fflush($fh);
    flock($fh, LOCK_UN);
    fclose($fh);
    return true;
}

function uid($prefix = '') {
    return $prefix . bin2hex(random_bytes(6));
}

function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header('Location: ' . $path);
    exit;
}
