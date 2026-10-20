<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;

final class AdminController extends Controller
{
    public function index(): void
    {
        if (!AuthMiddleware::requireRoles(['admin'])) {
            return;
        }

        $this->view('admin/index', ['title' => 'Administration']);
    }
}
