<?php
$base = rtrim((string)app_config('app.base_url', ''), '/');
?><!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — The Journal.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500;1,600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/front-end/css/styles.css">
</head>
<body>

<div style="display:flex;flex-direction:column;min-height:100vh;">

    <nav>
        <a class="logo" href="<?= $base ?>/">The Journal<span>.</span></a>
        <ul class="nav-links">
            <li><a href="<?= $base ?>/articles">Articles</a></li>
            <li>
                <?php if ($userLoggedIn): ?>
                    <a href="<?= $base ?>/admin">Write</a>
                <?php else: ?>
                    <a href="<?= $base ?>/login">Write</a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>

    <?php if (!empty($flash) && is_array($flash)): ?>
        <div class="flash flash-<?= e((string)($flash['type'] ?? 'info')) ?>">
            <?= e((string)($flash['message'] ?? '')) ?>
        </div>
    <?php endif; ?>

    <?php require $viewFile; ?>

    <footer>
        <p>The Journal — published with care.</p>
        <span>© <?= date('Y') ?></span>
    </footer>

</div>

<div class="toast" id="toast"></div>
<script>
function showToast(msg) {
    const t = document.getElementById('toast');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}
</script>

</body>
</html>
