const bcrypt = require('bcryptjs');
const { readJson, writeJson, uid, filePath } = require('./db');
const fs = require('fs');

function findUserByLogin(login) {
  const users = readJson('users.json', []);
  return users.find(u => u.login.toLowerCase() === String(login).toLowerCase()) || null;
}

function findUserById(id) {
  const users = readJson('users.json', []);
  return users.find(u => u.id === id) || null;
}

async function registerUser(name, phone, login, password) {
  if (findUserByLogin(login)) {
    return { ok: false, error: 'Такой логин уже занят, выберите другой.' };
  }
  const users = readJson('users.json', []);
  const user = {
    id: uid('u_'),
    name,
    phone,
    login,
    password_hash: bcrypt.hashSync(password, 10),
    created_at: new Date().toISOString(),
  };
  users.push(user);
  await writeJson('users.json', users);
  return { ok: true, user };
}

function loginUser(login, password) {
  const user = findUserByLogin(login);
  if (!user || !bcrypt.compareSync(password, user.password_hash)) {
    return { ok: false, error: 'Неверный логин или пароль.' };
  }
  return { ok: true, user };
}

function adminExists() {
  return fs.existsSync(filePath('admin.json'));
}

function getAdmin() {
  return readJson('admin.json', null);
}

async function createAdmin(login, password) {
  await writeJson('admin.json', {
    login,
    password_hash: bcrypt.hashSync(password, 10),
  });
}

function loginAdmin(login, password) {
  const admin = getAdmin();
  if (!admin || admin.login.toLowerCase() !== String(login).toLowerCase()) return false;
  return bcrypt.compareSync(password, admin.password_hash);
}

// Middleware
function requireLogin(req, res, next) {
  if (!req.session.userId) {
    return res.redirect('/login?next=' + encodeURIComponent(req.originalUrl));
  }
  next();
}

function requireAdmin(req, res, next) {
  if (!req.session.admin) {
    return res.redirect('/admin/login');
  }
  next();
}

function currentUser(req) {
  if (!req.session.userId) return null;
  return findUserById(req.session.userId);
}

module.exports = {
  findUserByLogin, findUserById, registerUser, loginUser,
  adminExists, getAdmin, createAdmin, loginAdmin,
  requireLogin, requireAdmin, currentUser,
};
