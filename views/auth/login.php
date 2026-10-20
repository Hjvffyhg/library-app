<section class="auth-wrap">
    <div class="auth-card">
        <p class="eyebrow">STAFF AND MEMBER ACCESS</p>
        <h1>Sign in</h1>
        <p class="muted">Use the email address and password for your library account.</p>

        <form method="post" action="<?= e(url('login')) ?>" class="form-stack" autocomplete="on">
            <?= csrf_field() ?>
            <div class="form-field">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" maxlength="190" autocomplete="username" required autofocus>
            </div>
            <div class="form-field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" maxlength="4096" required>
            </div>
            <button class="button button-primary button-full" type="submit">Sign in</button>
        </form>
        <p class="muted small-text">Need an account? Ask your library administrator. Public sign-up is not enabled.</p>
    </div>
</section>
