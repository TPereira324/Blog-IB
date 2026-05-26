<?php
// Servidor de desenvolvimento: php -S localhost:8000 router.php
// (executar a partir da pasta Blog-IB)

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

// Serve ficheiros estáticos directamente
if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false;
}

require __DIR__ . '/back-end/index.php';
