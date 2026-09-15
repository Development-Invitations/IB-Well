<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/telegram.php';
$admin_title = 'Настройки';
$admin_active = 'settings';

$settings = read_json('settings.json', [
    'course_bot' => ['token' => '', 'chat_id' => '', 'label' => 'Бот заявок на курсы'],
    'consult_bot' => ['token' => '', 'chat_id' => '', 'label' => 'Бот заявок на консультацию'],
]);

$notice = '';
$testResult = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_bots') {
        $settings['course_bot'] = [
            'token' => trim($_POST['course_token'] ?? ''),
            'chat_id' => trim($_POST['course_chat_id'] ?? ''),
            'label' => 'Бот заявок на курсы',
        ];
        $settings['consult_bot'] = [
            'token' => trim($_POST['consult_token'] ?? ''),
            'chat_id' => trim($_POST['consult_chat_id'] ?? ''),
            'label' => 'Бот заявок на консультацию',
        ];
        write_json('settings.json', $settings);
        $notice = 'Настройки ботов сохранены.';
    }

    if ($action === 'test_bot') {
        $bot = $settings[$_POST['bot_key']] ?? null;
        if ($bot) {
            $testResult = tg_send($bot['token'], $bot['chat_id'], 'Тестовое сообщение от сайта Ishonchli Buxgalter ✅');
        }
    }

    if ($action === 'change_password') {
        $admin = read_json('admin.json', []);
        if (!password_verify($_POST['current_password'] ?? '', $admin['password_hash'] ?? '')) {
            $notice = 'Текущий пароль неверен.';
        } elseif (strlen($_POST['new_password'] ?? '') < 6) {
            $notice = 'Новый пароль должен быть не короче 6 символов.';
        } else {
            $admin['password_hash'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            write_json('admin.json', $admin);
            $notice = 'Пароль администратора изменён.';
        }
    }
}

require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar"><h1>Настройки</h1></div>

<?php if ($notice): ?><div class="admin-panel" style="border-left:3px solid var(--gold);"><?= h($notice) ?></div><?php endif; ?>

<div class="admin-panel">
  <h2>Telegram-боты для заявок</h2>
  <p class="muted">Заведите двух ботов через @BotFather, вставьте их токены сюда, укажите chat_id чата/группы,
  куда должны приходить уведомления. Первый бот получает заявки на конкретные курсы, второй — заявки на консультацию
  (выбор «Ещё не решил(а), нужна консультация» на странице «Контакты»).</p>
  <form method="post">
    <input type="hidden" name="action" value="save_bots">
    <div class="two-col" style="gap:24px;">
      <div>
        <h3 style="font-size:15px;">Бот заявок на курсы</h3>
        <div class="form-row"><label>Token</label><input type="text" name="course_token" value="<?= h($settings['course_bot']['token']) ?>" placeholder="123456:ABC-DEF..."></div>
        <div class="form-row"><label>Chat ID</label><input type="text" name="course_chat_id" value="<?= h($settings['course_bot']['chat_id']) ?>" placeholder="-1001234567890"></div>
      </div>
      <div>
        <h3 style="font-size:15px;">Бот заявок на консультацию</h3>
        <div class="form-row"><label>Token</label><input type="text" name="consult_token" value="<?= h($settings['consult_bot']['token']) ?>" placeholder="123456:ABC-DEF..."></div>
        <div class="form-row"><label>Chat ID</label><input type="text" name="consult_chat_id" value="<?= h($settings['consult_bot']['chat_id']) ?>" placeholder="-1001234567890"></div>
      </div>
    </div>
    <button type="submit" class="btn btn-gold">Сохранить настройки ботов</button>
  </form>

  <div style="display:flex;gap:12px;margin-top:18px;">
    <form method="post"><input type="hidden" name="action" value="test_bot"><input type="hidden" name="bot_key" value="course_bot">
      <button type="submit" class="btn btn-ghost">Тест: бот курсов</button>
    </form>
    <form method="post"><input type="hidden" name="action" value="test_bot"><input type="hidden" name="bot_key" value="consult_bot">
      <button type="submit" class="btn btn-ghost">Тест: бот консультаций</button>
    </form>
  </div>
  <?php if ($testResult): ?>
    <p style="margin-top:12px;font-size:13.5px;" class="<?= $testResult['ok'] ? '' : 'muted' ?>">
      <?= $testResult['ok'] ? '✅ Сообщение отправлено.' : '⚠️ Не отправлено: ' . h($testResult['error'] ?? 'проверьте token/chat_id') ?>
    </p>
  <?php endif; ?>
</div>

<div class="admin-panel">
  <h2>Смена пароля администратора</h2>
  <form method="post" style="max-width:420px;">
    <input type="hidden" name="action" value="change_password">
    <div class="form-row"><label>Текущий пароль</label><input type="password" name="current_password" required></div>
    <div class="form-row"><label>Новый пароль</label><input type="password" name="new_password" minlength="6" required></div>
    <button type="submit" class="btn btn-gold">Изменить пароль</button>
  </form>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
