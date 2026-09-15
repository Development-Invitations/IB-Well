<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

$allLeads = read_json('leads.json', []);
$myLeads = array_values(array_filter($allLeads, fn($l) => $l['user_id'] === $user['id']));
usort($myLeads, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

$statusLabels = ['new' => 'Новая', 'contacted' => 'Связались', 'enrolled' => 'Записан(а)'];

$page_title = 'Личный кабинет';
$active = 'account';
require __DIR__ . '/includes/header.php';
render_breadcrumbs([['label' => 'Личный кабинет']]);
?>

<div class="page-header">
  <div class="hero-ledger"></div>
  <div class="container">
    <div class="kicker">Личный кабинет</div>
    <h1>Здравствуйте, <?= h($user['name']) ?></h1>
  </div>
</div>

<section>
  <div class="container">
    <div class="section-head" style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;">
      <div>
        <h2 style="font-size:24px;">Мои заявки</h2>
        <p class="muted" style="margin:0;">Телефон для связи: <?= h($user['phone']) ?></p>
      </div>
      <div style="display:flex;gap:12px;">
        <a href="/contacts.php" class="btn btn-gold">Оставить новую заявку</a>
        <a href="/logout.php" class="btn btn-ghost">Выйти</a>
      </div>
    </div>

    <?php if (!$myLeads): ?>
      <p class="muted">Заявок пока нет. Выберите курс на странице «Курсы» и оставьте заявку.</p>
    <?php else: ?>
      <table class="price-table">
        <thead><tr><th>Дата заявки</th><th>Тип</th><th>Курс</th><th>Статус</th><th>Начало</th><th>Окончание</th></tr></thead>
        <tbody>
        <?php foreach ($myLeads as $l): ?>
          <tr>
            <td><?= h(date('d.m.Y', strtotime($l['created_at']))) ?></td>
            <td><?= $l['type'] === 'consultation' ? 'Консультация' : 'Курс' ?></td>
            <td><?= h($l['course'] ?: '—') ?></td>
            <td><?= h($statusLabels[$l['status']] ?? $l['status']) ?></td>
            <td><?= !empty($l['start_date']) ? h(date('d.m.Y', strtotime($l['start_date']))) : '—' ?></td>
            <td><?= !empty($l['end_date']) ? h(date('d.m.Y', strtotime($l['end_date']))) : '—' ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <p class="muted" style="font-size:13px;margin-top:14px;">Даты начала и окончания появляются после того, как менеджер подтвердит зачисление на курс.</p>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
