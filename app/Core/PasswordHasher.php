<?php

declare(strict_types=1);

namespace App\Core;

final class PasswordHasher
{
    /** Prefer Argon2id when the installed PHP build supports it. */
    public static function algorithm(): string|int
    {
        return defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
    }

    public static function hash(string $password): string
    {
        $hash = password_hash($password, self::algorithm());
        if (!is_string($hash)) {
            throw new \RuntimeException('Password hashing failed.');
        }
        return $hash;
    }

    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, self::algorithm());
    }

    /** A dummy hash using the currently preferred algorithm helps normalize failed-login timing. */
    public static function dummyHash(): string
    {
        if (defined('PASSWORD_ARGON2ID')) {
            return '$argon2id$v=19$m=65536,t=4,p=1$Lkk2QWNhQmVESU1rN0tjYg$IA8UYEX6ZmbaUiwKdwqNaPw8MPd2Jvop4eveB/bKyUU';
        }

        return '$2y$12$fVAf0PBy89J6SI2G17HKy.AcPqy7biYvNHfJ9z5/K5/rY/3vxxt32';
    }

    /** Bcrypt truncates input after 72 bytes; do not silently accept longer input on fallback. */
    public static function maxPasswordBytes(): int
    {
        return defined('PASSWORD_ARGON2ID') ? 4096 : 72;
    }
}
