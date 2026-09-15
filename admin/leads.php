<?php
require_once dirname(__DIR__) . '/includes/config.php';
$admin_title = 'Заявки';
$admin_active = 'leads';

$leads = read_json('leads.json', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lead_id'])) {
    foreach ($leads as &$l) {
        if ($l['id'] === $_POST['lead_id']) {
            $l['status'] = $_POST['status'];
            $l['start_date'] = trim($_POST['start_date'] ?? '');
            $l['end_date'] = trim($_POST['end_date'] ?? '');
        }
    }
    unset($l);
    write_json('leads.json', $leads);
    redirect('leads.php' . (isset($_GET['filter']) ? '?filter=' . urlencode($_GET['filter']) : ''));
}

require __DIR__ . '/includes/admin_header.php';

usort($leads, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
$filter = $_GET['filter'] ?? 'all';
if ($filter === 'course') $leads = array_filter($leads, fn($l) => $l['type'] === 'course');
if ($filter === 'consultation') $leads = array_filter($leads, fn($l) => $l['type'] === 'consultation');
if ($filter === 'new') $leads = array_filter($leads, fn($l) => $l['status'] === 'new');

$statusLabels = ['new' => 'Новая', 'contacted' => 'Связались', 'enrolled' => 'Записан(а)'];
$badgeClass = ['new' => 'badge-new', 'contacted' => 'badge-contacted', 'enrolled' => 'badge-enrolled'];
?>

<div class="admin-topbar">
  <h1>Заявки</h1>
  <div class="admin-actions">
    <a class="btn <?= $filter==='all'?'btn-primary':'btn-ghost' ?>" href="?filter=all">Все</a>
    <a class="btn <?= $filter==='new'?'btn-primary':'btn-ghost' ?>" href="?filter=new">Новые</a>
    <a class="btn <?= $filter==='course'?'btn-primary':'btn-ghost' ?>" href="?filter=course">На курс</a>
    <a class="btn <?= $filter==='consultation'?'btn-primary':'btn-ghost' ?>" href="?filter=consultation">Консультации</a>
  </div>
</div>

<div class="admin-panel">
  <?php foreach ($leads as $l): ?>
    <form id="lf_<?= h($l['id']) ?>" method="post" style="display:none;"></form>
  <?php endforeach; ?>
  <table class="admin-table">
    <thead>
      <tr><th>Дата</th><th>Имя</th><th>Телефон</th><th>Тип</th><th>Курс</th><th>Комментарий</th><th>Статус</th><th>Начало курса</th><th>Окончание курса</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($leads as $l): $fid = 'lf_' . h($l['id']); ?>
      <tr>
        <td><?= h(date('d.m.Y H:i', strtotime($l['created_at']))) ?></td>
        <td><?= h($l['name']) ?></td>
        <td><?= h($l['phone']) ?></td>
        <td><?= $l['type'] === 'consultation' ? 'Консультация' : 'Курс' ?></td>
        <td><?= h($l['course'] ?: '—') ?></td>
        <td style="max-width:200px;"><?= h($l['message'] ?: '—') ?></td>
        <td>
          <input type="hidden" form="<?= $fid ?>" name="lead_id" value="<?= h($l['id']) ?>">
          <span class="badge <?= $badgeClass[$l['status']] ?? '' ?>"><?= h($statusLabels[$l['status']] ?? $l['status']) ?></span><br>
          <select form="<?= $fid ?>" name="status" style="margin-top:6px;">
            <?php foreach ($statusLabels as $key => $label): ?>
              <option value="<?= h($key) ?>" <?= $l['status'] === $key ? 'selected' : '' ?>><?= h($label) ?></option>
            <?php endforeach; ?>
          </select>
        </td>
        <td><input form="<?= $fid ?>" type="date" name="start_date" value="<?= h($l['start_date'] ?? '') ?>" style="padding:6px 8px;font-size:13px;border:1px solid var(--line);background:var(--cream);"></td>
        <td><input form="<?= $fid ?>" type="date" name="end_date" value="<?= h($l['end_date'] ?? '') ?>" style="padding:6px 8px;font-size:13px;border:1px solid var(--line);background:var(--cream);"></td>
        <td><button form="<?= $fid ?>" type="submit" class="btn btn-ghost" style="padding:8px 14px;font-size:13px;">Сохранить</button></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$leads): ?><tr><td colspan="10" class="muted">Заявок нет.</td></tr><?php endif; ?>
    </tbody>
  </table>
  <p class="muted" style="font-size:12.5px;margin-top:14px;">При статусе «Записан(а)» укажите даты начала и окончания обучения — они появятся в личном кабинете пользователя.</p>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
