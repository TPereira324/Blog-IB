<?php
declare(strict_types=1);

function auth_is_logged_in(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['id']);
}

function auth_user_id(): int
{
    return (int)($_SESSION['user']['id'] ?? 0);
}

function auth_require_writer(): void
{
    if (!auth_is_logged_in() || (string)($_SESSION['user']['role'] ?? '') !== 'writer') {
        redirect('/login');
    }
}

function auth_login_writer(string $email, string $password): array
{
    if ($email === '' || $password === '') {
        return ['ok' => false, 'message' => 'Preenche email e password.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'message' => 'Email inválido.'];
    }
    if (mb_strlen($password) < 6) {
        return ['ok' => false, 'message' => 'Password inválida.'];
    }

    $stmt = db()->prepare('SELECT id, email, password_hash, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => mb_strtolower($email)]);
    $user = $stmt->fetch();
    if (!$user) {
        return ['ok' => false, 'message' => 'Credenciais inválidas.'];
    }
    if ((string)$user['role'] !== 'writer') {
        return ['ok' => false, 'message' => 'Acesso não autorizado.'];
    }
    if (!password_verify($password, (string)$user['password_hash'])) {
        return ['ok' => false, 'message' => 'Credenciais inválidas.'];
    }

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'email' => (string)$user['email'],
        'role' => (string)$user['role'],
    ];

    $stmt = db()->prepare('UPDATE users SET last_login_at = :ts WHERE id = :id');
    $stmt->execute([
        ':ts' => gmdate('Y-m-d H:i:s'),
        ':id' => (int)$user['id'],
    ]);

    return ['ok' => true, 'message' => 'OK'];
}

function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool)$params['secure'], (bool)$params['httponly']);
    }
    session_destroy();
}

function setup_is_enabled(): bool
{
    $key = (string)app_config('app.setup_key', '');
    return $key !== '';
}

function setup_create_first_writer(string $setupKey, string $email, string $password): array
{
    $expected = (string)app_config('app.setup_key', '');
    if ($expected === '' || $setupKey === '' || !hash_equals($expected, $setupKey)) {
        return ['ok' => false, 'message' => 'Setup key inválida.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'message' => 'Email inválido.'];
    }
    if (mb_strlen($password) < 6) {
        return ['ok' => false, 'message' => 'Password fraca. Usa pelo menos 6 caracteres.'];
    }

    $pdo = db();
    $count = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($count > 0) {
        $stmt = $pdo->prepare('UPDATE users SET email = :email, password_hash = :hash WHERE id = (SELECT id FROM users ORDER BY id LIMIT 1)');
        $stmt->execute([
            ':email' => mb_strtolower(trim($email)),
            ':hash'  => password_hash($password, PASSWORD_DEFAULT),
        ]);
        return ['ok' => true, 'message' => 'Conta actualizada.'];
    }

    $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, role, created_at) VALUES (:email, :hash, :role, :ts)');
    $stmt->execute([
        ':email' => mb_strtolower(trim($email)),
        ':hash' => password_hash($password, PASSWORD_DEFAULT),
        ':role' => 'writer',
        ':ts' => gmdate('Y-m-d H:i:s'),
    ]);

    return ['ok' => true, 'message' => 'Writer criado.'];
}

