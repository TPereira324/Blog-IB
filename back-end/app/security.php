<?php
declare(strict_types=1);

function session_init(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    if ($isHttps) {
        ini_set('session.cookie_secure', '1');
    }

    session_name('blogib_session');
    session_start();
}

function send_security_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    $csp = implode('; ', [
        "default-src 'self'",
        "base-uri 'self'",
        "form-action 'self'",
        "frame-ancestors 'none'",
        "img-src 'self' data:",
        "style-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net 'unsafe-inline'",
        "font-src 'self' https://fonts.gstatic.com",
        "script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'",
        "connect-src 'self'",
    ]);
    header('Content-Security-Policy: ' . $csp);
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function nl2p(string $text): string
{
    $text = trim($text);
    if ($text === '') {
        return '';
    }
    $parts = preg_split('/\R{2,}/', $text) ?: [];
    $html = [];
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p === '') {
            continue;
        }
        $html[] = '<p>' . nl2br(e($p)) . '</p>';
    }
    return implode("\n", $html);
}

function csrf_token(): string
{
    if (!isset($_SESSION['_csrf']) || !is_string($_SESSION['_csrf']) || $_SESSION['_csrf'] === '') {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_validate(string $token): bool
{
    $stored = $_SESSION['_csrf'] ?? '';
    if (!is_string($stored) || $stored === '' || $token === '') {
        return false;
    }
    return hash_equals($stored, $token);
}

function redirect(string $path): void
{
    $baseUrl = (string)app_config('app.base_url', '');
    if ($baseUrl !== '') {
        $target = $baseUrl . $path;
    } else {
        $target = $path;
    }
    header('Location: ' . $target, true, 302);
    exit;
}

function client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if (!is_string($ip) || $ip === '') {
        return '0.0.0.0';
    }
    return $ip;
}

function rate_limit_allow_login(string $ip): bool
{
    if (!isset($_SESSION['_login_rl']) || !is_array($_SESSION['_login_rl'])) {
        $_SESSION['_login_rl'] = [];
    }

    $now = time();
    $state = $_SESSION['_login_rl'][$ip] ?? ['count' => 0, 'reset' => $now + 300, 'locked_until' => 0];
    if (!is_array($state)) {
        $state = ['count' => 0, 'reset' => $now + 300, 'locked_until' => 0];
    }

    if (($state['locked_until'] ?? 0) > $now) {
        $_SESSION['_login_rl'][$ip] = $state;
        return false;
    }

    if (($state['reset'] ?? 0) < $now) {
        $state = ['count' => 0, 'reset' => $now + 300, 'locked_until' => 0];
    }

    $state['count'] = (int)($state['count'] ?? 0) + 1;
    if ($state['count'] > 8) {
        $state['locked_until'] = $now + 300;
    }

    $_SESSION['_login_rl'][$ip] = $state;
    return $state['locked_until'] <= $now;
}

function slugify(string $value): string
{
    $value = trim(mb_strtolower($value));
    $value = preg_replace('~[^\pL\pN]+~u', '-', $value) ?? '';
    $value = trim($value, '-');
    $value = preg_replace('~-{2,}~', '-', $value) ?? '';
    return $value !== '' ? $value : 'artigo';
}

