<?php
require_once __DIR__ . '/includes/auth.php';
if (current_user()) redirect('account.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || $phone === '' || $login === '' || $password === '') {
        $error = 'Заполните все поля.';
    } elseif (!preg_match('/^\+998 \d{2} \d{3} \d{2} \d{2}$/', $phone)) {
        $error = 'Телефон должен быть в формате +998 90 123 45 67.';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не короче 6 символов.';
    } else {
        $result = register_user($name, $phone, $login, $password);
        if ($result['ok']) {
            redirect('account.php');
        } else {
            $error = $result['error'];
        }
    }
}

$page_title = 'Регистрация';
$active = 'account';
require __DIR__ . '/includes/header.php';
render_breadcrumbs([['label' => 'Регистрация']]);
?>

<div class="page-header">
  <div class="hero-ledger"></div>
  <div class="container">
    <div class="kicker">Личный кабинет</div>
    <h1>Регистрация</h1>
  </div>
</div>

<section>
  <div class="container" style="max-width:480px;">
    <div class="form-card" style="max-width:none;">
      <p class="muted">Аккаунт нужен, чтобы оставлять заявки на курсы и следить за их статусом.</p>
      <?php if ($error): ?><p style="color:#8B2E2E;font-size:14.5px;"><?= h($error) ?></p><?php endif; ?>
      <form method="post">
        <div class="form-row">
          <label for="name">Имя</label>
          <input type="text" id="name" name="name" value="<?= h($_POST['name'] ?? '') ?>" required>
        </div>
        <div class="form-row">
          <label for="phone">Телефон</label>
          <input type="tel" id="phone" name="phone" data-phone-mask placeholder="+998 90 123 45 67" maxlength="17" value="<?= h($_POST['phone'] ?? '') ?>" required>
        </div>
        <div class="form-row">
          <label for="login">Логин</label>
          <input type="text" id="login" name="login" value="<?= h($_POST['login'] ?? '') ?>" required>
        </div>
        <div class="form-row">
          <label for="password">Пароль</label>
          <input type="password" id="password" name="password" minlength="6" required>
        </div>
        <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center;">Зарегистрироваться</button>
        <p class="form-note">Уже есть аккаунт? <a href="/login.php" style="border-bottom:1px solid var(--gold);">Войти</a></p>
      </form>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
