document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.main-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('open');
      toggle.textContent = nav.classList.contains('open') ? '✕' : '☰';
    });
  }

  document.querySelectorAll('.faq-item').forEach(function (item) {
    var q = item.querySelector('.faq-q');
    q.addEventListener('click', function () {
      var wasOpen = item.classList.contains('open');
      document.querySelectorAll('.faq-item').forEach(function (i) { i.classList.remove('open'); });
      if (!wasOpen) item.classList.add('open');
    });
  });

  // Маска телефона: +998 XX XXX XX XX
  document.querySelectorAll('[data-phone-mask]').forEach(function (input) {
    function formatUzPhone(raw) {
      var digits = raw.replace(/\D/g, '');
      if (digits.indexOf('998') === 0) digits = digits.slice(3);
      digits = digits.slice(0, 9);
      var out = '+998';
      if (digits.length > 0) out += ' ' + digits.slice(0, 2);
      if (digits.length > 2) out += ' ' + digits.slice(2, 5);
      if (digits.length > 5) out += ' ' + digits.slice(5, 7);
      if (digits.length > 7) out += ' ' + digits.slice(7, 9);
      return out;
    }
    input.addEventListener('focus', function () {
      if (!input.value) input.value = '+998 ';
    });
    input.addEventListener('input', function () {
      input.value = formatUzPhone(input.value);
    });
    input.addEventListener('keydown', function (e) {
      // не даём стереть "+998 " backspace'ом за один символ до пустоты
      if (e.key === 'Backspace' && input.value.length <= 5) {
        e.preventDefault();
        input.value = '';
      }
    });
  });
});

// Floating scroll-top + contact widget
(function () {
  var scrollBtn = document.getElementById('floatScrollTop');
  var contactToggle = document.getElementById('floatContactToggle');
  var widget = document.getElementById('floatWidget');
  if (!scrollBtn || !contactToggle || !widget) return;

  window.addEventListener('scroll', function () {
    scrollBtn.classList.toggle('visible', window.scrollY > 400);
  });

  scrollBtn.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  contactToggle.addEventListener('click', function () {
    var isOpen = widget.classList.toggle('open');
    contactToggle.classList.toggle('open', isOpen);
  });

  document.addEventListener('click', function (e) {
    if (!widget.contains(e.target)) {
      widget.classList.remove('open');
      contactToggle.classList.remove('open');
    }
  });
})();
