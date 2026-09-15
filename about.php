<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'О нас';
$active = 'about';
require __DIR__ . '/includes/header.php';
render_breadcrumbs([['label' => 'О нас']]);
?>

<div class="page-header">
  <div class="hero-ledger"></div>
  <div class="container">
    <div class="kicker">Учебный центр</div>
    <h1>Ishonchli Buxgalter — надёжный партнёр в мире учёта</h1>
  </div>
</div>

<section>
  <div class="container two-col">
    <div>
      <h2 style="font-size:26px;">С 2018 года — рядом с бизнесом Ферганы</h2>
      <p>Мы начинали как 1С-франчайзи: внедряли и сопровождали бухучёт для организаций региона. За годы работы накопили сотни реальных кейсов — и в какой-то момент поняли, что этим опытом стоит делиться.</p>
      <p>Так появился учебный центр Ishonchli Buxgalter: курсы ведут не преподаватели «по учебнику», а практикующие специалисты, которые каждый день закрывают отчётность настоящих компаний.</p>
      <p>Сегодня мы совмещаем две задачи — сопровождаем бизнес по 1С и обучаем новых бухгалтеров, которым доверяют этот учёт.</p>
    </div>
    <div class="ledger-card" style="border-top:4px solid var(--gold);">
      <table>
        <thead><tr><th>Показатель</th><th style="text-align:right">Значение</th></tr></thead>
        <tbody>
          <tr><td>Работаем на рынке</td><td class="num">с 2018 года</td></tr>
          <tr><td>Выпускников курсов</td><td class="num">430+</td></tr>
          <tr><td>Программ обучения</td><td class="num">6</td></tr>
          <tr><td>Статус</td><td class="num">Официальный партнёр 1С</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<section style="background:var(--paper);border-top:1px solid var(--line);">
  <div class="container">
    <div class="section-head">
      <div class="kicker">Преподаватели</div>
      <h2>Учат те, кто ведёт учёт каждый день</h2>
    </div>
    <div class="team-grid">
      <div class="team-card">
        <div class="team-photo">Н.А.</div>
        <div class="info">
          <h3>Нилуфар Абдуллаева</h3>
          <div class="role">Главный бухгалтер, преподаватель курса «с нуля»</div>
          <p>12 лет в бухучёте, ведёт отчётность нескольких компаний Ферганы. В курсе — на реальных первичных документах.</p>
        </div>
      </div>
      <div class="team-card">
        <div class="team-photo">Ж.Т.</div>
        <div class="info">
          <h3>Жасур Турсунов</h3>
          <div class="role">1С-специалист, преподаватель по налоговой отчётности</div>
          <p>Настраивает и внедряет 1С:Бухгалтерию для организаций региона, знает отчётность РУз изнутри.</p>
        </div>
      </div>
      <div class="team-card">
        <div class="team-photo">Д.К.</div>
        <div class="info">
          <h3>Дилором Каримова</h3>
          <div class="role">Специалист по кадрам и зарплате</div>
          <p>Ведёт курс «1С:Зарплата и кадры», на практике объясняет расчёт зарплаты и кадровый документооборот.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head">
      <div class="kicker">Наши принципы</div>
      <h2>Чем руководствуемся в работе</h2>
    </div>
    <div class="why-grid">
      <div class="why-item"><h3>Практика прежде теории</h3><p>Каждая тема закрепляется в 1С на реальном примере, а не только на слайдах.</p></div>
      <div class="why-item"><h3>Актуальность</h3><p>Следим за изменениями в законодательстве РУз и обновляем программу курсов.</p></div>
      <div class="why-item"><h3>Небольшие группы</h3><p>До 10 человек — у каждого студента достаточно внимания преподавателя.</p></div>
      <div class="why-item"><h3>Ответственность</h3><p>Мы сами ведём бухучёт компаний — и учим так, как хотели бы, чтобы учили нас.</p></div>
    </div>
  </div>
</section>

<section class="cta-band">
  <div class="container">
    <h2>Познакомиться с преподавателями лично?</h2>
    <a href="/contacts.php" class="btn btn-gold">Записаться на консультацию</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
