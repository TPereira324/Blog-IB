<section class="hero">
    <p class="hero-kicker">Volume I &nbsp;·&nbsp; Est. 2026</p>
    <h1 class="hero-title">Slow thoughts,<br><em>printed with intent.</em></h1>
    <p class="hero-sub">A quiet corner of the internet for essays that wait, breathe, and arrive only when they are ready.</p>
</section>

<hr class="divider">

<section class="essays-section">
    <div class="essays-header">
        <h2>Latest essays</h2>
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

<hr class="divider" style="margin-top: 2rem;">

<section class="newsletter-section">
    <p class="newsletter-kicker">The Newsletter</p>
    <h2 class="newsletter-title">Receive each new piece <em>in your inbox.</em></h2>
    <p class="newsletter-sub">No spam, no schedule — only the essay, the moment it's published.</p>
    <form class="newsletter-form" action="<?= $base ?>/newsletter" method="post" onsubmit="handleNewsletter(event)">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <input type="email" name="email" placeholder="you@example.com" id="nl-email" required>
        <button type="submit" class="btn-subscribe">Subscribe</button>
    </form>
</section>

<hr class="divider">
<div style="height: 2rem;"></div>

<script>
function handleNewsletter(e) {
    e.preventDefault();
    const email = document.getElementById('nl-email').value.trim();
    if (!email) return;
    document.getElementById('nl-email').value = '';
    showToast('Subscrito. Bem-vindo ao Journal.');
}
</script>
