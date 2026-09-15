<?php
require_once dirname(__DIR__) . '/includes/config.php';
$admin_title = 'Пользователи';
$admin_active = 'users';
require __DIR__ . '/includes/admin_header.php';

$users = read_json('users.json', []);
$leads = read_json('leads.json', []);
usort($users, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

$leadCountByUser = [];
foreach ($leads as $l) {
    $leadCountByUser[$l['user_id']] = ($leadCountByUser[$l['user_id']] ?? 0) + 1;
}
?>

<div class="admin-topbar"><h1>Пользователи (<?= count($users) ?>)</h1></div>

<div class="admin-panel">
  <table class="admin-table">
    <thead><tr><th>Дата регистрации</th><th>Имя</th><th>Телефон</th><th>Логин</th><th>Заявок</th></tr></thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td><?= h(date('d.m.Y', strtotime($u['created_at']))) ?></td>
        <td><?= h($u['name']) ?></td>
        <td><?= h($u['phone']) ?></td>
        <td><?= h($u['login']) ?></td>
        <td><?= $leadCountByUser[$u['id']] ?? 0 ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$users): ?><tr><td colspan="5" class="muted">Пока нет зарегистрированных пользователей.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
