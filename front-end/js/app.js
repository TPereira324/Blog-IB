const apiBase = '/api';

async function apiGet(path) {
    const response = await fetch(`${apiBase}${path}`, {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' },
    });
    if (!response.ok) {
        const message = await response.text();
        throw new Error(message || 'Erro ao carregar dados.');
    }
    return response.json();
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function formatDate(value) {
    const date = new Date(value);
    return date.toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function getQueryParam(name) {
    return new URLSearchParams(window.location.search).get(name) || '';
}

function showEmpty(message) {
    const emptyBox = document.getElementById('article-empty');
    if (!emptyBox) return;
    emptyBox.textContent = message;
    emptyBox.classList.remove('d-none');
}

function createArticleCard(article) {
    return `
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card p-4 h-100 article-card animate-fade-in">
                <div class="mb-3 text-muted small">${formatDate(article.published_at)}</div>
                <h3 class="h5 mb-3">${escapeHtml(article.title)}</h3>
                <p class="text-muted mb-4">${escapeHtml(article.excerpt)}</p>
                <a class="stretched-link text-decoration-none fw-semibold" href="article.html?id=${article.id}">Ler artigo</a>
            </div>
        </div>
    `;
}

async function initHome() {
    try {
        const articles = await apiGet('/api/articles');
        const list = document.getElementById('article-list');
        if (!list) return;
        if (!Array.isArray(articles) || articles.length === 0) {
            showEmpty('Ainda não existem artigos publicados. Volte mais tarde.');
            return;
        }
        list.innerHTML = articles.slice(0, 6).map(createArticleCard).join('');
    } catch (error) {
        showEmpty(error.message);
    }
}

async function initArticles() {
    try {
        const articles = await apiGet('/api/articles');
        const list = document.getElementById('article-list');
        if (!list) return;
        if (!Array.isArray(articles) || articles.length === 0) {
            showEmpty('Ainda não existem artigos publicados. Volte mais tarde.');
            return;
        }
        list.innerHTML = articles.map(createArticleCard).join('');
    } catch (error) {
        showEmpty(error.message);
    }
}

async function initArticle() {
    const id = getQueryParam('id');
    const errorBox = document.getElementById('article-error');
    if (!id) {
        if (errorBox) {
            errorBox.textContent = 'ID do artigo não fornecido.';
            errorBox.classList.remove('d-none');
        }
        return;
    }

    try {
        const article = await apiGet(`/api/article?id=${encodeURIComponent(id)}`);
        const detail = document.getElementById('article-detail');
        if (!detail) return;
        detail.innerHTML = `
            <div class="mb-4"><a class="link-underline" href="articles.html">← Voltar aos artigos</a></div>
            <h1 class="mb-3">${escapeHtml(article.title)}</h1>
            <div class="text-muted mb-4">${formatDate(article.published_at)}</div>
            <div class="article-body text-break fs-5 lh-lg">${article.content}</div>
        `;
    } catch (error) {
        if (errorBox) {
            errorBox.textContent = error.message;
            errorBox.classList.remove('d-none');
        }
    }
}

async function initLogin() {
    try {
        const response = await apiGet('/api/csrf');
        const tokenInput = document.getElementById('csrfToken');
        if (tokenInput && response.token) {
            tokenInput.value = response.token;
        }
    } catch (error) {
        const errorBox = document.getElementById('loginError');
        if (errorBox) {
            errorBox.textContent = 'Não foi possível carregar o formulário de login.';
            errorBox.classList.remove('d-none');
        }
    }
}

function initPage() {
    const page = document.body.dataset.page;
    if (!page) return;
    if (page === 'home') initHome();
    if (page === 'articles') initArticles();
    if (page === 'article') initArticle();
    if (page === 'login') initLogin();
}

window.addEventListener('DOMContentLoaded', initPage);
