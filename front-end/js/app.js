// ─── Constants ────────────────────────────────────────────────────────────────
const ARTICLES_KEY = 'journal_articles';
const AUTH_KEY = 'journal_auth';
const DEMO_EMAIL = 'admin@thejournal.com';
const DEMO_PASS = 'journal2026';

// ─── Storage ──────────────────────────────────────────────────────────────────
function getArticles() {
  try { return JSON.parse(localStorage.getItem(ARTICLES_KEY) || '[]'); }
  catch { return []; }
}

function saveArticles(articles) {
  localStorage.setItem(ARTICLES_KEY, JSON.stringify(articles));
}

// ─── Auth ─────────────────────────────────────────────────────────────────────
function isAdmin() {
  return localStorage.getItem(AUTH_KEY) === 'true';
}

function requireAdmin() {
  if (!isAdmin()) window.location.href = 'admin-login.html';
}

function adminLogout() {
  localStorage.removeItem(AUTH_KEY);
  window.location.href = 'index.html';
}

// ─── Helpers ──────────────────────────────────────────────────────────────────
function formatDate(iso) {
  const d = new Date(iso);
  return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
}

function readTime(text) {
  const words = text.trim().split(/\s+/).length;
  const mins = Math.max(1, Math.round(words / 200));
  return `${mins} min read`;
}

function excerpt(text, len = 160) {
  const plain = text.replace(/<[^>]*>/g, '');
  return plain.length > len ? plain.slice(0, len).trimEnd() + '…' : plain;
}

function articleCardHTML(art, i) {
  return `
    <div class="article-card" onclick="window.location.href='article.html?id=${i}'">
      <div class="article-meta">
        <span class="article-date">${formatDate(art.date)}</span>
        ${art.tag ? `<span class="article-tag">${art.tag}</span>` : ''}
      </div>
      <h3 class="article-title">${art.title}</h3>
      <p class="article-excerpt">${excerpt(art.body)}</p>
    </div>`;
}

// ─── Toast ────────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

// ─── Write helpers ────────────────────────────────────────────────────────────
function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = el.scrollHeight + 'px';
}