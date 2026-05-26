// ─── Config ───────────────────────────────────────────────────────────────────
// Define window.API_BASE antes de carregar este ficheiro para apontar ao servidor PHP
// Exemplo: <script>window.API_BASE = 'http://localhost:8000'</script>
// Se estiver vazio usa a mesma origem (quando servido pelo PHP)
const API_BASE = (typeof window !== 'undefined' && window.API_BASE)
  ? window.API_BASE.replace(/\/$/, '')
  : '';

// ─── API (backend PHP) ────────────────────────────────────────────────────────
async function apiGetArticles() {
  const r = await fetch(API_BASE + '/api/articles');
  if (!r.ok) throw new Error('API ' + r.status);
  return r.json();
}

async function apiGetArticle(id) {
  const r = await fetch(API_BASE + '/api/article?id=' + encodeURIComponent(id));
  if (!r.ok) throw new Error('API ' + r.status);
  return r.json();
}

// ─── Storage (fallback localStorage) ─────────────────────────────────────────
const ARTICLES_KEY = 'journal_articles';
const AUTH_KEY     = 'journal_auth';
const DEMO_EMAIL   = 'admin@thejournal.com';
const DEMO_PASS    = 'journal2026';

function getArticles() {
  try { return JSON.parse(localStorage.getItem(ARTICLES_KEY) || '[]'); }
  catch { return []; }
}

function saveArticles(articles) {
  localStorage.setItem(ARTICLES_KEY, JSON.stringify(articles));
}

function isAdmin() {
  return localStorage.getItem(AUTH_KEY) === 'true';
}

function requireAdmin() {
  if (!isAdmin()) window.location.href = 'admin-login.html';
}

function adminLogout() {
  localStorage.removeItem(AUTH_KEY);
  window.location.href = '../index.html';
}

// ─── Helpers ──────────────────────────────────────────────────────────────────
function formatDate(iso) {
  if (!iso) return '';
  const d = new Date(iso);
  return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
}

function readTime(text) {
  const words = (text || '').trim().split(/\s+/).length;
  const mins  = Math.max(1, Math.round(words / 200));
  return `${mins} min read`;
}

function excerpt(text, len = 160) {
  const plain = (text || '').replace(/<[^>]*>/g, '');
  return plain.length > len ? plain.slice(0, len).trimEnd() + '…' : plain;
}

// Suporta formato API {id, title, excerpt, published_at}
// e formato localStorage {title, body, tag, date}
function articleCardHTML(art, i, base = '') {
  const isApi   = art.id !== undefined;
  const href    = isApi
    ? (API_BASE || '') + '/article?id=' + art.id
    : base + 'article.html?id=' + i;
  const dateStr = formatDate(isApi ? art.published_at : art.date);
  const exc     = isApi ? (art.excerpt || '') : excerpt(art.body || '');
  const tag     = isApi ? null : (art.tag || null);

  return `
    <div class="article-card" onclick="window.location.href='${href}'">
      <div class="article-meta">
        <span class="article-date">${dateStr}</span>
        ${tag ? `<span class="article-tag">${tag}</span>` : ''}
      </div>
      <h3 class="article-title">${art.title || ''}</h3>
      <p class="article-excerpt">${exc}</p>
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
