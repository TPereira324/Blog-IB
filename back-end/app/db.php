<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn  = (string)app_config('db.dsn',  '');
    $user = (string)app_config('db.user', '');
    $pass = (string)app_config('db.pass', '');

    if ($dsn === '') {
        throw new RuntimeException('Base de dados não configurada. Define DATABASE_URL ou DB_HOST/DB_NAME/DB_USER/DB_PASS.');
    }

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    return $pdo;
}

function db_is_pgsql(): bool
{
    return str_starts_with((string)app_config('db.dsn', ''), 'pgsql:');
}

function db_ensure_schema(): void
{
    $pdo = db();

    if (db_is_pgsql()) {
        // ── PostgreSQL ──────────────────────────────────────────────────
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id            SERIAL PRIMARY KEY,
                email         VARCHAR(190) NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                role          VARCHAR(50)  NOT NULL,
                created_at    TIMESTAMP    NOT NULL,
                last_login_at TIMESTAMP    NULL,
                CONSTRAINT uniq_users_email UNIQUE (email)
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS articles (
                id           SERIAL PRIMARY KEY,
                author_id    INTEGER      NOT NULL,
                title        VARCHAR(200) NOT NULL,
                slug         VARCHAR(220) NOT NULL,
                excerpt      TEXT         NOT NULL,
                content      TEXT         NOT NULL,
                status       VARCHAR(20)  NOT NULL,
                published_at TIMESTAMP    NULL,
                created_at   TIMESTAMP    NOT NULL,
                updated_at   TIMESTAMP    NOT NULL,
                CONSTRAINT uniq_articles_slug UNIQUE (slug),
                CONSTRAINT fk_articles_author
                    FOREIGN KEY (author_id) REFERENCES users(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            )
        ");

        $pdo->exec("
            CREATE INDEX IF NOT EXISTS idx_articles_status_published
            ON articles(status, published_at)
        ");
    } else {
        // ── MySQL ────────────────────────────────────────────────────────
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
                email         VARCHAR(190)  NOT NULL,
                password_hash VARCHAR(255)  NOT NULL,
                role          VARCHAR(50)   NOT NULL,
                created_at    DATETIME      NOT NULL,
                last_login_at DATETIME      NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_users_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS articles (
                id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
                author_id    INT UNSIGNED NOT NULL,
                title        VARCHAR(200) NOT NULL,
                slug         VARCHAR(220) NOT NULL,
                excerpt      TEXT         NOT NULL,
                content      MEDIUMTEXT   NOT NULL,
                status       VARCHAR(20)  NOT NULL,
                published_at DATETIME     NULL,
                created_at   DATETIME     NOT NULL,
                updated_at   DATETIME     NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_articles_slug (slug),
                KEY idx_articles_status_published (status, published_at),
                CONSTRAINT fk_articles_author
                    FOREIGN KEY (author_id) REFERENCES users(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
}
