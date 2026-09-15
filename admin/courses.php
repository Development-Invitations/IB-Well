<?php
require_once dirname(__DIR__) . '/includes/config.php';
$admin_title = 'Курсы';
$admin_active = 'courses';

$courses = read_json('courses.json', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $courses = array_values(array_filter($courses, fn($c) => $c['id'] !== $_POST['id']));
        write_json('courses.json', $courses);
        redirect('courses.php');
    }

    if ($action === 'save') {
        $data = [
            'tag' => trim($_POST['tag'] ?? ''),
            'title' => trim($_POST['title'] ?? ''),
            'summary' => trim($_POST['summary'] ?? ''),
            'duration' => trim($_POST['duration'] ?? ''),
            'duration_note' => trim($_POST['duration_note'] ?? ''),
            'price' => trim($_POST['price'] ?? ''),
            'format' => trim($_POST['format'] ?? ''),
        ];
        $id = $_POST['id'] ?? '';
        $found = false;
        foreach ($courses as &$c) {
            if ($c['id'] === $id) { $c = array_merge($c, $data); $found = true; }
        }
        unset($c);
        if (!$found) {
            $data['id'] = $id !== '' ? $id : uid('c_');
            $courses[] = $data;
        }
        write_json('courses.json', $courses);
        redirect('courses.php');
    }
}

require __DIR__ . '/includes/admin_header.php';
$editId = $_GET['edit'] ?? null;
$editCourse = null;
foreach ($courses as $c) { if ($c['id'] === $editId) $editCourse = $c; }
?>

<div class="admin-topbar"><h1>Курсы</h1></div>

<div class="admin-panel">
  <h2><?= $editCourse ? 'Редактировать курс' : 'Добавить курс' ?></h2>
  <form method="post">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= h($editCourse['id'] ?? '') ?>">
    <div class="two-col" style="gap:20px;">
      <div>
        <div class="form-row"><label>Название курса</label><input type="text" name="title" value="<?= h($editCourse['title'] ?? '') ?>" required></div>
        <div class="form-row"><label>Метка (например «Для начинающих»)</label><input type="text" name="tag" value="<?= h($editCourse['tag'] ?? '') ?>"></div>
        <div class="form-row"><label>Длительность (напр. «5 месяцев»)</label><input type="text" name="duration" value="<?= h($editCourse['duration'] ?? '') ?>" required></div>
        <div class="form-row"><label>Пояснение к длительности (необязательно)</label><input type="text" name="duration_note" placeholder="напр. 3 мес. бухгалтерия + 2 мес. 1С" value="<?= h($editCourse['duration_note'] ?? '') ?>"></div>
      </div>
      <div>
        <div class="form-row"><label>Цена</label><input type="text" name="price" value="<?= h($editCourse['price'] ?? '') ?>" required></div>
        <div class="form-row"><label>Формат</label><input type="text" name="format" value="<?= h($editCourse['format'] ?? 'Очно, группа') ?>"></div>
        <div class="form-row"><label>Описание</label><textarea name="summary" rows="4"><?= h($editCourse['summary'] ?? '') ?></textarea></div>
      </div>
    </div>
    <button type="submit" class="btn btn-gold"><?= $editCourse ? 'Сохранить изменения' : 'Добавить курс' ?></button>
    <?php if ($editCourse): ?><a href="courses.php" class="btn btn-ghost">Отмена</a><?php endif; ?>
  </form>
</div>

<div class="admin-panel">
  <h2>Все курсы (<?= count($courses) ?>)</h2>
  <table class="admin-table">
    <thead><tr><th>Название</th><th>Длительность</th><th>Цена</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($courses as $c): ?>
      <tr>
        <td><?= h($c['title']) ?><div class="muted" style="font-size:12.5px;"><?= h($c['tag']) ?></div></td>
        <td><?= h($c['duration']) ?><?= $c['duration_note'] ? '<div class="muted" style="font-size:12.5px;">' . h($c['duration_note']) . '</div>' : '' ?></td>
        <td><?= h($c['price']) ?></td>
        <td class="admin-actions">
          <a class="btn btn-ghost" href="?edit=<?= h($c['id']) ?>">Изменить</a>
          <form method="post" onsubmit="return confirm('Удалить курс «<?= h(addslashes($c['title'])) ?>»?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= h($c['id']) ?>">
            <button type="submit" class="btn btn-ghost" style="border-color:#8B2E2E;color:#8B2E2E;">Удалить</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
