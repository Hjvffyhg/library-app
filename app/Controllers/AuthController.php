<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\PasswordHasher;
use App\Services\AuthService;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            header('Location: ' . url('dashboard'), true, 303);
            return;
        }

        $this->view('auth/login', ['title' => 'Sign in']);
    }

    public function login(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            echo 'Method not allowed.';
            return;
        }

        if (!Csrf::isValid($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Your form session expired. Please try signing in again.');
            header('Location: ' . url('login'), true, 303);
            return;
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || strlen($password) > PasswordHasher::maxPasswordBytes()) {
            set_flash('error', 'The email or password is incorrect, or the account is inactive.');
            header('Location: ' . url('login'), true, 303);
            return;
        }

        try {
            $service = new AuthService();
            $authenticated = $service->attempt($email, $password, (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        } catch (\Throwable $exception) {
            error_log('Login service error: ' . $exception->getMessage());
            http_response_code(500);
            $this->view('errors/500', ['title' => 'Sign-in unavailable']);
            return;
        }

        if (!$authenticated) {
            // The same message covers wrong password, inactive account, and throttling.
            set_flash('error', 'The email or password is incorrect, or the account is inactive. If you have tried several times, wait 15 minutes before trying again.');
            header('Location: ' . url('login'), true, 303);
            return;
        }

        set_flash('success', 'Welcome back! You are now signed in.');
        $intendedPath = $_SESSION['_intended_path'] ?? null;
        unset($_SESSION['_intended_path']);
        // Only allow local application paths to prevent open redirects.
        if (is_string($intendedPath) && str_starts_with($intendedPath, '/') && !str_starts_with($intendedPath, '//')) {
            header('Location: ' . $intendedPath, true, 303);
            return;
        }

        header('Location: ' . url('dashboard'), true, 303);
    }

    public function logout(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            echo 'Method not allowed.';
            return;
        }

        if (!Csrf::isValid($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            echo 'Your form session expired. Return to the page and try again.';
            return;
        }

        Auth::logout();
        // Force a new session ID before storing the one-time confirmation message.
        session_id('');
        session_start();
        set_flash('success', 'You have signed out.');
        header('Location: ' . url('login'), true, 303);
    }
}
