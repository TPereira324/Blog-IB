<?php
$gradients = [
    'linear-gradient(135deg,#1a3d21 0%,#2d6a34 100%)',
    'linear-gradient(135deg,#2d4a2e 0%,#3d7a46 100%)',
    'linear-gradient(135deg,#0f2a14 0%,#245c2a 100%)',
    'linear-gradient(135deg,#1e4d24 0%,#4a8c52 100%)',
];
?>

<div class="main-layout" style="padding-top:2.5rem;">

    <!-- Articles column -->
    <div class="articles-col">
        <div class="page-header-mag">
            <h1>All Essays</h1>
            <span class="essays-count"><?= count($articles) === 1 ? '1 piece' : count($articles) . ' pieces' ?></span>
        </div>

        <?php if (empty($articles)): ?>
            <div class="empty-state" style="margin-top:2rem;">
                <p>The press is warming up.</p>
                <p>No articles published yet — check back soon.</p>
            </div>
        <?php else: ?>
            <?php $featured = $articles[0]; $rest = array_slice($articles, 1); ?>

            <a class="card-large" href="<?= $base ?>/article?id=<?= (int)$featured['id'] ?>">
                <div class="card-large-img" style="background:<?= $gradients[0] ?>">
                    <span class="card-tag">Essay</span>
                </div>
                <div class="card-large-body">
                    <h2><?= e((string)$featured['title']) ?></h2>
                    <p class="card-excerpt"><?= e(mb_strimwidth((string)$featured['excerpt'], 0, 180, '…')) ?></p>
                    <div class="card-meta">
                        <span class="card-author">by The Journal</span>
                        <span class="card-dot">·</span>
                        <span class="card-date"><?= e(date('F j, Y', strtotime((string)$featured['published_at']))) ?></span>
                    </div>
                </div>
            </a>

            <?php if (!empty($rest)): ?>
            <div class="card-grid">
                <?php foreach ($rest as $i => $art): ?>
                <a class="card-small" href="<?= $base ?>/article?id=<?= (int)$art['id'] ?>">
                    <div class="card-small-img" style="background:<?= $gradients[($i + 1) % count($gradients)] ?>">
                        <span class="card-tag">Essay</span>
                    </div>
                    <div class="card-small-body">
                        <h3><?= e((string)$art['title']) ?></h3>
                        <p class="card-excerpt"><?= e(mb_strimwidth((string)$art['excerpt'], 0, 100, '…')) ?></p>
                        <div class="card-meta">
                            <span class="card-date"><?= e(date('M j, Y', strtotime((string)$art['published_at']))) ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-section sidebar-profile">
            <div class="sidebar-avatar">J</div>
            <h3>The Journal.</h3>
            <p>A space for slow, intentional writing. Essays on life, ideas, and what it means to pay attention.</p>
            <div class="sidebar-sig">— Est. 2026</div>
        </div>

        <div class="sidebar-section" id="newsletter">
            <h4 class="sidebar-section-title">Best of the Journal<br><em>Straight to Your Inbox</em></h4>
            <form class="sidebar-nl-form" action="<?= $base ?>/newsletter" method="post" onsubmit="handleNewsletter(event)">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <input type="email" name="email" id="nl-email" placeholder="Enter your email" required>
                <button type="submit">SUBSCRIBE</button>
            </form>
        </div>

        <?php if (!empty($articles)): ?>
        <div class="sidebar-section">
            <h4 class="sidebar-section-title">Latest Essays</h4>
            <ol class="sidebar-popular">
                <?php foreach (array_slice($articles, 0, 4) as $i => $art): ?>
                <li>
                    <a href="<?= $base ?>/article?id=<?= (int)$art['id'] ?>">
                        <span class="pop-num"><?= $i + 1 ?></span>
                        <span class="pop-title"><?= e((string)$art['title']) ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <?php endif; ?>
    </aside>
</div>

<script>
function handleNewsletter(e) {
    e.preventDefault();
    document.getElementById('nl-email').value = '';
    showToast('Subscrito. Bem-vindo ao Journal.');
}
</script>
