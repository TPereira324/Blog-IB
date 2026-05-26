<?php
$words = str_word_count(strip_tags((string)$article['content']));
$readMins = max(1, (int)round($words / 200));
?>

<div class="article-page">
    <a class="back-link" href="<?= $base ?>/articles">← Back to essays</a>

    <h1><?= e((string)$article['title']) ?></h1>

    <div class="article-page-meta">
        <span><?= e(date('j F Y', strtotime((string)$article['published_at']))) ?></span>
        <span><?= $readMins ?> min read</span>
    </div>

    <div class="article-body">
        <?= nl2p((string)$article['content']) ?>
    </div>
</div>
