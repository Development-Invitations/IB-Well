<?php
require_once dirname(__DIR__) . '/includes/config.php';
$admin_title = 'Дашборд';
$admin_active = 'dashboard';
require __DIR__ . '/includes/admin_header.php';

$leads = read_json('leads.json', []);
$users = read_json('users.json', []);
$courses = read_json('courses.json', []);

$total = count($leads);
$today = count(array_filter($leads, fn($l) => substr($l['created_at'], 0, 10) === date('Y-m-d')));
$consultations = count(array_filter($leads, fn($l) => $l['type'] === 'consultation'));
$newStatus = count(array_filter($leads, fn($l) => $l['status'] === 'new'));

// По курсам
$byCourse = [];
foreach ($leads as $l) {
    $key = $l['course'] ?: '— не указан —';
    $byCourse[$key] = ($byCourse[$key] ?? 0) + 1;
}
arsort($byCourse);

// По дням, последние 14 дней
$byDay = [];
for ($i = 13; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i day"));
    $byDay[$d] = 0;
}
foreach ($leads as $l) {
    $d = substr($l['created_at'], 0, 10);
    if (isset($byDay[$d])) $byDay[$d]++;
}
$maxDay = max(1, max($byDay));
?>

<div class="admin-topbar">
  <h1>Дашборд</h1>
</div>

<div class="stat-cards">
  <div class="stat-card"><b><?= $total ?></b><span>заявок всего</span></div>
  <div class="stat-card"><b><?= $today ?></b><span>заявок сегодня</span></div>
  <div class="stat-card"><b><?= $newStatus ?></b><span>новых (не обработано)</span></div>
  <div class="stat-card"><b><?= count($users) ?></b><span>зарегистрировано пользователей</span></div>
</div>

<div class="admin-panel">
  <h2>Заявки за последние 14 дней</h2>
  <div style="display:flex;align-items:flex-end;gap:6px;height:120px;">
    <?php foreach ($byDay as $d => $count): ?>
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;">
        <div title="<?= h($d) ?>: <?= $count ?>" style="width:100%;background:var(--gold);height:<?= max(3, round($count / $maxDay * 90)) ?>px;"></div>
        <span style="font-size:10px;color:var(--muted);"><?= date('d.m', strtotime($d)) ?></span>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="admin-panel">
  <h2>Заявки по курсам</h2>
  <table class="admin-table">
    <thead><tr><th>Курс / тип</th><th>Заявок</th></tr></thead>
    <tbody>
      <?php foreach ($byCourse as $name => $count): ?>
        <tr><td><?= h($name) ?></td><td><?= $count ?></td></tr>
      <?php endforeach; ?>
      <?php if (!$byCourse): ?><tr><td colspan="2" class="muted">Заявок пока нет.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<div class="admin-panel">
  <h2>Заявки на консультацию vs на курс</h2>
  <p class="muted" style="margin:0;">Консультаций: <b><?= $consultations ?></b> · На конкретный курс: <b><?= $total - $consultations ?></b></p>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
