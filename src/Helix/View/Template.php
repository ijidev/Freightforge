<?php

namespace Helix\View;

class Template
{
    private string $viewsPath;
    private array $shared = [];

    public function __construct(?string $viewsPath = null)
    {
        $this->viewsPath = $viewsPath ?? __DIR__ . '/../../../app/Http/Views';
    }

    public function share(string $key, mixed $value): void
    {
        $this->shared[$key] = $value;
    }

    public function render(string $template, array $data = []): string
    {
        extract(array_merge($this->shared, $data), EXTR_SKIP);

        $file = $this->viewsPath . '/' . str_replace('.', '/', $template) . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("View not found: {$template}");
        }

        ob_start();
        include $file;
        return ob_get_clean();
    }

    public function exists(string $template): bool
    {
        $file = $this->viewsPath . '/' . str_replace('.', '/', $template) . '.php';
        return file_exists($file);
    }
}
