<?php
declare(strict_types=1);

function articles_latest_published(int $limit): array
{
    $limit = max(1, min(50, $limit));
    $stmt = db()->prepare(
        'SELECT id, title, excerpt, published_at
         FROM articles
         WHERE status = :status AND published_at IS NOT NULL
         ORDER BY published_at DESC
         LIMIT ' . $limit
    );
    $stmt->execute([':status' => 'published']);
    return $stmt->fetchAll() ?: [];
}

function articles_list_published(): array
{
    $stmt = db()->prepare(
        'SELECT id, title, excerpt, published_at
         FROM articles
         WHERE status = :status AND published_at IS NOT NULL
         ORDER BY published_at DESC'
    );
    $stmt->execute([':status' => 'published']);
    return $stmt->fetchAll() ?: [];
}

function articles_get_published_by_id(int $id): ?array
{
    $stmt = db()->prepare(
        'SELECT a.id, a.title, a.excerpt, a.content, a.published_at, u.email AS author_email
         FROM articles a
         JOIN users u ON u.id = a.author_id
         WHERE a.id = :id AND a.status = :status AND a.published_at IS NOT NULL
         LIMIT 1'
    );
    $stmt->execute([':id' => $id, ':status' => 'published']);
    $row = $stmt->fetch();
    return $row ?: null;
}

function articles_list_all_for_admin(): array
{
    $stmt = db()->query(
        'SELECT id, title, status, created_at, updated_at, published_at
         FROM articles
         ORDER BY created_at DESC'
    );
    return $stmt->fetchAll() ?: [];
}

function articles_get_for_admin_by_id(int $id): ?array
{
    $stmt = db()->prepare('SELECT id, title, excerpt, content, status FROM articles WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function articles_create_draft(int $authorId, string $title, string $excerpt, string $content): array
{
    if ($authorId <= 0) {
        return ['ok' => false, 'message' => 'Sessão inválida.'];
    }
    $title = trim($title);
    if (mb_strlen($title) < 3) {
        return ['ok' => false, 'message' => 'Título demasiado curto.'];
    }
    if (mb_strlen($excerpt) < 10) {
        return ['ok' => false, 'message' => 'Resumo demasiado curto.'];
    }
    if (mb_strlen($content) < 30) {
        return ['ok' => false, 'message' => 'Conteúdo demasiado curto.'];
    }

    $slugBase = slugify($title);
    $slug = articles_unique_slug($slugBase);

    $stmt = db()->prepare(
        'INSERT INTO articles (author_id, title, slug, excerpt, content, status, created_at, updated_at)
         VALUES (:author_id, :title, :slug, :excerpt, :content, :status, :created_at, :updated_at)'
    );
    $now = gmdate('Y-m-d H:i:s');
    $stmt->execute([
        ':author_id' => $authorId,
        ':title' => $title,
        ':slug' => $slug,
        ':excerpt' => $excerpt,
        ':content' => $content,
        ':status' => 'draft',
        ':created_at' => $now,
        ':updated_at' => $now,
    ]);

    return ['ok' => true, 'message' => 'Rascunho criado.'];
}

function articles_update_draft(int $id, string $title, string $excerpt, string $content): array
{
    $title = trim($title);
    if ($id <= 0) {
        return ['ok' => false, 'message' => 'Artigo inválido.'];
    }
    if (mb_strlen($title) < 3) {
        return ['ok' => false, 'message' => 'Título demasiado curto.'];
    }
    if (mb_strlen($excerpt) < 10) {
        return ['ok' => false, 'message' => 'Resumo demasiado curto.'];
    }
    if (mb_strlen($content) < 30) {
        return ['ok' => false, 'message' => 'Conteúdo demasiado curto.'];
    }

    $existing = articles_get_for_admin_by_id($id);
    if ($existing === null) {
        return ['ok' => false, 'message' => 'Artigo não encontrado.'];
    }

    $slugBase = slugify($title);
    $slug = (string)($existing['title'] === $title ? (string)db()->query("SELECT slug FROM articles WHERE id = " . (int)$id)->fetchColumn() : articles_unique_slug($slugBase, $id));

    $stmt = db()->prepare(
        'UPDATE articles
         SET title = :title, slug = :slug, excerpt = :excerpt, content = :content, updated_at = :updated_at
         WHERE id = :id'
    );
    $stmt->execute([
        ':id' => $id,
        ':title' => $title,
        ':slug' => $slug,
        ':excerpt' => $excerpt,
        ':content' => $content,
        ':updated_at' => gmdate('Y-m-d H:i:s'),
    ]);

    return ['ok' => true, 'message' => 'Rascunho atualizado.'];
}

function articles_publish(int $id): void
{
    $stmt = db()->prepare(
        'UPDATE articles
         SET status = :status, published_at = COALESCE(published_at, :ts), updated_at = :ts
         WHERE id = :id'
    );
    $ts = gmdate('Y-m-d H:i:s');
    $stmt->execute([
        ':id' => $id,
        ':status' => 'published',
        ':ts' => $ts,
    ]);
}

function articles_delete(int $id): void
{
    $stmt = db()->prepare('DELETE FROM articles WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

function articles_unique_slug(string $base, ?int $ignoreId = null): string
{
    $base = $base !== '' ? $base : 'artigo';
    $slug = $base;
    $i = 1;

    while (articles_slug_exists($slug, $ignoreId)) {
        $i++;
        $slug = $base . '-' . $i;
        if ($i > 200) {
            $slug = $base . '-' . bin2hex(random_bytes(3));
            break;
        }
    }

    return $slug;
}

function articles_slug_exists(string $slug, ?int $ignoreId = null): bool
{
    if ($ignoreId !== null) {
        $stmt = db()->prepare('SELECT 1 FROM articles WHERE slug = :slug AND id <> :id LIMIT 1');
        $stmt->execute([':slug' => $slug, ':id' => $ignoreId]);
    } else {
        $stmt = db()->prepare('SELECT 1 FROM articles WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => $slug]);
    }
    return (bool)$stmt->fetchColumn();
}

