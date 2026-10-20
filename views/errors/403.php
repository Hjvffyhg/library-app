<?php
/** @var string $title */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Access denied') ?></title>
    <link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>">
</head>
<body>
<main class="container main-content">
    <section class="auth-card">
        <p class="eyebrow">403 · FORBIDDEN</p>
        <h1>Access denied</h1>
        <p class="muted">Your account does not have permission to access this page.</p>
        <a class="button button-primary" href="<?= e(url('/')) ?>">Return home</a>
    </section>
</main>
</body>
</html>
