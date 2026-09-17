const express = require('express');
const session = require('express-session');
const path = require('path');
const fs = require('fs');
const bodyParser = require('body-parser');

// Простая загрузка .env (без доп. зависимостей) — необязательна, но удобна
(function loadDotEnv() {
  const envPath = path.join(__dirname, '.env');
  if (!fs.existsSync(envPath)) return;
  fs.readFileSync(envPath, 'utf8').split('\n').forEach(line => {
    const m = line.match(/^\s*([\w.-]+)\s*=\s*(.*)?\s*$/);
    if (m && !process.env[m[1]]) process.env[m[1]] = (m[2] || '').trim();
  });
})();

const { readJson, writeJson, uid, filePath, DATA_DIR } = require('./lib/db');
const auth = require('./lib/auth');
const { tgSend, notifyNewLead, notifyNewRegistration } = require('./lib/telegram');

const app = express();
const PORT = process.env.PORT || 3000;

const SITE_NAME = 'Ishonchli Buxgalter Academy';
const SITE_VERSION = '1.3.0-node';
const BUILD_DATE = '2026-09-16';

const STATUS_LABELS = { new: 'Новая', contacted: 'Связались', enrolled: 'Записан(а)' };

// Значения по умолчанию — подстраховка на случай неполного/повреждённого
// content.json (например, после ручного редактирования файла).
const DEFAULT_CONTENT = {
  home: {
    hero_eyebrow: 'Учебный центр «Ishonchli Buxgalter» · Фергана',
    hero_title: 'Профессия бухгалтера, которая всегда нужна бизнесу',
    hero_lead: 'Учим вести учёт и работать в «1С:Предприятие 8, Бухгалтерия Узбекистана 3.0».',
    stat1_value: '7 лет', stat1_label: 'обучаем бухучёту и 1С',
    stat2_value: '430+', stat2_label: 'выпускников курсов',
    stat3_value: '6', stat3_label: 'программ обучения',
    stat4_value: '1С®', stat4_label: 'официальный партнёр-франчайзи',
    why1_title: 'Преподают практики', why1_text: '',
    why2_title: 'Официальный партнёр 1С', why2_text: '',
    why3_title: 'Законодательство РУз', why3_text: '',
    why4_title: 'Поддержка после курса', why4_text: '',
  },
  about: {
    hero_title: 'Ishonchli Buxgalter — надёжный партнёр в мире учёта',
    intro1: '', intro2: '', intro3: '',
    stat_since: 'с 2018 года', stat_grads: '430+', stat_programs: '6', stat_status: 'Официальный партнёр 1С',
  },
  contacts: {
    address: 'г. Фергана, ул. Мустакиллик, 24',
    phone: '+998 99 918 00 10',
    email: 'info@ishonchli-buxgalter.uz',
    hours: 'Пн–Сб, 9:00–18:00',
    telegram: '@ishonchli_buxgalter',
  },
  footer: { tagline: 'Учебный центр и 1С-франчайзи в Фергане.' },
};

function loadContent() {
  const saved = readJson('content.json', {});
  const merged = {};
  for (const section of Object.keys(DEFAULT_CONTENT)) {
    merged[section] = Object.assign({}, DEFAULT_CONTENT[section], saved[section] || {});
  }
  return merged;
}

// Express 4 не ловит ошибки из async-функций сам по себе — оборачиваем,
// чтобы любая ошибка (например, невозможность записать файл) долетала
// до общего обработчика ошибок, а не роняла процесс молча.
function asyncHandler(fn) {
  return function (req, res, next) {
    Promise.resolve(fn(req, res, next)).catch(next);
  };
}

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));
app.use(express.static(path.join(__dirname, 'public')));
app.use(bodyParser.urlencoded({ extended: true }));

app.use(session({
  secret: process.env.SESSION_SECRET || 'ishonchli-buxgalter-academy-change-me',
  resave: false,
  saveUninitialized: false,
  cookie: { maxAge: 30 * 24 * 60 * 60 * 1000 },
}));

// Общие данные для всех шаблонов
app.use((req, res, next) => {
  res.locals.siteName = SITE_NAME;
  res.locals.siteVersion = SITE_VERSION;
  res.locals.buildDate = BUILD_DATE;
  res.locals.currentUser = auth.currentUser(req);
  res.locals.content = loadContent();
  next();
});

// ==================== ПУБЛИЧНЫЙ САЙТ ====================

app.get('/', (req, res) => {
  const courses = readJson('courses.json', []);
  const posts = readJson('posts.json', []).sort((a, b) => b.date.localeCompare(a.date));
  res.render('index', { featured: courses.slice(0, 3), latestPosts: posts.slice(0, 3) });
});

app.get('/courses', (req, res) => {
  res.render('courses', { courses: readJson('courses.json', []) });
});

app.get('/about', (req, res) => res.render('about'));

app.get('/blog', (req, res) => {
  const posts = readJson('posts.json', []).sort((a, b) => b.date.localeCompare(a.date));
  const courses = readJson('courses.json', []);
  const coursesById = {};
  courses.forEach(c => { coursesById[c.id] = c; });
  res.render('blog', { posts, coursesById });
});

app.get('/contacts', (req, res) => {
  res.render('contacts', { courses: readJson('courses.json', []), sent: req.query.sent === '1' });
});

app.get('/register', (req, res) => {
  if (auth.currentUser(req)) return res.redirect('/account');
  res.render('register', { error: '', form: {} });
});

app.post('/register', asyncHandler(async (req, res) => {
  if (auth.currentUser(req)) return res.redirect('/account');
  const { name, phone, login, password } = req.body;
  let error = '';
  if (!name || !phone || !login || !password) {
    error = 'Заполните все поля.';
  } else if (!/^\+998 \d{2} \d{3} \d{2} \d{2}$/.test(phone)) {
    error = 'Телефон должен быть в формате +998 90 123 45 67.';
  } else if (password.length < 6) {
    error = 'Пароль должен быть не короче 6 символов.';
  } else {
    const result = await auth.registerUser(name, phone, login, password);
    if (result.ok) {
      req.session.userId = result.user.id;
      notifyNewRegistration(result.user).catch(() => {});
      return res.redirect('/account');
    }
    error = result.error;
  }
  res.render('register', { error, form: req.body });
}));

app.get('/login', (req, res) => {
  if (auth.currentUser(req)) return res.redirect('/account');
  res.render('login', { error: '', next: req.query.next || '/account' });
});

app.post('/login', (req, res) => {
  const { login, password, next } = req.body;
  const result = auth.loginUser(login, password);
  if (result.ok) {
    req.session.userId = result.user.id;
    return res.redirect(next || '/account');
  }
  res.render('login', { error: result.error, next: next || '/account' });
});

app.get('/logout', (req, res) => {
  req.session.userId = null;
  res.redirect('/');
});

app.get('/account', auth.requireLogin, (req, res) => {
  const user = auth.currentUser(req);
  const leads = readJson('leads.json', []).filter(l => l.user_id === user.id)
    .sort((a, b) => b.created_at.localeCompare(a.created_at));
  res.render('account', { myLeads: leads, statusLabels: STATUS_LABELS });
});

app.post('/apply', auth.requireLogin, asyncHandler(async (req, res) => {
  const user = auth.currentUser(req);
  const course = (req.body.course || '').trim();
  const message = (req.body.message || '').trim();
  const isConsultation = course.includes('консультац') || course === '';

  const lead = {
    id: uid('l_'),
    user_id: user.id,
    name: user.name,
    phone: user.phone,
    course,
    message,
    type: isConsultation ? 'consultation' : 'course',
    status: 'new',
    created_at: new Date().toISOString(),
  };

  const leads = readJson('leads.json', []);
  leads.push(lead);
  await writeJson('leads.json', leads);
  notifyNewLead(lead).catch(() => {});

  res.redirect('/contacts?sent=1');
}));

// ==================== УСТАНОВКА ====================

app.get('/install', (req, res) => {
  res.render('install', { adminExists: auth.adminExists(), done: false, error: '' });
});

app.post('/install', asyncHandler(async (req, res) => {
  if (auth.adminExists()) {
    return res.render('install', { adminExists: true, done: false, error: '' });
  }
  const { login, password, password2 } = req.body;
  let error = '';
  if (!login || !password) error = 'Заполните логин и пароль.';
  else if (password !== password2) error = 'Пароли не совпадают.';
  else if (password.length < 6) error = 'Пароль должен быть не короче 6 символов.';

  if (error) return res.render('install', { adminExists: false, done: false, error });

  await auth.createAdmin(login, password);
  res.render('install', { adminExists: false, done: true, error: '' });
}));

// ==================== ДИАГНОСТИКА ====================

app.get('/healthcheck', (req, res) => {
  const criticalFiles = [
    ['public/style.css', 'Стили сайта'],
    ['public/script.js', 'Скрипты сайта'],
    ['public/images/logo-mark.png', 'Логотип (PNG)'],
    ['public/images/favicon.svg', 'Фавикон (SVG)'],
    ['views/partials/header.ejs', 'Шапка сайта'],
    ['views/partials/footer.ejs', 'Футер сайта'],
    ['data/courses.json', 'Данные: курсы'],
    ['data/posts.json', 'Данные: блог'],
    ['data/content.json', 'Данные: тексты страниц'],
    ['data/settings.json', 'Данные: настройки ботов'],
  ];
  const files = criticalFiles.map(([p, label]) => ({
    label, path: p, ok: fs.existsSync(path.join(__dirname, p)),
  }));

  let dataWritable = false;
  try {
    const testFile = path.join(DATA_DIR, '.write_test');
    fs.writeFileSync(testFile, 'ok');
    fs.unlinkSync(testFile);
    dataWritable = true;
  } catch (e) { dataWritable = false; }

  res.render('healthcheck', {
    nodeVersion: process.version,
    nodeOk: parseInt(process.version.slice(1), 10) >= 18,
    dataWritable,
    files,
    adminExists: auth.adminExists(),
    usersCount: readJson('users.json', []).length,
    leadsCount: readJson('leads.json', []).length,
  });
});

// ==================== АДМИНКА ====================

const adminRouter = express.Router();

adminRouter.get('/login', (req, res) => {
  if (req.session.admin) return res.redirect('/admin');
  if (!auth.adminExists()) return res.redirect('/install');
  res.render('admin/login', { error: '' });
});

adminRouter.post('/login', (req, res) => {
  const { login, password } = req.body;
  if (auth.loginAdmin(login, password)) {
    req.session.admin = login;
    return res.redirect('/admin');
  }
  res.render('admin/login', { error: 'Неверный логин или пароль.' });
});

adminRouter.get('/logout', (req, res) => {
  req.session.admin = null;
  res.redirect('/admin/login');
});

adminRouter.use(auth.requireAdmin);

adminRouter.get('/', (req, res) => {
  const leads = readJson('leads.json', []);
  const users = readJson('users.json', []);
  const today = new Date().toISOString().slice(0, 10);

  const total = leads.length;
  const todayCount = leads.filter(l => l.created_at.slice(0, 10) === today).length;
  const consultations = leads.filter(l => l.type === 'consultation').length;
  const newStatus = leads.filter(l => l.status === 'new').length;

  const byCourseMap = {};
  leads.forEach(l => {
    const key = l.course || '— не указан —';
    byCourseMap[key] = (byCourseMap[key] || 0) + 1;
  });
  const byCourse = Object.entries(byCourseMap).map(([name, count]) => ({ name, count }))
    .sort((a, b) => b.count - a.count);

  const byDay = [];
  for (let i = 13; i >= 0; i--) {
    const d = new Date();
    d.setDate(d.getDate() - i);
    const iso = d.toISOString().slice(0, 10);
    const count = leads.filter(l => l.created_at.slice(0, 10) === iso).length;
    byDay.push({ date: iso, count, label: `${String(d.getDate()).padStart(2, '0')}.${String(d.getMonth() + 1).padStart(2, '0')}` });
  }
  const maxDay = Math.max(1, ...byDay.map(d => d.count));

  res.render('admin/dashboard', {
    adminLogin: req.session.admin,
    total, today: todayCount, newStatus, usersCount: users.length,
    byCourse, byDay, maxDay, consultations,
  });
});

adminRouter.get('/leads', (req, res) => {
  let leads = readJson('leads.json', []).sort((a, b) => b.created_at.localeCompare(a.created_at));
  const filter = req.query.filter || 'all';
  if (filter === 'course') leads = leads.filter(l => l.type === 'course');
  if (filter === 'consultation') leads = leads.filter(l => l.type === 'consultation');
  if (filter === 'new') leads = leads.filter(l => l.status === 'new');
  res.render('admin/leads', { adminLogin: req.session.admin, leads, filter, statusLabels: STATUS_LABELS });
});

adminRouter.post('/leads/update', asyncHandler(async (req, res) => {
  const leads = readJson('leads.json', []);
  const lead = leads.find(l => l.id === req.body.lead_id);
  if (lead) {
    lead.status = req.body.status;
    lead.start_date = (req.body.start_date || '').trim();
    lead.end_date = (req.body.end_date || '').trim();
    await writeJson('leads.json', leads);
  }
  res.redirect('/admin/leads');
}));

adminRouter.get('/courses', (req, res) => {
  const courses = readJson('courses.json', []);
  const editCourse = courses.find(c => c.id === req.query.edit) || null;
  res.render('admin/courses', { adminLogin: req.session.admin, courses, editCourse });
});

adminRouter.post('/courses/save', asyncHandler(async (req, res) => {
  const courses = readJson('courses.json', []);
  const data = {
    tag: req.body.tag || '', title: req.body.title || '', summary: req.body.summary || '',
    duration: req.body.duration || '', duration_note: req.body.duration_note || '',
    price: req.body.price || '', format: req.body.format || '',
  };
  const id = req.body.id;
  const existing = courses.find(c => c.id === id);
  if (existing) {
    Object.assign(existing, data);
  } else {
    courses.push({ id: uid('c_'), ...data });
  }
  await writeJson('courses.json', courses);
  res.redirect('/admin/courses');
}));

adminRouter.post('/courses/delete', asyncHandler(async (req, res) => {
  const courses = readJson('courses.json', []).filter(c => c.id !== req.body.id);
  await writeJson('courses.json', courses);
  res.redirect('/admin/courses');
}));

adminRouter.get('/posts', (req, res) => {
  const posts = readJson('posts.json', []).sort((a, b) => b.date.localeCompare(a.date));
  const courses = readJson('courses.json', []);
  const editPost = posts.find(p => p.id === req.query.edit) || null;
  res.render('admin/posts', { adminLogin: req.session.admin, posts, courses, editPost, today: new Date().toISOString().slice(0, 10) });
});

adminRouter.post('/posts/save', asyncHandler(async (req, res) => {
  const posts = readJson('posts.json', []);
  const data = {
    title: req.body.title || '', excerpt: req.body.excerpt || '',
    date: req.body.date || new Date().toISOString().slice(0, 10),
    course_id: req.body.course_id || '',
  };
  const id = req.body.id;
  const existing = posts.find(p => p.id === id);
  if (existing) {
    Object.assign(existing, data);
  } else {
    posts.push({ id: uid('p_'), ...data });
  }
  await writeJson('posts.json', posts);
  res.redirect('/admin/posts');
}));

adminRouter.post('/posts/delete', asyncHandler(async (req, res) => {
  const posts = readJson('posts.json', []).filter(p => p.id !== req.body.id);
  await writeJson('posts.json', posts);
  res.redirect('/admin/posts');
}));

adminRouter.get('/content', (req, res) => {
  res.render('admin/content', { adminLogin: req.session.admin, content: loadContent(), notice: '' });
});

adminRouter.post('/content/save', asyncHandler(async (req, res) => {
  const content = loadContent();
  const b = req.body;
  const pick = (section, key, formKey) => (b[formKey] !== undefined ? b[formKey] : (content[section] && content[section][key]) || '');

  content.home = {
    hero_eyebrow: pick('home', 'hero_eyebrow', 'home_hero_eyebrow'),
    hero_title: pick('home', 'hero_title', 'home_hero_title'),
    hero_lead: pick('home', 'hero_lead', 'home_hero_lead'),
    stat1_value: pick('home', 'stat1_value', 'home_stat1_value'), stat1_label: pick('home', 'stat1_label', 'home_stat1_label'),
    stat2_value: pick('home', 'stat2_value', 'home_stat2_value'), stat2_label: pick('home', 'stat2_label', 'home_stat2_label'),
    stat3_value: pick('home', 'stat3_value', 'home_stat3_value'), stat3_label: pick('home', 'stat3_label', 'home_stat3_label'),
    stat4_value: pick('home', 'stat4_value', 'home_stat4_value'), stat4_label: pick('home', 'stat4_label', 'home_stat4_label'),
    why1_title: pick('home', 'why1_title', 'home_why1_title'), why1_text: pick('home', 'why1_text', 'home_why1_text'),
    why2_title: pick('home', 'why2_title', 'home_why2_title'), why2_text: pick('home', 'why2_text', 'home_why2_text'),
    why3_title: pick('home', 'why3_title', 'home_why3_title'), why3_text: pick('home', 'why3_text', 'home_why3_text'),
    why4_title: pick('home', 'why4_title', 'home_why4_title'), why4_text: pick('home', 'why4_text', 'home_why4_text'),
  };
  content.about = {
    hero_title: pick('about', 'hero_title', 'about_hero_title'),
    intro1: pick('about', 'intro1', 'about_intro1'), intro2: pick('about', 'intro2', 'about_intro2'), intro3: pick('about', 'intro3', 'about_intro3'),
    stat_since: pick('about', 'stat_since', 'about_stat_since'), stat_grads: pick('about', 'stat_grads', 'about_stat_grads'),
    stat_programs: pick('about', 'stat_programs', 'about_stat_programs'), stat_status: pick('about', 'stat_status', 'about_stat_status'),
  };
  content.contacts = {
    address: pick('contacts', 'address', 'contacts_address'), phone: pick('contacts', 'phone', 'contacts_phone'),
    email: pick('contacts', 'email', 'contacts_email'), hours: pick('contacts', 'hours', 'contacts_hours'),
    telegram: pick('contacts', 'telegram', 'contacts_telegram'),
  };
  content.footer = { tagline: pick('footer', 'tagline', 'footer_tagline') };

  await writeJson('content.json', content);
  res.render('admin/content', { adminLogin: req.session.admin, content, notice: 'Контент сохранён и уже применился на сайте.' });
}));

adminRouter.get('/users', (req, res) => {
  const users = readJson('users.json', []).sort((a, b) => b.created_at.localeCompare(a.created_at));
  const leads = readJson('leads.json', []);
  const leadCountByUser = {};
  leads.forEach(l => { leadCountByUser[l.user_id] = (leadCountByUser[l.user_id] || 0) + 1; });
  res.render('admin/users', { adminLogin: req.session.admin, users, leadCountByUser });
});

adminRouter.get('/settings', (req, res) => {
  const settings = readJson('settings.json', {
    course_bot: { token: '', chat_id: '' }, consult_bot: { token: '', chat_id: '' }, register_bot: { token: '', chat_id: '' },
  });
  if (!settings.register_bot) settings.register_bot = { token: '', chat_id: '' };
  res.render('admin/settings', { adminLogin: req.session.admin, settings, notice: '', testResult: null });
});

adminRouter.post('/settings/bots', asyncHandler(async (req, res) => {
  const settings = readJson('settings.json', {});
  settings.course_bot = { token: req.body.course_token || '', chat_id: req.body.course_chat_id || '', label: 'Бот заявок на курсы' };
  settings.consult_bot = { token: req.body.consult_token || '', chat_id: req.body.consult_chat_id || '', label: 'Бот заявок на консультацию' };
  settings.register_bot = { token: req.body.register_token || '', chat_id: req.body.register_chat_id || '', label: 'Бот уведомлений о регистрации' };
  await writeJson('settings.json', settings);
  res.render('admin/settings', { adminLogin: req.session.admin, settings, notice: 'Настройки ботов сохранены.', testResult: null });
}));

adminRouter.post('/settings/test-bot', asyncHandler(async (req, res) => {
  const settings = readJson('settings.json', {});
  const bot = settings[req.body.bot_key];
  const testResult = bot ? await tgSend(bot.token, bot.chat_id, 'Тестовое сообщение от сайта Ishonchli Buxgalter Academy ✅') : { ok: false, error: 'бот не найден' };
  res.render('admin/settings', { adminLogin: req.session.admin, settings, notice: '', testResult });
}));

adminRouter.post('/settings/password', asyncHandler(async (req, res) => {
  const bcrypt = require('bcryptjs');
  const admin = auth.getAdmin();
  const settings = readJson('settings.json', {});
  let notice;
  if (!bcrypt.compareSync(req.body.current_password || '', admin.password_hash)) {
    notice = 'Текущий пароль неверен.';
  } else if ((req.body.new_password || '').length < 6) {
    notice = 'Новый пароль должен быть не короче 6 символов.';
  } else {
    admin.password_hash = bcrypt.hashSync(req.body.new_password, 10);
    await writeJson('admin.json', admin);
    notice = 'Пароль администратора изменён.';
  }
  res.render('admin/settings', { adminLogin: req.session.admin, settings, notice, testResult: null });
}));

app.use('/admin', adminRouter);

// 404
app.use((req, res) => {
  res.status(404).send('Страница не найдена — 404');
});

// Единый обработчик ошибок: вместо белого экрана/500 — понятное объяснение,
// особенно полезно на хостингах с файловой системой только для чтения (Vercel и т.п.)
app.use((err, req, res, next) => {
  console.error('Ошибка:', err);
  const isReadOnly = /EROFS|read-only|EACCES|EPERM/i.test(err && err.message || '');
  res.status(500).send(`<!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ошибка сохранения</title>
<link rel="stylesheet" href="/style.css"></head>
<body style="background:var(--ink);min-height:100vh;display:flex;align-items:center;justify-content:center;">
<div class="form-card" style="max-width:520px;">
  <h1 style="font-size:20px;">Не удалось сохранить</h1>
  ${isReadOnly
    ? `<p>Похоже, сайт размещён на хостинге, где файловая система доступна только для чтения
       (например, Vercel). Проект хранит все данные (курсы, заявки, контент, пользователей)
       в обычных файлах — на таких платформах запись невозможна физически, это ограничение
       самого хостинга, а не ошибка в коде.</p>
       <p class="muted" style="font-size:13.5px;">Решение: перенести сайт на хостинг с обычной
       файловой системой (VPS, Render, Railway, обычный Node.js-хостинг) — там всё заработает
       без изменений в коде.</p>`
    : `<p class="muted">Техническая причина: ${(err && err.message) || 'неизвестная ошибка'}</p>`
  }
  <a href="javascript:history.back()" class="btn btn-ghost" style="margin-top:12px;">Назад</a>
</div>
</body></html>`);
});

// На обычном хостинге/VPS — запускаем постоянный сервер.
// На serverless-платформах (Vercel и т.п.) — экспортируем приложение.
if (require.main === module) {
  app.listen(PORT, () => {
    console.log(`${SITE_NAME} запущен: http://localhost:${PORT}`);
  });
}

module.exports = app;
