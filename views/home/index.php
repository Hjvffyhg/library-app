<section class="hero">
    <p class="eyebrow">PROJECT STARTER</p>
    <h1>Your library, organized.</h1>
    <p class="lead">The foundation for managing book titles, physical copies, members, borrowing, returns, and reports.</p>
    <div class="hero-actions">
        <a class="button button-primary" href="<?= e(url('/login')) ?>">Open sign-in page</a>
        <span class="status"><span class="status-dot"></span> Routing is working</span>
    </div>
</section>

<section class="section-heading">
    <p class="eyebrow">BUILD PLAN</p>
    <h2>Core modules</h2>
    <p class="muted">We’ll implement these one at a time, with database rules and tests behind each workflow.</p>
</section>

<div class="feature-grid">
    <article class="feature-card"><div class="feature-number">01</div><h3>Catalog</h3><p>Book details, categories, copies, and barcode inventory.</p><span class="tag">Planned</span></article>
    <article class="feature-card"><div class="feature-number">02</div><h3>Members</h3><p>Member codes, contact details, account status, and history.</p><span class="tag">Planned</span></article>
    <article class="feature-card"><div class="feature-number">03</div><h3>Circulation</h3><p>Safe borrowing, returns, renewals, and due dates.</p><span class="tag">Planned</span></article>
    <article class="feature-card"><div class="feature-number">04</div><h3>Reports</h3><p>Overdue books, most borrowed titles, fine summaries, and exports.</p><span class="tag">Planned</span></article>
</div>

<div class="notice notice-info">
    <strong>Current milestone:</strong> project foundation and sign-in flow. Create the first admin account from the command line before signing in. Catalog, member-management, and circulation modules will be added next.
</div>
