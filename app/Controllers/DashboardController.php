<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $user = AuthMiddleware::requireAuthenticated();

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'user' => $user,
        ]);
    }
}
