<?php
require_once __DIR__ . '/includes/auth.php';
$user = current_user();
$courses = read_json('courses.json', []);
$sent = isset($_GET['sent']);

$page_title = 'Контакты';
$active = 'contacts';
require __DIR__ . '/includes/header.php';
render_breadcrumbs([['label' => 'Контакты']]);
?>

<div class="page-header">
  <div class="hero-ledger"></div>
  <div class="container">
    <div class="kicker">Контакты</div>
    <h1>Оставьте заявку — подберём курс и группу</h1>
  </div>
</div>

<section>
  <div class="container two-col">
    <div>
      <h2 style="font-size:24px;">Заявка на курс или консультацию</h2>
      <p class="muted" style="margin-bottom:28px;">Ответим в течение рабочего дня и расскажем про ближайший старт групп.</p>

      <?php if ($sent): ?>
        <div class="form-card" style="max-width:none;">
          <div class="form-success" style="display:block;">Спасибо! Заявка отправлена — мы свяжемся с вами в течение рабочего дня.</div>
          <a href="/account.php" class="btn btn-ghost" style="margin-top:18px;">Посмотреть мои заявки</a>
        </div>

      <?php elseif (!$user): ?>
        <div class="form-card" style="max-width:none;">
          <p>Чтобы оставить заявку, войдите в личный кабинет или зарегистрируйтесь — это займёт минуту.</p>
          <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="/login.php?next=%2Fcontacts.php" class="btn btn-gold">Войти</a>
            <a href="/register.php" class="btn btn-ghost">Зарегистрироваться</a>
          </div>
        </div>

      <?php else: ?>
        <div class="form-card" style="max-width:none;">
          <form method="post" action="/apply.php" data-lead-form>
            <div class="form-row">
              <label>Имя</label>
              <input type="text" value="<?= h($user['name']) ?>" disabled>
            </div>
            <div class="form-row">
              <label>Телефон</label>
              <input type="text" value="<?= h($user['phone']) ?>" disabled>
            </div>
            <div class="form-row">
              <label for="course">Интересующий курс</label>
              <select id="course" name="course">
                <?php foreach ($courses as $c): ?>
                  <option><?= h($c['title']) ?></option>
                <?php endforeach; ?>
                <option>Ещё не решил(а), нужна консультация</option>
              </select>
            </div>
            <div class="form-row">
              <label for="message">Комментарий (необязательно)</label>
              <textarea id="message" name="message" rows="3" placeholder="Удобное время, вопросы по курсу и т.д."></textarea>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center;">Отправить заявку</button>
            <p class="form-note">Заявка появится в вашем личном кабинете и попадёт нашему менеджеру в Telegram.</p>
          </form>
        </div>
      <?php endif; ?>
    </div>

    <div>
      <h2 style="font-size:24px;">Как нас найти</h2>
      <div class="contact-info-item">
        <div class="ico">📍</div>
        <div><h4>Адрес</h4><p>г. Фергана, ул. Мустакиллик, 24 (рядом с ЦУМ)</p></div>
      </div>
      <div class="contact-info-item">
        <div class="ico">📞</div>
        <div><h4>Телефон</h4><p>+998 99 918 00 10</p></div>
      </div>
      <div class="contact-info-item">
        <div class="ico">✉️</div>
        <div><h4>Email</h4><p>info@ishonchli-buxgalter.uz</p></div>
      </div>
      <div class="contact-info-item">
        <div class="ico">🕒</div>
        <div><h4>Часы работы</h4><p>Пн–Сб, 9:00–18:00</p></div>
      </div>
      <div class="contact-info-item">
        <div class="ico">💬</div>
        <div><h4>Telegram</h4><p>@ishonchli_buxgalter</p></div>
      </div>

      <div class="map-frame" style="margin-top:24px;">Карта — г. Фергана, ул. Мустакиллик, 24</div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
