<?php
require_once __DIR__ . '/includes/auth.php';
if (current_user()) redirect('account.php');

$error = '';
$next = $_GET['next'] ?? 'account.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = login_user(trim($_POST['login'] ?? ''), $_POST['password'] ?? '');
    if ($result['ok']) {
        redirect($_POST['next'] ?: 'account.php');
    } else {
        $error = $result['error'];
    }
}

$page_title = 'Вход';
$active = 'account';
require __DIR__ . '/includes/header.php';
render_breadcrumbs([['label' => 'Вход']]);
?>

<div class="page-header">
  <div class="hero-ledger"></div>
  <div class="container">
    <div class="kicker">Личный кабинет</div>
    <h1>Вход</h1>
  </div>
</div>

<section>
  <div class="container" style="max-width:480px;">
    <div class="form-card" style="max-width:none;">
      <?php if ($error): ?><p style="color:#8B2E2E;font-size:14.5px;"><?= h($error) ?></p><?php endif; ?>
      <form method="post">
        <input type="hidden" name="next" value="<?= h($next) ?>">
        <div class="form-row">
          <label for="login">Логин</label>
          <input type="text" id="login" name="login" required>
        </div>
        <div class="form-row">
          <label for="password">Пароль</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center;">Войти</button>
        <p class="form-note">Нет аккаунта? <a href="/register.php" style="border-bottom:1px solid var(--gold);">Зарегистрироваться</a></p>
      </form>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
