<?php
declare(strict_types=1);

// Load .env from project root if it exists
(function () {
    $envFile = dirname(dirname(__DIR__)) . '/.env';
    if (!is_file($envFile)) {
        return;
    }
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        $name  = trim($name);
        $value = trim($value, " \t\"'");
        if ($name !== '' && getenv($name) === false) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
})();

require __DIR__ . '/config.php';
require __DIR__ . '/db.php';
require __DIR__ . '/security.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/articles.php';
require __DIR__ . '/views.php';

date_default_timezone_set('Europe/Lisbon');

$env = app_config('app.env', 'prod');
if ($env === 'dev') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
}

session_init();
send_security_headers();

try {
    db_ensure_schema();
} catch (Throwable $e) {
    // DB não configurada ou indisponível — regista o erro mas não crasha
    error_log('[Blog-IB] DB error: ' . $e->getMessage());
}

