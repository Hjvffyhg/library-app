<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\PasswordHasher;
use Dotenv\Dotenv;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit("Not found.\n");
}

$projectRoot = dirname(__DIR__);
$autoload = $projectRoot . '/vendor/autoload.php';
if (!is_file($autoload)) {
    fwrite(STDERR, "Composer dependencies are missing. Run composer install first.\n");
    exit(1);
}

require $autoload;
Dotenv::createImmutable($projectRoot)->safeLoad();

function ask(string $prompt): string
{
    fwrite(STDOUT, $prompt);
    $value = fgets(STDIN);
    return $value === false ? '' : trim($value);
}

try {
    $pdo = Database::connection();
    $existingAdmin = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    if ($existingAdmin > 0) {
        fwrite(STDERR, "An admin account already exists. For safety, this one-time setup command will not create another.\n");
        exit(1);
    }

    fwrite(STDOUT, "Create the first Library Management System administrator.\n");
    fwrite(STDOUT, "Note: password input is visible in some Windows terminals; do not run this on a shared screen.\n\n");

    $name = ask('Full name: ');
    $email = strtolower(ask('Email address: '));
    preg_match_all('/./us', $name, $nameCharacters);
    if ($name === '' || count($nameCharacters[0]) > 150) {
        throw new InvalidArgumentException('Name must be between 1 and 150 characters.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        throw new InvalidArgumentException('Enter a valid email address no longer than 190 characters.');
    }

    $password = ask('Password (minimum 12 characters): ');
    $confirm = ask('Confirm password: ');
    if (strlen($password) < 12) {
        throw new InvalidArgumentException('Use a password with at least 12 characters.');
    }
    if (strlen($password) > PasswordHasher::maxPasswordBytes()) {
        throw new InvalidArgumentException('Password is too long for the active password-hashing algorithm.');
    }
    if (!hash_equals($password, $confirm)) {
        throw new InvalidArgumentException('The passwords do not match.');
    }

    $hash = PasswordHasher::hash($password);

    $pdo->beginTransaction();
    $insert = $pdo->prepare(
        "INSERT INTO users (name, email, password_hash, role, status)
         VALUES (:name, :email, :password_hash, 'admin', 'active')"
    );
    $insert->execute(['name' => $name, 'email' => $email, 'password_hash' => $hash]);
    $userId = (int) $pdo->lastInsertId();

    // The audit table is part of the base schema. Initial setup is logged too.
    $audit = $pdo->prepare(
        "INSERT INTO audit_log (user_id, action, entity_type, entity_id, details)
         VALUES (:user_id, 'admin.initial_setup', 'user', :entity_id, JSON_OBJECT('source', 'cli'))"
    );
    $audit->execute(['user_id' => $userId, 'entity_id' => $userId]);
    $pdo->commit();

    fwrite(STDOUT, "\nAdministrator created successfully. You can now sign in at /login.\n");
} catch (Throwable $exception) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, "Setup failed: " . $exception->getMessage() . "\n");
    exit(1);
}
