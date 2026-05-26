<?php
declare(strict_types=1);

function render(string $view, array $data = []): void
{
    $viewsDir = dirname(__DIR__) . '/../../front-end/views';
    $viewFile = $viewsDir . '/' . $view . '.php';
    if (!is_file($viewFile)) {
        throw new RuntimeException('View não encontrada: ' . $view);
    }

    $pageTitle = (string)($data['pageTitle'] ?? '');
    $flash = $data['flash'] ?? null;
    $userLoggedIn = auth_is_logged_in();
    $csrf = csrf_token();

    extract($data, EXTR_SKIP);

    require $viewsDir . '/layout.php';
}
