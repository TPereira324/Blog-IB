<div class="auth-page">
    <div class="auth-box">
        <h1>Welcome back</h1>
        <p class="auth-sub">Admin access — to write &amp; publish.</p>

        <form method="post" action="<?= $base ?>/login">
            <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
            <input class="auth-field" type="email" name="email" placeholder="Email" required autofocus>
            <input class="auth-field" type="password" name="password" placeholder="Password" required>
            <button class="btn-auth" type="submit">Sign In</button>
        </form>

        <p class="auth-signup">No account? <a href="#">Sign up</a></p>
        <a class="back-to-journal" href="<?= $base ?>/">← Back to journal</a>
    </div>
</div>
