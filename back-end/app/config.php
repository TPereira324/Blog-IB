<?php
declare(strict_types=1);

function app_config_all(): array
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    $dbHost = getenv('DB_HOST') ?: '';
    $dbName = getenv('DB_NAME') ?: '';
    $dbPort = getenv('DB_PORT') ?: '3306';

    $dsn = getenv('DB_DSN');
    if ($dsn === false || $dsn === '') {
        $dsn = 'mysql:host=' . $dbHost . ';port=' . $dbPort . ';dbname=' . $dbName . ';charset=utf8mb4';
    }

    $config = [
        'app' => [
            'env' => getenv('APP_ENV') ?: 'prod',
            'base_url' => rtrim((string)(getenv('APP_BASE_URL') ?: ''), '/'),
            'setup_key' => (string)(getenv('APP_SETUP_KEY') ?: ''),
        ],
        'db' => [
            'dsn' => $dsn,
            'user' => (string)(getenv('DB_USER') ?: ''),
            'pass' => (string)(getenv('DB_PASS') ?: ''),
        ],
    ];

    return $config;
}

function app_config(string $path, mixed $default = null): mixed
{
    $config = app_config_all();
    $parts = explode('.', $path);
    $value = $config;
    foreach ($parts as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

