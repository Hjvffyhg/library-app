<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Database;

final class AuthMiddleware
{
    /** Redirect unauthenticated requests to the login page. */
    public static function requireAuthenticated(): array
    {
        $user = Auth::user();
        $lastActivity = (int) ($_SESSION['_last_activity'] ?? time());
        if ($user !== null && time() - $lastActivity > 1800) {
            Auth::logout();
            session_id('');
            session_start();
            set_flash('error', 'You were signed out after 30 minutes of inactivity. Please sign in again.');
            header('Location: ' . url('login'), true, 303);
            exit;
        }

        if ($user === null) {
            $_SESSION['_intended_path'] = $_SERVER['REQUEST_URI'] ?? '/dashboard';
            header('Location: ' . url('login'), true, 303);
            exit;
        }

        // Re-check the account on protected requests so disabled accounts or role
        // changes take effect without waiting for the session cookie to expire.
        $statement = Database::connection()->prepare(
            'SELECT name, email, role, status FROM users WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $user['id']]);
        $account = $statement->fetch();
        if (!is_array($account) || $account['status'] !== 'active') {
            Auth::logout();
            session_id('');
            session_start();
            set_flash('error', 'Your account is inactive or no longer available. Please contact the library administrator.');
            header('Location: ' . url('login'), true, 303);
            exit;
        }

        $_SESSION['user']['name'] = (string) $account['name'];
        $_SESSION['user']['email'] = (string) $account['email'];
        $_SESSION['user']['role'] = (string) $account['role'];
        $_SESSION['_last_activity'] = time();
        return Auth::user() ?? $user;
    }

    /** Return false and render 403 if the authenticated user's role is disallowed. */
    public static function requireRoles(array $allowedRoles): bool
    {
        $user = self::requireAuthenticated();
        if (!in_array($user['role'], $allowedRoles, true)) {
            http_response_code(403);
            $title = 'Access denied';
            require dirname(__DIR__, 2) . '/views/errors/403.php';
            return false;
        }

        return true;
    }
}
