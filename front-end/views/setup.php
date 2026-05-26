<div class="auth-page">
    <div class="auth-box">
        <h1>Configuração inicial</h1>
        <p class="auth-sub">Cria a conta de escritor do Journal.</p>

        <form method="post" action="<?= $base ?>/setup">
            <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
            <input class="auth-field" type="text"     name="setup_key" placeholder="Setup key"       required autofocus>
            <input class="auth-field" type="email"    name="email"     placeholder="Email"            required>
            <input class="auth-field" type="password" name="password"  placeholder="Password (mín. 12 caracteres)" required>
            <button class="btn-auth" type="submit">Criar conta</button>
        </form>

        <a class="back-to-journal" href="<?= $base ?>/">← Voltar ao Journal</a>
    </div>
</div>
