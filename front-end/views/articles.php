<div class="page-header" style="padding-bottom: 2rem;">
    <h1>All essays</h1>
</div>

<hr class="divider">

<section class="essays-section">
    <div class="essays-header">
        <h2>Published pieces</h2>
        <span class="essays-count">
            <?= count($articles) === 1 ? '1 piece' : count($articles) . ' pieces' ?>
        </span>
    </div>

    <?php if (empty($articles)): ?>
        <div class="empty-state">
            <p>The press is warming up.</p>
            <p>No articles published yet — check back soon.</p>
        </div>
    <?php else: ?>
        <?php foreach ($articles as $art): ?>
            <div class="article-card" onclick="window.location.href='<?= $base ?>/article?id=<?= (int)$art['id'] ?>'">
                <div class="article-meta">
                    <span class="article-date"><?= e(date('j F Y', strtotime((string)$art['published_at']))) ?></span>
                </div>
                <h3 class="article-title"><?= e((string)$art['title']) ?></h3>
                <p class="article-excerpt"><?= e(mb_strimwidth((string)$art['excerpt'], 0, 160, '…')) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<div style="flex: 1;"></div>
