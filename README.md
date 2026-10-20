# Library Management System (LMS)

A beginner-friendly single-branch Library Management System built with plain PHP MVC, PDO, MySQL-compatible SQL, and a responsive CSS interface. This iteration includes the project skeleton plus the first authentication and authorization flow.

## 1. Requirements

- Windows with XAMPP (Apache, PHP 8.2+, and MySQL/MariaDB)
- Composer 2
- A browser
- Optional: Git

> XAMPP commonly bundles MariaDB rather than Oracle MySQL. The project uses MySQL 8.x-compatible SQL features that are supported by current MariaDB releases. If a SQL import fails, check the database version shown on the phpMyAdmin home page.

## 2. Put the project in XAMPP

Extract the ZIP to `C:\xampp\htdocs\library-app`. Make sure `composer.json`, `app`, `bin`, `database`, and `public` are directly inside that folder.

Start Apache and MySQL from XAMPP Control Panel, then open `http://localhost/phpmyadmin/`.

Local URLs:

- `http://localhost/library-app/public/` — home
- `http://localhost/library-app/public/login` — sign in
- `http://localhost/library-app/public/dashboard` — requires a signed-in account
- `http://localhost/library-app/public/admin` — requires the `admin` role

For production, configure Apache's `DocumentRoot` to the project's `public` directory. The XAMPP subfolder layout is for local development only.

## 3. Create or update the database

In phpMyAdmin, create a database named `library_management` using `utf8mb4` (for example, `utf8mb4_unicode_ci`). Select it and import these files in order:

1. `database/schema.sql`
2. `database/seed.sql`

**If you already imported the original schema and seed data in the previous step:** do not import `schema.sql` again because the base tables already exist. Instead, select `library_management`, open the **Import** tab, and import `database/migrations/001_create_login_attempts.sql`. That migration adds the table used to rate-limit repeated login attempts.

The seed file adds demo categories, book titles, physical copies, member profiles, and system settings. It intentionally does not contain a default login or password.

## 4. Configure `.env`

Copy `.env.example` to `.env` in the project root. Ensure the file is named `.env`, not `.env.txt`.

Typical local XAMPP defaults are:

```dotenv
APP_NAME="Library Management System"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=Asia/Manila

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_management
DB_USERNAME=root
DB_PASSWORD=
```

Adjust these values if your local database uses another port or password. These defaults are for local development only. Never use an unrestricted database root account in production, and never commit `.env` to Git.

## 5. Install Composer dependencies

Open PowerShell or Command Prompt:

```powershell
cd C:\xampp\htdocs\library-app
C:\xampp\php\php.exe -v
composer --version
composer install
```

If `php` is not recognized, use `C:\xampp\php\php.exe` or add `C:\xampp\php` to Windows PATH. Composer must use XAMPP's PHP installation. If PDO reports `could not find driver`, enable `pdo_mysql` in XAMPP's `php.ini`, then restart Apache.

## 6. Create the first administrator account

No default admin credentials are included. From the project folder, run:

```powershell
C:\xampp\php\php.exe bin/create-admin.php
```

The command asks for the administrator's full name, email, and a password of at least 12 characters, then stores only a password hash. For safety, it refuses to run if an admin already exists. Do not put the password in the command itself or run this on a shared screen; some Windows terminals visibly echo input typed at password prompts.

Then open `http://localhost/library-app/public/login` and sign in with that account. The `/admin` page demonstrates a server-side admin-only route.

## 7. Verify the app and tests

1. Visit `/` and confirm the LibraryDesk home page appears.
2. Visit `/login` and confirm the sign-in form appears.
3. Create the administrator using the CLI step above.
4. Sign in; you should be redirected to `/dashboard`.
5. Open `/admin` to confirm the admin-only screen is accessible.
6. Click **Sign out**. The action uses POST with a CSRF token.
7. Try opening `/dashboard` after signing out; it should redirect to sign in.

Run the automated unit tests after Composer has installed the dev dependencies:

```powershell
composer test
```

The initial tests cover CSRF token creation, validation, and regeneration. We will add service and integration tests as circulation and other business modules are introduced.

## 8. What's included in authentication

- Password hashing prefers Argon2id when the installed PHP build supports it, with PHP's default algorithm as a fallback; logins use `password_verify()` and hashes are rehashed when needed
- Session ID regeneration after successful login
- HTTP-only session cookies, `SameSite=Lax`, strict session mode, and `Secure` cookies when the app uses HTTPS
- CSRF validation for login and logout POST requests
- Generic login failure messages, so the UI does not reveal whether an account exists or is inactive
- Login-attempt throttling: up to five failed attempts for an email/IP pair within a 15-minute window
- A 30-minute inactivity timeout on authenticated routes
- Authentication middleware for protected pages and role middleware for the admin-only route
- Prepared statements for user queries, output escaping, and security headers
- An audit record for initial administrator creation

Rate limiting here is an application-level baseline, not a replacement for web-server/WAF controls or monitoring on an internet-facing deployment. Before production, review and test the security configuration in the actual environment.

## 9. Project layout

```text
app/
  Controllers/     Request handling
  Core/            Router, PDO, session auth, CSRF
  Helpers/         HTML escaping, URLs, flash messages
  Middleware/      Authentication and role checks
  Services/        Login and authentication logic
bin/               Local CLI setup commands
config/            Database configuration entry point
database/           Schema, seed data, ER diagram, migrations
public/             Web document entry point and CSS
routes/             URL-to-controller mappings
storage/            Private logs and uploads
tests/              PHPUnit unit tests
views/              PHP templates and shared layout
```

## 10. Current limitations and next steps

Authentication is now functional once the database, migration, Composer dependencies, and admin account are set up. The catalog, member CRUD, borrowing/return service, fine-payment workflow, reservations, reports, upload handling, and deployment runbook are not yet implemented. The starter is for local development and should not be used with real patron data until those modules are implemented and appropriately tested.

Recommended next milestones:

1. Add tests for authentication and role-based access.
2. Build catalog and physical-copy management.
3. Build member management.
4. Implement transactional borrow/return/renewal services.
5. Add fine assessment and payment history, dashboard reports, and hardening before deployment.
