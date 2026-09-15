<?php
require_once __DIR__ . '/config.php';

// ================= Пользователи сайта (кто оставляет заявки) =================

function current_user() {
    if (empty($_SESSION['user_id'])) return null;
    $users = read_json('users.json', []);
    foreach ($users as $u) {
        if ($u['id'] === $_SESSION['user_id']) return $u;
    }
    return null;
}

function require_login() {
    if (!current_user()) {
        redirect('login.php?next=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

function find_user_by_login($login) {
    $users = read_json('users.json', []);
    foreach ($users as $u) {
        if (strtolower($u['login']) === strtolower($login)) return $u;
    }
    return null;
}

function register_user($name, $phone, $login, $password) {
    $users = read_json('users.json', []);
    if (find_user_by_login($login)) {
        return ['ok' => false, 'error' => 'Такой логин уже занят, выберите другой.'];
    }
    $user = [
        'id' => uid('u_'),
        'name' => $name,
        'phone' => $phone,
        'login' => $login,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'created_at' => date('c'),
    ];
    $users[] = $user;
    write_json('users.json', $users);
    $_SESSION['user_id'] = $user['id'];
    return ['ok' => true, 'user' => $user];
}

function login_user($login, $password) {
    $user = find_user_by_login($login);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['ok' => false, 'error' => 'Неверный логин или пароль.'];
    }
    $_SESSION['user_id'] = $user['id'];
    return ['ok' => true, 'user' => $user];
}

function logout_user() {
    unset($_SESSION['user_id']);
}

// ================= Админ =================

function current_admin() {
    return !empty($_SESSION['admin']) ? $_SESSION['admin'] : null;
}

function require_admin() {
    if (!current_admin()) {
        redirect('login.php');
    }
}

function login_admin($login, $password) {
    $admin = read_json('admin.json', null);
    if (!$admin || strtolower($admin['login']) !== strtolower($login) || !password_verify($password, $admin['password_hash'])) {
        return false;
    }
    $_SESSION['admin'] = $admin['login'];
    return true;
}

function logout_admin() {
    unset($_SESSION['admin']);
}
