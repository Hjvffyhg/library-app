<section class="page-heading">
    <p class="eyebrow">YOUR WORKSPACE</p>
    <h1>Welcome, <?= e($user['name']) ?>.</h1>
    <p class="muted">You are signed in as <strong><?= e(ucfirst($user['role'])) ?></strong>. Library modules will appear here as we build them.</p>
</section>
<section class="card-grid">
    <article class="feature-card">
        <h2>Book catalog</h2>
        <p>Manage titles and physical copies, and search the collection.</p>
        <span class="status-pill">Coming next</span>
    </article>
    <article class="feature-card">
        <h2>Members</h2>
        <p>Maintain member records and membership status.</p>
        <span class="status-pill">Planned</span>
    </article>
    <article class="feature-card">
        <h2>Circulation</h2>
        <p>Issue, return, and renew books while keeping a reliable loan history.</p>
        <span class="status-pill">Planned</span>
    </article>
</section>
