<?php

declare(strict_types=1);

if (!function_exists('e')) {
    /** Escape a value for safe insertion into ordinary HTML text/attribute contexts. */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    /** Build a URL that works at the domain root or under XAMPP's project subfolder. */
    function url(string $path = ''): string
    {
        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');
        if ($basePath === '.' || $basePath === '/') {
            $basePath = '';
        }

        return $basePath . '/' . ltrim($path, '/');
    }
}

if (!function_exists('csrf_field')) {
    /** Return a hidden field containing the current session's CSRF token. */
    function csrf_field(): string
    {
        $token = \App\Core\Csrf::token();
        return '<input type="hidden" name="_csrf" value="' . e($token) . '">';
    }
}

if (!function_exists('set_flash')) {
    function set_flash(string $type, string $message): void
    {
        if (!in_array($type, ['success', 'error', 'info'], true)) {
            $type = 'info';
        }
        $_SESSION['_flash'][$type] = $message;
    }
}

if (!function_exists('take_flash')) {
    /** Read and remove a one-time flash message. */
    function take_flash(string $type): ?string
    {
        $message = $_SESSION['_flash'][$type] ?? null;
        unset($_SESSION['_flash'][$type]);
        return is_string($message) ? $message : null;
    }
}

if (!function_exists('current_user')) {
    /** @return array{id:int,name:string,email:string,role:string}|null */
    function current_user(): ?array
    {
        return \App\Core\Auth::user();
    }
}
