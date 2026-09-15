<?php
require_once __DIR__ . '/includes/config.php';
$posts = read_json('posts.json', []);
usort($posts, fn($a, $b) => strcmp($b['date'], $a['date']));
$courses = read_json('courses.json', []);
$courseById = [];
foreach ($courses as $c) { $courseById[$c['id']] = $c; }

$page_title = 'Блог';
$active = 'blog';
require __DIR__ . '/includes/header.php';
render_breadcrumbs([['label' => 'Блог']]);
?>

<div class="page-header">
  <div class="hero-ledger"></div>
  <div class="container">
    <div class="kicker">Блог</div>
    <h1>Полезное о бухучёте и 1С</h1>
  </div>
</div>

<section>
  <div class="container">
    <div class="blog-full-grid">
      <?php foreach ($posts as $p): $course = $courseById[$p['course_id']] ?? null; ?>
      <article class="blog-full-card" id="<?= h($p['id']) ?>">
        <div class="blog-date"><?= h(date('d.m.Y', strtotime($p['date']))) ?></div>
        <h2 style="font-size:21px;"><?= h($p['title']) ?></h2>
        <p><?= h($p['excerpt']) ?></p>
        <?php if ($course): ?>
          <a href="/courses.php#<?= h($course['id']) ?>" class="details" style="font-size:14px;font-weight:600;border-bottom:1px solid var(--gold);">Изучить на курсе «<?= h($course['title']) ?>» →</a>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-band">
  <div class="container">
    <h2>Хотите разбираться в этом на практике?</h2>
    <a href="/courses.php" class="btn btn-gold">Смотреть курсы</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
