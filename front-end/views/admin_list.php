<div class="admin-section">
    <div class="admin-toolbar">
        <h1 style="font-family:var(--serif);font-size:2rem;font-weight:400;letter-spacing:-0.02em;">Painel</h1>
        <div style="display:flex;gap:0.75rem;align-items:center;">
            <a class="btn-outline" href="<?= $base ?>/admin/write">Novo ensaio</a>
            <form method="post" action="<?= $base ?>/logout" style="margin:0;">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <button type="submit" class="btn-outline">Sign out</button>
            </form>
        </div>
    </div>

    <?php if (empty($articles)): ?>
        <div class="empty-state">
            <p>Ainda não há ensaios.</p>
            <p>Começa a escrever o primeiro.</p>
        </div>
    <?php else: ?>
        <?php foreach ($articles as $art): ?>
            <div class="admin-article-row">
                <div style="flex:1;min-width:0;">
                    <p class="admin-article-title"><?= e((string)$art['title']) ?></p>
                    <p class="admin-article-meta">
                        <?= e(date('j M Y', strtotime((string)$art['created_at']))) ?>
                        &nbsp;·&nbsp;
                        <span class="status-badge status-badge-<?= e((string)$art['status']) ?>">
                            <?= $art['status'] === 'published' ? 'Publicado' : 'Rascunho' ?>
                        </span>
                    </p>
                </div>

                <div class="admin-actions">
                    <a class="btn-sm" href="<?= $base ?>/admin/edit?id=<?= (int)$art['id'] ?>">Editar</a>

                    <?php if ($art['status'] !== 'published'): ?>
                        <form method="post" action="<?= $base ?>/admin/publish" style="margin:0;">
                            <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                            <input type="hidden" name="id" value="<?= (int)$art['id'] ?>">
                            <button type="submit" class="btn-sm btn-sm-primary">Publicar</button>
                        </form>
                    <?php endif; ?>

                    <form method="post" action="<?= $base ?>/admin/delete" style="margin:0;"
                          onsubmit="return confirm('Eliminar este ensaio?')">
                        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$art['id'] ?>">
                        <button type="submit" class="btn-sm btn-sm-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
