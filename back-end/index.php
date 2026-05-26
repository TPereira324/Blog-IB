<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

$routes = [
    'GET' => [
        '/' => 'home',
        '/articles' => 'articles',
        '/article' => 'article',
        '/api/articles' => 'api_articles',
        '/api/article' => 'api_article',
        '/api/csrf' => 'api_csrf',
        '/login' => 'login',
        '/logout' => 'logout',
        '/admin' => 'admin_list',
        '/admin/write' => 'admin_write',
        '/admin/edit' => 'admin_edit',
        '/setup' => 'setup',
    ],
    'POST' => [
        '/login' => 'login_post',
        '/logout' => 'logout',
        '/admin/write' => 'admin_write_post',
        '/admin/edit' => 'admin_edit_post',
        '/admin/delete' => 'admin_delete_post',
        '/admin/publish' => 'admin_publish_post',
        '/newsletter' => 'newsletter_post',
        '/setup' => 'setup_post',
    ],
];

$handler = $routes[$method][$path] ?? null;
if ($handler === null) {
    http_response_code(404);
    render('home', [
        'pageTitle' => 'Página não encontrada',
        'flash' => ['type' => 'warning', 'message' => 'Página não encontrada.'],
    ]);
    exit;
}

switch ($handler) {
    case 'home': {
        $articles = articles_latest_published(6);
        render('home', [
            'pageTitle' => 'Início',
            'articles'  => $articles,
            'flash'     => isset($_GET['subscribed'])
                ? ['type' => 'success', 'message' => 'Subscrito. Bem-vindo ao Journal.']
                : null,
        ]);
        break;
    }
    case 'articles': {
        $articles = articles_list_published();
        render('articles', [
            'pageTitle' => 'Artigos',
            'articles' => $articles,
        ]);
        break;
    }
    case 'article': {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(404);
            render('articles', [
                'pageTitle' => 'Artigos',
                'articles' => articles_list_published(),
                'flash' => ['type' => 'warning', 'message' => 'Artigo inválido.'],
            ]);
            break;
        }
        $article = articles_get_published_by_id($id);
        if ($article === null) {
            http_response_code(404);
            render('articles', [
                'pageTitle' => 'Artigos',
                'articles' => articles_list_published(),
                'flash' => ['type' => 'warning', 'message' => 'Artigo não encontrado.'],
            ]);
            break;
        }
        render('article', [
            'pageTitle' => $article['title'],
            'article' => $article,
        ]);
        break;
    }
    case 'api_articles': {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(articles_list_published(), JSON_UNESCAPED_UNICODE);
        break;
    }
    case 'api_article': {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        header('Content-Type: application/json; charset=utf-8');
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido.'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $article = articles_get_published_by_id($id);
        if ($article === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Artigo não encontrado.'], JSON_UNESCAPED_UNICODE);
            break;
        }
        echo json_encode($article, JSON_UNESCAPED_UNICODE);
        break;
    }
    case 'api_csrf': {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['token' => csrf_token()], JSON_UNESCAPED_UNICODE);
        break;
    }
    case 'login': {
        if (auth_is_logged_in()) {
            redirect('/admin');
        }
        render('login', [
            'pageTitle' => 'Entrar',
        ]);
        break;
    }
    case 'login_post': {
        $token = (string)($_POST['_csrf'] ?? '');
        if (!csrf_validate($token)) {
            http_response_code(400);
            render('login', [
                'pageTitle' => 'Entrar',
                'flash' => ['type' => 'danger', 'message' => 'Sessão inválida. Tenta novamente.'],
            ]);
            break;
        }

        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $ip = client_ip();

        if (!rate_limit_allow_login($ip)) {
            http_response_code(429);
            render('login', [
                'pageTitle' => 'Entrar',
                'flash' => ['type' => 'danger', 'message' => 'Muitas tentativas. Espera um pouco e tenta novamente.'],
            ]);
            break;
        }

        $result = auth_login_writer($email, $password);
        if ($result['ok'] === true) {
            redirect('/admin');
        }
        render('login', [
            'pageTitle' => 'Entrar',
            'flash' => ['type' => 'danger', 'message' => $result['message']],
        ]);
        break;
    }
    case 'logout': {
        auth_logout();
        redirect('/');
        break;
    }
    case 'admin_list': {
        auth_require_writer();
        $articles = articles_list_all_for_admin();
        $flashMessages = [
            'created'   => ['type' => 'success', 'message' => 'Ensaio guardado como rascunho.'],
            'updated'   => ['type' => 'success', 'message' => 'Ensaio actualizado.'],
            'published' => ['type' => 'success', 'message' => 'Ensaio publicado.'],
            'deleted'   => ['type' => 'success', 'message' => 'Ensaio eliminado.'],
        ];
        $flashKey = array_key_first(array_filter(
            $flashMessages,
            fn($k) => isset($_GET[$k]),
            ARRAY_FILTER_USE_KEY
        ));
        render('admin_list', [
            'pageTitle' => 'Painel',
            'articles'  => $articles,
            'flash'     => $flashKey ? $flashMessages[$flashKey] : null,
        ]);
        break;
    }
    case 'admin_write': {
        auth_require_writer();
        render('admin_write', [
            'pageTitle' => 'Escrever',
            'form' => [
                'title' => '',
                'excerpt' => '',
                'content' => '',
            ],
        ]);
        break;
    }
    case 'admin_write_post': {
        auth_require_writer();
        $token = (string)($_POST['_csrf'] ?? '');
        if (!csrf_validate($token)) {
            http_response_code(400);
            render('admin_write', [
                'pageTitle' => 'Escrever',
                'form' => $_POST,
                'flash' => ['type' => 'danger', 'message' => 'Sessão inválida.'],
            ]);
            break;
        }

        $title = trim((string)($_POST['title'] ?? ''));
        $excerpt = trim((string)($_POST['excerpt'] ?? ''));
        $content = trim((string)($_POST['content'] ?? ''));

        $result = articles_create_draft(auth_user_id(), $title, $excerpt, $content);
        if ($result['ok'] !== true) {
            render('admin_write', [
                'pageTitle' => 'Escrever',
                'form' => compact('title', 'excerpt', 'content'),
                'flash' => ['type' => 'danger', 'message' => $result['message']],
            ]);
            break;
        }
        redirect('/admin?created=1');
        break;
    }
    case 'admin_edit': {
        auth_require_writer();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            redirect('/admin');
        }
        $article = articles_get_for_admin_by_id($id);
        if ($article === null) {
            redirect('/admin');
        }
        render('admin_write', [
            'pageTitle' => 'Editar',
            'editId' => $id,
            'form' => [
                'title' => $article['title'],
                'excerpt' => $article['excerpt'],
                'content' => $article['content'],
            ],
        ]);
        break;
    }
    case 'admin_edit_post': {
        auth_require_writer();
        $token = (string)($_POST['_csrf'] ?? '');
        if (!csrf_validate($token)) {
            http_response_code(400);
            render('admin_list', [
                'pageTitle' => 'Painel',
                'articles' => articles_list_all_for_admin(),
                'flash' => ['type' => 'danger', 'message' => 'Sessão inválida.'],
            ]);
            break;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            redirect('/admin');
        }

        $title = trim((string)($_POST['title'] ?? ''));
        $excerpt = trim((string)($_POST['excerpt'] ?? ''));
        $content = trim((string)($_POST['content'] ?? ''));
        $result = articles_update_draft($id, $title, $excerpt, $content);
        if ($result['ok'] !== true) {
            render('admin_write', [
                'pageTitle' => 'Editar',
                'editId' => $id,
                'form' => compact('title', 'excerpt', 'content'),
                'flash' => ['type' => 'danger', 'message' => $result['message']],
            ]);
            break;
        }
        redirect('/admin?updated=1');
        break;
    }
    case 'admin_publish_post': {
        auth_require_writer();
        $token = (string)($_POST['_csrf'] ?? '');
        if (!csrf_validate($token)) {
            http_response_code(400);
            render('admin_list', [
                'pageTitle' => 'Painel',
                'articles' => articles_list_all_for_admin(),
                'flash' => ['type' => 'danger', 'message' => 'Sessão inválida.'],
            ]);
            break;
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            articles_publish($id);
        }
        redirect('/admin?published=1');
        break;
    }
    case 'admin_delete_post': {
        auth_require_writer();
        $token = (string)($_POST['_csrf'] ?? '');
        if (!csrf_validate($token)) {
            http_response_code(400);
            render('admin_list', [
                'pageTitle' => 'Painel',
                'articles' => articles_list_all_for_admin(),
                'flash' => ['type' => 'danger', 'message' => 'Sessão inválida.'],
            ]);
            break;
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            articles_delete($id);
        }
        redirect('/admin?deleted=1');
        break;
    }
    case 'newsletter_post': {
        redirect('/?subscribed=1');
        break;
    }
    case 'setup': {
        if (!setup_is_enabled()) {
            http_response_code(404);
            render('home', [
                'pageTitle' => 'Início',
                'articles' => articles_latest_published(6),
            ]);
            break;
        }
        render('setup', [
            'pageTitle' => 'Setup',
        ]);
        break;
    }
    case 'setup_post': {
        if (!setup_is_enabled()) {
            http_response_code(404);
            render('home', [
                'pageTitle' => 'Início',
                'articles' => articles_latest_published(6),
            ]);
            break;
        }
        $token = (string)($_POST['_csrf'] ?? '');
        if (!csrf_validate($token)) {
            http_response_code(400);
            render('setup', [
                'pageTitle' => 'Setup',
                'flash' => ['type' => 'danger', 'message' => 'Sessão inválida.'],
            ]);
            break;
        }

        $setupKey = (string)($_POST['setup_key'] ?? '');
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        $result = setup_create_first_writer($setupKey, $email, $password);
        if ($result['ok'] !== true) {
            render('setup', [
                'pageTitle' => 'Setup',
                'flash' => ['type' => 'danger', 'message' => $result['message']],
            ]);
            break;
        }
        redirect('/login?setup=1');
        break;
    }
}

