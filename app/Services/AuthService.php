<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Core\PasswordHasher;
use PDO;

final class AuthService
{
    private const MAX_ATTEMPTS = 5;
    private const WINDOW_MINUTES = 15;

    public function __construct(private readonly ?PDO $pdo = null)
    {
    }

    private function db(): PDO
    {
        return $this->pdo ?? Database::connection();
    }

    /**
     * Authenticate an active account. The result is deliberately boolean so the
     * UI never reveals whether an email exists, is locked, or has a bad password.
     */
    public function attempt(string $email, string $password, string $ipAddress): bool
    {
        $normalizedEmail = strtolower(trim($email));
        $emailHash = hash('sha256', $normalizedEmail);
        $ipAddress = substr($ipAddress, 0, 45);
        $pdo = $this->db();

        // Opportunistically remove old rows to keep this small table bounded.
        $pdo->exec('DELETE FROM login_attempts WHERE attempted_at < UTC_TIMESTAMP() - INTERVAL 1 DAY');

        $limit = $pdo->prepare(
            'SELECT COUNT(*) FROM login_attempts
             WHERE email_hash = :email_hash
               AND ip_address = :ip_address
               AND attempted_at >= UTC_TIMESTAMP() - INTERVAL ' . self::WINDOW_MINUTES . ' MINUTE'
        );
        $limit->execute(['email_hash' => $emailHash, 'ip_address' => $ipAddress]);

        if ((int) $limit->fetchColumn() >= self::MAX_ATTEMPTS) {
            // Avoid allowing unlimited cheap password checks for a throttled key.
            return false;
        }

        $query = $pdo->prepare(
            'SELECT id, name, email, password_hash, role, status
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $query->execute(['email' => $normalizedEmail]);
        $user = $query->fetch();

        // Perform a password hash verification even for unknown accounts to make
        // the most obvious timing-based email enumeration harder.
        if (!is_array($user)) {
            PasswordHasher::verify($password, PasswordHasher::dummyHash());
        }

        $validPassword = is_array($user) && PasswordHasher::verify($password, (string) $user['password_hash']);
        $activeAccount = is_array($user) && $user['status'] === 'active';

        if (!$validPassword || !$activeAccount) {
            $insert = $pdo->prepare(
                'INSERT INTO login_attempts (email_hash, ip_address, attempted_at)
                 VALUES (:email_hash, :ip_address, UTC_TIMESTAMP())'
            );
            $insert->execute(['email_hash' => $emailHash, 'ip_address' => $ipAddress]);
            return false;
        }

        $deleteAttempts = $pdo->prepare(
            'DELETE FROM login_attempts WHERE email_hash = :email_hash AND ip_address = :ip_address'
        );
        $deleteAttempts->execute(['email_hash' => $emailHash, 'ip_address' => $ipAddress]);

        if (PasswordHasher::needsRehash((string) $user['password_hash'])) {
            $newHash = PasswordHasher::hash($password);
            $updateHash = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
            $updateHash->execute(['hash' => $newHash, 'id' => $user['id']]);
        }

        $lastLogin = $pdo->prepare('UPDATE users SET last_login_at = UTC_TIMESTAMP() WHERE id = :id');
        $lastLogin->execute(['id' => $user['id']]);

        $audit = $pdo->prepare(
            "INSERT INTO audit_log (user_id, action, entity_type, entity_id, ip_address, user_agent, details)
             VALUES (:user_id, 'auth.login', 'user', :entity_id, :ip_address, :user_agent, JSON_OBJECT('method', 'password'))"
        );
        $audit->execute([
            'user_id' => $user['id'],
            'entity_id' => $user['id'],
            'ip_address' => $ipAddress,
            'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
        ]);

        Auth::login($user);
        return true;
    }
}
