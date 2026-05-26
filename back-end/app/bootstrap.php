<?php
declare(strict_types=1);

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

db_ensure_schema();

