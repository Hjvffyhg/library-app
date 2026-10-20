<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Single-branch Library Management System starter project">
    <title><?= e($title) ?> | Library Management System</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>">
</head>
<body>
    <?php $navigationUser = current_user(); ?>
    <header class="site-header">
        <div class="container nav-row">
            <a class="brand" href="<?= e(url('/')) ?>">Library<span>Desk</span></a>
            <nav aria-label="Main navigation">
                <a href="<?= e(url('/')) ?>">Home</a>
                <?php if ($navigationUser !== null): ?>
                    <a href="<?= e(url('dashboard')) ?>">Dashboard</a>
                    <?php if ($navigationUser['role'] === 'admin'): ?>
                        <a href="<?= e(url('admin')) ?>">Admin</a>
                    <?php endif; ?>
                    <form method="post" action="<?= e(url('logout')) ?>" class="nav-form">
                        <?= csrf_field() ?>
                        <button class="nav-link-button" type="submit">Sign out</button>
                    </form>
                <?php else: ?>
                    <a href="<?= e(url('login')) ?>">Sign in</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container main-content">
        <?php foreach (['success', 'error', 'info'] as $flashType): ?>
            <?php $flashMessage = take_flash($flashType); ?>
            <?php if ($flashMessage !== null): ?>
                <div class="notice notice-<?= e($flashType) ?>" role="status"><?= e($flashMessage) ?></div>
            <?php endif; ?>
        <?php endforeach; ?>
        <?= $content ?>
    </main>

    <footer class="site-footer">
        <div class="container">Library Management System · Development starter · <?= e(date('Y')) ?></div>
    </footer>
</body>
</html>
