<?php

namespace App\Http\Controllers;

use Helix\Http\Response;
use Helix\Routing\Attributes\Route;

class DocsController
{
    private array $pages = [
        'index' => 'Documentation',
        'getting-started' => 'Getting Started',
        'architecture' => 'Architecture',
        'routing' => 'Routing',
        'controllers' => 'Controllers',
        'dependency-injection' => 'Dependency Injection',
        'database' => 'Database',
        'validation' => 'Validation',
        'console' => 'Console',
        'ai-agent-protocol' => 'AI Agent Protocol',
        'testing' => 'Testing',
        'templates' => 'Templates',
    ];

    private string $docsDir;

    public function __construct()
    {
        $this->docsDir = realpath(__DIR__ . '/../../../docs');
    }

    #[Route('/docs', method: 'GET')]
    public function index(): Response
    {
        return $this->render('index');
    }

    #[Route('/docs/{page}', method: 'GET')]
    public function show(string $page): Response
    {
        if (!isset($this->pages[$page])) {
            return Response::html($this->layout('404', '<h1>404</h1><p>Documentation page not found.</p>'), 404);
        }

        return $this->render($page);
    }

    private function render(string $page): Response
    {
        $file = $this->docsDir . '/' . $page . '.md';

        if (!file_exists($file)) {
            return Response::html($this->layout('404', '<h1>404</h1><p>Documentation page not found.</p>', 'index'), 404);
        }

        $markdown = file_get_contents($file);
        $html = $this->markdownToHtml($markdown);
        $title = $this->pages[$page] ?? 'Documentation';

        return Response::html($this->layout($title, $html, $page));
    }

    private function markdownToHtml(string $markdown): string
    {
        $parsedown = new \Parsedown();
        $parsedown->setSafeMode(false);
        return $parsedown->text($markdown);
    }

    private function layout(string $title, string $content, string $currentPage = 'index'): string
    {
        $sidebar = $this->sidebar($currentPage);
        $escapedTitle = htmlspecialchars($title);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$escapedTitle} — Helix-Forge Docs</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 260px;
            background: #1e293b;
            border-right: 1px solid #334155;
            padding: 1.5rem 0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
        }
        .sidebar-logo {
            font-size: 1.25rem;
            font-weight: 700;
            padding: 0 1.25rem 1rem;
            border-bottom: 1px solid #334155;
            margin-bottom: 0.75rem;
        }
        .sidebar-logo a {
            color: #818cf8;
            text-decoration: none;
        }
        .sidebar-logo a:hover { color: #a5b4fc; }
        .sidebar-logo small {
            display: block;
            font-size: 0.7rem;
            font-weight: 400;
            color: #64748b;
            margin-top: 0.25rem;
        }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li { border-left: 2px solid transparent; }
        .sidebar-nav li.active {
            border-left-color: #818cf8;
            background: rgba(129, 140, 248, 0.08);
        }
        .sidebar-nav a {
            display: block;
            padding: 0.5rem 1.25rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.15s;
        }
        .sidebar-nav a:hover { color: #e2e8f0; }
        .sidebar-nav li.active a { color: #818cf8; font-weight: 600; }
        .main {
            margin-left: 260px;
            flex: 1;
            padding: 2rem 3rem;
            max-width: 900px;
        }
        .main h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #334155;
        }
        .main h2 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #e2e8f0;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
        }
        .main h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .main p { margin-bottom: 1rem; line-height: 1.7; color: #cbd5e1; }
        .main a { color: #818cf8; text-decoration: none; }
        .main a:hover { text-decoration: underline; }
        .main code {
            background: #1e293b;
            padding: 0.15rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.85em;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            color: #c084fc;
        }
        .main pre {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .main pre code {
            background: none;
            padding: 0;
            color: #e2e8f0;
            font-size: 0.85rem;
        }
        .main table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .main th, .main td {
            padding: 0.6rem 0.75rem;
            border: 1px solid #334155;
            text-align: left;
        }
        .main th {
            background: #1e293b;
            color: #94a3b8;
            font-weight: 600;
        }
        .main td { color: #cbd5e1; }
        .main ul, .main ol { margin: 0.5rem 0 1rem 1.5rem; color: #cbd5e1; }
        .main li { margin-bottom: 0.25rem; line-height: 1.6; }
        .main blockquote {
            border-left: 3px solid #818cf8;
            padding: 0.75rem 1rem;
            margin: 1rem 0;
            background: #1e293b;
            border-radius: 0 0.5rem 0.5rem 0;
        }
        .main blockquote p { margin: 0; color: #94a3b8; }
        .main hr {
            border: none;
            border-top: 1px solid #334155;
            margin: 2rem 0;
        }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-logo">
            <a href="/">Helix-Forge</a>
            <small>v1.0.0 Docs</small>
        </div>
        <ul class="sidebar-nav">
            {$sidebar}
        </ul>
    </aside>
    <main class="main">
        {$content}
    </main>
</body>
</html>
HTML;
    }

    private function sidebar(string $current = 'index'): string
    {
        $items = '';

        foreach ($this->pages as $slug => $label) {
            $active = $slug === $current ? ' class="active"' : '';
            $href = '/docs' . ($slug !== 'index' ? '/' . $slug : '');
            $items .= "<li{$active}><a href=\"{$href}\">" . htmlspecialchars($label) . "</a></li>\n";
        }

        return $items;
    }
}
