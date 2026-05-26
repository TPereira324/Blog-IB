<?php
$featured = !empty($articles) ? $articles[0] : null;
$rest     = !empty($articles) ? array_slice($articles, 1) : [];
$gradients = [
    'linear-gradient(135deg,#1a3d21 0%,#2d6a34 100%)',
    'linear-gradient(135deg,#2d4a2e 0%,#3d7a46 100%)',
    'linear-gradient(135deg,#0f2a14 0%,#245c2a 100%)',
    'linear-gradient(135deg,#1e4d24 0%,#4a8c52 100%)',
];
?>

<!-- ── Hero Featured ── -->
<section class="hero-featured" style="<?= $featured ? 'background:linear-gradient(160deg,#0f2a14 0%,#1a3d21 55%,#2d6a34 100%)' : 'background:linear-gradient(160deg,#0f2a14 0%,#1a3d21 60%,#2d6a34 100%)' ?>">
    <div class="hero-featured-inner">
        <?php if ($featured): ?>
            <span class="hero-feat-tag">Featured</span>
            <h1 class="hero-feat-title"><?= e((string)$featured['title']) ?></h1>
            <div class="hero-feat-meta">
                <span>by The Journal</span>
                <span class="hero-feat-dot">·</span>
                <span><?= e(date('F j, Y', strtotime((string)$featured['published_at']))) ?></span>
            </div>
            <a class="hero-feat-read" href="<?= $base ?>/article?id=<?= (int)$featured['id'] ?>">Read the essay →</a>
        <?php else: ?>
            <span class="hero-feat-tag">Est. 2026</span>
            <h1 class="hero-feat-title">Slow thoughts,<br><em>printed with intent.</em></h1>
            <p class="hero-feat-sub">A quiet corner for essays that wait, breathe,<br>and arrive only when they are ready.</p>
        <?php endif; ?>
    </div>
    <div class="hero-featured-overlay"></div>
</section>

<!-- ── Quick bar ── -->
<div class="quick-bar">
    <div class="quick-bar-item">
        <div class="quick-bar-thumb" style="background:linear-gradient(135deg,#2d6a34,#88a38d)">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="#f4f8f4" opacity="0.9"><path d="M12 2a10 10 0 100 20A10 10 0 0012 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        </div>
        <div class="quick-bar-text">
            <h4>About the Journal</h4>
            <p>Get to know the story behind the writing. <a href="<?= $base ?>/articles">Read more</a></p>
        </div>
    </div>
    <div class="quick-bar-item">
        <div class="quick-bar-thumb quick-bar-thumb--ig">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
        </div>
        <div class="quick-bar-text">
            <h4>Instagram</h4>
            <p>Find more moments and thoughts. <a href="https://www.instagram.com/isabel.bgtdi" target="_blank" rel="noopener">@isabel.bgtdi</a></p>
        </div>
    </div>
    <div class="quick-bar-item">
        <div class="quick-bar-thumb quick-bar-thumb--tt">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="white"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.79 1.53V6.78a4.85 4.85 0 01-1.02-.09z"/></svg>
        </div>
        <div class="quick-bar-text">
            <h4>TikTok</h4>
            <p>Vídeos e ideias no dia a dia. <a href="https://www.tiktok.com/@isabel.bgtdi" target="_blank" rel="noopener">@isabel.bgtdi</a></p>
        </div>
    </div>
</div>

<!-- ── Main layout ── -->
<div class="main-layout">

    <!-- Articles column -->
    <div class="articles-col">
        <?php if (empty($articles)): ?>
            <div class="empty-state" style="margin-top:2rem;">
                <p>The press is warming up.</p>
                <p>No articles published yet — check back soon.</p>
            </div>
        <?php else: ?>

            <?php if ($featured): ?>
            <!-- Featured large card -->
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
            <?php endif; ?>

            <!-- Small cards grid -->
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

        <!-- Author profile -->
        <div class="sidebar-section sidebar-profile">
            <div class="sidebar-avatar">J</div>
            <h3>The Journal.</h3>
            <p>A space for slow, intentional writing. Essays on life, ideas, and what it means to pay attention.</p>
            <div class="sidebar-sig">— Est. 2026</div>
            <div class="sidebar-socials">
                <a class="social-btn" href="https://www.instagram.com/isabel.bgtdi" target="_blank" rel="noopener">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    Instagram
                </a>
                <a class="social-btn" href="https://www.tiktok.com/@isabel.bgtdi" target="_blank" rel="noopener">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.79 1.53V6.78a4.85 4.85 0 01-1.02-.09z"/></svg>
                    TikTok
                </a>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="sidebar-section" id="newsletter">
            <h4 class="sidebar-section-title">Best of the Journal<br><em>Straight to Your Inbox</em></h4>
            <form class="sidebar-nl-form" action="<?= $base ?>/newsletter" method="post" onsubmit="handleNewsletter(event)">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <input type="email" name="email" id="nl-email" placeholder="Enter your email" required>
                <button type="submit">SUBSCRIBE</button>
            </form>
        </div>

        <!-- Popular / latest -->
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
