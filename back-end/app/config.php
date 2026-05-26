<?php
declare(strict_types=1);

function app_config_all(): array
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    // Render injeta DATABASE_URL automaticamente ao ligar uma base de dados
    $databaseUrl = getenv('DATABASE_URL') ?: '';

    if ($databaseUrl !== '') {
        $parsed  = parse_url($databaseUrl);
        $scheme  = $parsed['scheme'] ?? 'postgres';
        $dbHost  = $parsed['host'] ?? 'localhost';
        $dbPort  = (string)($parsed['port'] ?? ($scheme === 'mysql' ? 3306 : 5432));
        $dbName  = ltrim($parsed['path'] ?? '', '/');
        $dbUser  = urldecode($parsed['user'] ?? '');
        $dbPass  = urldecode($parsed['pass'] ?? '');
        $dbType  = in_array($scheme, ['mysql', 'mysql2'], true) ? 'mysql' : 'pgsql';

        if ($dbType === 'pgsql') {
            $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";
        } else {
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        }
    } else {
        $dbHost = getenv('DB_HOST') ?: 'localhost';
        $dbName = getenv('DB_NAME') ?: '';
        $dbPort = getenv('DB_PORT') ?: '3306';
        $dbUser = getenv('DB_USER') ?: '';
        $dbPass = getenv('DB_PASS') ?: '';
        $dbType = getenv('DB_TYPE') ?: 'mysql';

        $dsnEnv = getenv('DB_DSN');
        if ($dsnEnv !== false && $dsnEnv !== '') {
            $dsn = $dsnEnv;
        } elseif ($dbType === 'pgsql') {
            $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";
        } else {
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        }
    }

    $config = [
        'app' => [
            'env'       => getenv('APP_ENV') ?: 'prod',
            'base_url'  => rtrim((string)(getenv('APP_BASE_URL') ?: ''), '/'),
            'setup_key' => (string)(getenv('APP_SETUP_KEY') ?: ''),
        ],
        'db' => [
            'dsn'  => $dsn,
            'user' => $dbUser,
            'pass' => $dbPass,
            'type' => $dbType,
        ],
    ];

    return $config;
}

function app_config(string $path, mixed $default = null): mixed
{
    $config = app_config_all();
    $parts  = explode('.', $path);
    $value  = $config;
    foreach ($parts as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}
