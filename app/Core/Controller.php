<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /** Render a PHP view inside the common application layout. */
    protected function view(string $view, array $data = []): void
    {
        $viewsDirectory = dirname(__DIR__, 2) . '/views';
        $viewFile = $viewsDirectory . '/' . $view . '.php';

        if (!is_file($viewFile)) {
            throw new \RuntimeException('View not found: ' . $view);
        }

        $title = $data['title'] ?? 'Library Management System';
        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        require $viewsDirectory . '/layouts/main.php';
    }
}
