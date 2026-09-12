<?php
require_once __DIR__ . '/includes/config.php';
$courses = read_json('courses.json', []);
$featured = array_slice($courses, 0, 3);
$posts = read_json('posts.json', []);
usort($posts, fn($a, $b) => strcmp($b['date'], $a['date']));
$latestPosts = array_slice($posts, 0, 3);

$page_title = 'Курсы бухгалтерии и 1С';
$active = 'home';
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="hero-ledger"></div>
  <div class="container">
    <div>
      <div class="hero-eyebrow">Учебный центр «Ishonchli Buxgalter» · Фергана</div>
      <h1>Профессия бухгалтера, которая всегда нужна бизнесу</h1>
      <p class="lead">Учим вести учёт и работать в 1С так, как это устроено в реальных организациях Узбекистана — от первички до сдачи отчётности.</p>
      <div class="hero-ctas">
        <a href="/courses.php" class="btn btn-gold">Смотреть курсы</a>
        <a href="/contacts.php" class="btn btn-ghost-light">Записаться на консультацию</a>
      </div>
    </div>
    <div class="ledger-card">
      <table>
        <thead><tr><th>Курс</th><th style="text-align:right">Старт</th></tr></thead>
        <tbody>
          <tr><td>1С:Бухгалтерия 8 — с нуля</td><td class="num">5 окт.</td></tr>
          <tr><td>Налоговая отчётность РУз</td><td class="num">12 окт.</td></tr>
          <tr><td>1С:Зарплата и кадры</td><td class="num">19 окт.</td></tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="container">
    <div class="stat-row">
      <div class="stat"><b>7 лет</b><span>обучаем бухучёту и 1С</span></div>
      <div class="stat"><b>430+</b><span>выпускников курсов</span></div>
      <div class="stat"><b>6</b><span>программ обучения</span></div>
      <div class="stat"><b>1С®</b><span>официальный партнёр-франчайзи</span></div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head">
      <div class="kicker">Почему у нас</div>
      <h2>Учёба, после которой можно сразу работать</h2>
    </div>
    <div class="why-grid">
      <div class="why-item">
        <h3>Преподают практики</h3>
        <p>Ведём бухгалтерию действующих компаний параллельно с обучением — на занятиях реальные кейсы, а не только теория.</p>
      </div>
      <div class="why-item">
        <h3>Официальный партнёр 1С</h3>
        <p>Работаем в лицензионной 1С:Бухгалтерии для Узбекистана — той же версии, что стоит у работодателей.</p>
      </div>
      <div class="why-item">
        <h3>Законодательство РУз</h3>
        <p>Разбираем актуальные формы отчётности, налоги и требования именно узбекистанского учёта.</p>
      </div>
      <div class="why-item">
        <h3>Поддержка после курса</h3>
        <p>Отвечаем на вопросы выпускников и помогаем с первыми самостоятельными отчётами.</p>
      </div>
    </div>
  </div>
</section>

<section style="padding-top:0;">
  <div class="container">
    <div class="section-head">
      <div class="kicker">Программы</div>
      <h2>Популярные курсы</h2>
    </div>
    <div class="course-grid">
      <?php foreach ($featured as $c): ?>
      <div class="course-card">
        <div class="course-tag"><?= h($c['tag']) ?></div>
        <h3><?= h($c['title']) ?></h3>
        <p><?= h($c['summary']) ?></p>
        <div class="course-meta">
          <span class="course-price"><?= h($c['price']) ?></span>
          <span class="course-dur"><?= h($c['duration']) ?></span>
        </div>
        <a href="/courses.php#<?= h($c['id']) ?>" class="details">Подробнее о курсе</a>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:32px;">
      <a href="/courses.php" class="btn btn-ghost">Все курсы и цены</a>
    </div>
  </div>
</section>

<section style="background:var(--paper);border-top:1px solid var(--line);border-bottom:1px solid var(--line);">
  <div class="container">
    <div class="section-head">
      <div class="kicker">Как проходит обучение</div>
      <h2>Четыре шага от заявки до диплома</h2>
    </div>
    <div class="steps">
      <div class="step"><em>01</em><h3>Заявка</h3><p>Регистрируетесь на сайте и оставляете заявку — подбираем курс под вашу цель.</p></div>
      <div class="step"><em>02</em><h3>Обучение</h3><p>Очные занятия в группе до 10 человек, практика в 1С на каждом уроке.</p></div>
      <div class="step"><em>03</em><h3>Практика</h3><p>Разбираем документы и отчёты на примерах реальных организаций.</p></div>
      <div class="step"><em>04</em><h3>Сертификат</h3><p>Выдаём сертификат и помогаем с трудоустройством выпускникам.</p></div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head">
      <div class="kicker">Отзывы</div>
      <h2>Что говорят выпускники</h2>
    </div>
    <div class="testi-grid">
      <div class="testi">
        <p>«После курса „с нуля“ устроилась помощником бухгалтера — уверенно работаю в 1С с первого дня».</p>
        <div class="who">Мадина Р. <span>выпускница, курс для начинающих</span></div>
      </div>
      <div class="testi">
        <p>«Веду учёт своего ИП сама, курс для собственников закрыл все вопросы, которые я боялась задавать бухгалтеру».</p>
        <div class="who">Шерзод А. <span>владелец малого бизнеса</span></div>
      </div>
      <div class="testi">
        <p>«Отчётность по зарплате и налогам теперь не вызывает паники — разобрали каждую форму на реальных примерах».</p>
        <div class="who">Дилноза К. <span>главный бухгалтер</span></div>
      </div>
    </div>
  </div>
</section>

<section style="background:var(--paper);border-top:1px solid var(--line);padding-bottom:64px;">
  <div class="container">
    <div class="section-head">
      <div class="kicker">Из блога</div>
      <h2>Полезное о бухучёте</h2>
    </div>
    <div class="blog-grid">
      <?php foreach ($latestPosts as $p): ?>
      <a class="blog-card" href="/blog.php#<?= h($p['id']) ?>">
        <div class="blog-date"><?= h(date('d.m.Y', strtotime($p['date']))) ?></div>
        <h3><?= h($p['title']) ?></h3>
        <p><?= h($p['excerpt']) ?></p>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-band">
  <div class="container">
    <h2>Готовы освоить бухучёт и 1С?</h2>
    <a href="/contacts.php" class="btn btn-gold">Записаться на курс</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
