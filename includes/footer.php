<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="brand" style="margin-bottom:14px;">
          <span class="brand-mark"><img src="/images/logo-mark.png" alt="Ishonchli Buxgalter"></span>
          <span class="brand-name"><span class="brand-main">Ishonchli Buxgalter</span><span class="brand-sub">Academy</span></span>
        </div>
        <p>Учебный центр и 1С-франчайзи в Фергане. Обучаем бухучёту и 1С с 2018 года.</p>
      </div>
      <div>
        <h4>Навигация</h4>
        <a href="/courses.php">Курсы</a>
        <a href="/about.php">О нас</a>
        <a href="/blog.php">Блог</a>
        <a href="/contacts.php">Контакты</a>
      </div>
      <div>
        <h4>Аккаунт</h4>
        <a href="/register.php">Регистрация</a>
        <a href="/login.php">Войти</a>
        <a href="/account.php">Мои заявки</a>
      </div>
      <div>
        <h4>Контакты</h4>
        <a href="tel:+998999180010">+998 99 918 00 10</a>
        <a href="https://t.me/ishonchli_buxgalter">@ishonchli_buxgalter</a>
        <a href="/contacts.php">г. Фергана, Узбекистан</a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> Ishonchli Buxgalter Academy. Все права защищены.</span>
      <span>Фергана, Узбекистан · 1С Франчайзинг · IT Park резидент · <?= h(SITE_VERSION) ?></span>
    </div>
  </div>
</footer>
<!-- build: <?= h(SITE_VERSION) ?> / <?= h(BUILD_DATE) ?> -->

<div class="float-widget" id="floatWidget">
  <div class="float-channels">
    <a class="float-channel tg" href="https://t.me/ishonchli_buxgalter" target="_blank" rel="noopener" aria-label="Telegram">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
    </a>
    <a class="float-channel wa" href="https://wa.me/998999180010" target="_blank" rel="noopener" aria-label="WhatsApp">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5 8.4 8.4 0 0 1-4-1l-4.5 1 1-4.5a8.4 8.4 0 0 1-1-4A8.5 8.5 0 1 1 21 11.5Z"/><path d="M8.5 10c0 3.5 2 5.5 5.5 5.5"/></svg>
    </a>
    <a class="float-channel ig" href="https://instagram.com/ishonchli_buxgalter" target="_blank" rel="noopener" aria-label="Instagram">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1"/></svg>
    </a>
  </div>
  <button type="button" class="float-btn float-contact-toggle" id="floatContactToggle" aria-label="Связаться с нами">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 6 8 7 8-7"/></svg>
  </button>
  <button type="button" class="float-btn float-scrolltop" id="floatScrollTop" aria-label="Наверх">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
  </button>
</div>

<script src="/script.js"></script>
</body>
</html>
