<?php
require_once dirname(__DIR__) . '/includes/config.php';
$admin_title = 'Блог';
$admin_active = 'posts';

$posts = read_json('posts.json', []);
$courses = read_json('courses.json', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $posts = array_values(array_filter($posts, fn($p) => $p['id'] !== $_POST['id']));
        write_json('posts.json', $posts);
        redirect('posts.php');
    }

    if ($action === 'save') {
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'date' => $_POST['date'] ?: date('Y-m-d'),
            'course_id' => $_POST['course_id'] ?? '',
        ];
        $id = $_POST['id'] ?? '';
        $found = false;
        foreach ($posts as &$p) {
            if ($p['id'] === $id) { $p = array_merge($p, $data); $found = true; }
        }
        unset($p);
        if (!$found) {
            $data['id'] = $id !== '' ? $id : uid('p_');
            $posts[] = $data;
        }
        write_json('posts.json', $posts);
        redirect('posts.php');
    }
}

require __DIR__ . '/includes/admin_header.php';
$editId = $_GET['edit'] ?? null;
$editPost = null;
foreach ($posts as $p) { if ($p['id'] === $editId) $editPost = $p; }
usort($posts, fn($a, $b) => strcmp($b['date'], $a['date']));
?>

<div class="admin-topbar"><h1>Блог</h1></div>

<div class="admin-panel">
  <h2><?= $editPost ? 'Редактировать статью' : 'Новая статья' ?></h2>
  <form method="post">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= h($editPost['id'] ?? '') ?>">
    <div class="form-row"><label>Заголовок</label><input type="text" name="title" value="<?= h($editPost['title'] ?? '') ?>" required></div>
    <div class="two-col" style="gap:20px;">
      <div class="form-row"><label>Дата публикации</label><input type="date" name="date" value="<?= h($editPost['date'] ?? date('Y-m-d')) ?>"></div>
      <div class="form-row">
        <label>Связанный курс (необязательно)</label>
        <select name="course_id">
          <option value="">— без привязки —</option>
          <?php foreach ($courses as $c): ?>
            <option value="<?= h($c['id']) ?>" <?= ($editPost['course_id'] ?? '') === $c['id'] ? 'selected' : '' ?>><?= h($c['title']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-row"><label>Текст (краткое содержание)</label><textarea name="excerpt" rows="5" required><?= h($editPost['excerpt'] ?? '') ?></textarea></div>
    <button type="submit" class="btn btn-gold"><?= $editPost ? 'Сохранить' : 'Опубликовать' ?></button>
    <?php if ($editPost): ?><a href="posts.php" class="btn btn-ghost">Отмена</a><?php endif; ?>
  </form>
</div>

<div class="admin-panel">
  <h2>Все статьи (<?= count($posts) ?>)</h2>
  <table class="admin-table">
    <thead><tr><th>Дата</th><th>Заголовок</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($posts as $p): ?>
      <tr>
        <td><?= h(date('d.m.Y', strtotime($p['date']))) ?></td>
        <td><?= h($p['title']) ?></td>
        <td class="admin-actions">
          <a class="btn btn-ghost" href="?edit=<?= h($p['id']) ?>">Изменить</a>
          <form method="post" onsubmit="return confirm('Удалить статью?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= h($p['id']) ?>">
            <button type="submit" class="btn btn-ghost" style="border-color:#8B2E2E;color:#8B2E2E;">Удалить</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
