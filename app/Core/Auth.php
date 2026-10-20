<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    /** @return array{id:int,name:string,email:string,role:string}|null */
    public static function user(): ?array
    {
        $user = $_SESSION['user'] ?? null;
        if (!is_array($user)
            || !isset($user['id'], $user['name'], $user['email'], $user['role'])
            || !is_numeric($user['id'])) {
            return null;
        }

        return [
            'id' => (int) $user['id'],
            'name' => (string) $user['name'],
            'email' => (string) $user['email'],
            'role' => (string) $user['role'],
        ];
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function login(array $user): void
    {
        // Prevent session fixation: issue a fresh session ID after authentication.
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => (string) $user['name'],
            'email' => (string) $user['email'],
            'role' => (string) $user['role'],
        ];
        $_SESSION['_last_activity'] = time();
        Csrf::regenerate();
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        session_destroy();
    }
}
