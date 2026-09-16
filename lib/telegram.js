const { readJson } = require('./db');

async function tgSend(token, chatId, text) {
  if (!token || !chatId) return { ok: false, error: 'Бот не настроен (нет token/chat_id)' };
  try {
    const resp = await fetch(`https://api.telegram.org/bot${token}/sendMessage`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ chat_id: chatId, text, parse_mode: 'HTML' }),
    });
    const json = await resp.json().catch(() => ({}));
    if (!resp.ok || json.ok === false) {
      return { ok: false, error: (json && json.description) || `HTTP ${resp.status}` };
    }
    return { ok: true };
  } catch (e) {
    return { ok: false, error: e.message };
  }
}

async function notifyNewLead(lead) {
  const settings = readJson('settings.json', {});
  const botKey = lead.type === 'consultation' ? 'consult_bot' : 'course_bot';
  const bot = settings[botKey];
  if (!bot) return;

  const label = lead.type === 'consultation' ? 'Заявка на консультацию' : 'Заявка на курс';
  const text = `<b>${label}</b>\n`
    + `Имя: ${lead.name}\n`
    + `Телефон: ${lead.phone}\n`
    + (lead.course ? `Курс: ${lead.course}\n` : '')
    + (lead.message ? `Комментарий: ${lead.message}\n` : '')
    + `Время: ${lead.created_at}`;

  return tgSend(bot.token, bot.chat_id, text);
}

async function notifyNewRegistration(user) {
  const settings = readJson('settings.json', {});
  const bot = settings.register_bot;
  if (!bot) return;

  const text = `<b>Новая регистрация на сайте</b>\n`
    + `Имя: ${user.name}\n`
    + `Телефон: ${user.phone}\n`
    + `Логин: ${user.login}\n`
    + `Время: ${user.created_at}`;

  return tgSend(bot.token, bot.chat_id, text);
}

module.exports = { tgSend, notifyNewLead, notifyNewRegistration };
