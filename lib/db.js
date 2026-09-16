const fs = require('fs');
const path = require('path');
const lockfile = require('proper-lockfile');

const DATA_DIR = path.join(__dirname, '..', 'data');

function filePath(name) {
  return path.join(DATA_DIR, name);
}

function ensureFile(name, fallback) {
  const p = filePath(name);
  if (!fs.existsSync(p)) {
    fs.writeFileSync(p, JSON.stringify(fallback, null, 2));
  }
  return p;
}

function readJson(name, fallback = []) {
  const p = ensureFile(name, fallback);
  try {
    const raw = fs.readFileSync(p, 'utf8');
    return raw.trim() === '' ? fallback : JSON.parse(raw);
  } catch (e) {
    return fallback;
  }
}

// Синхронная запись с блокировкой файла — чтобы две одновременные заявки
// не затёрли друг друга (аналог flock() в PHP-версии).
async function writeJson(name, data) {
  const p = ensureFile(name, []);
  let release = null;
  try {
    release = await lockfile.lock(p, { retries: { retries: 5, minTimeout: 50 } });
    fs.writeFileSync(p, JSON.stringify(data, null, 2));
  } finally {
    if (release) await release();
  }
  return true;
}

function uid(prefix = '') {
  return prefix + require('crypto').randomBytes(6).toString('hex');
}

module.exports = { readJson, writeJson, uid, DATA_DIR, filePath };
