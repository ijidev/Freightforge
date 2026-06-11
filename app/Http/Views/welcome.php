<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helix-Forge</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .container {
            max-width: 720px;
            text-align: center;
        }
        .logo {
            font-size: 4rem;
            font-weight: 800;
            letter-spacing: -0.05em;
            background: linear-gradient(135deg, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        .tagline {
            font-size: 1.25rem;
            color: #94a3b8;
            margin-bottom: 2rem;
        }
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: #1e293b;
            color: #818cf8;
            border: 1px solid #334155;
            margin-bottom: 1.5rem;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
            text-align: left;
        }
        .card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.75rem;
            padding: 1.25rem;
            transition: border-color 0.2s;
        }
        .card:hover { border-color: #818cf8; }
        .card h3 {
            font-size: 0.95rem;
            color: #818cf8;
            margin-bottom: 0.5rem;
        }
        .card p {
            font-size: 0.85rem;
            color: #94a3b8;
            line-height: 1.5;
        }
        .routes {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-top: 1.5rem;
            text-align: left;
        }
        .routes h3 {
            font-size: 0.9rem;
            color: #818cf8;
            margin-bottom: 0.75rem;
        }
        .routes table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        .routes th, .routes td {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #334155;
            text-align: left;
        }
        .routes th { color: #64748b; font-weight: 600; }
        .routes td { color: #cbd5e1; }
        .method {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .method-get { background: #1a3a2a; color: #4ade80; }
        .method-post { background: #1a2a3a; color: #60a5fa; }
        .method-put { background: #2a1a2a; color: #c084fc; }
        .method-delete { background: #2a1a1a; color: #f87171; }
        footer {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #475569;
        }
        footer a { color: #818cf8; text-decoration: none; }
        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge">v1.0.0 — AI-Native PHP Framework</div>
        <div class="logo">Helix-Forge</div>
        <p class="tagline">Fast, explicit, AI-native PHP 8.3+ framework for building APIs and web apps.</p>

        <div class="cards">
            <div class="card">
                <h3>⚡ Attribute Routing</h3>
                <p>Routes declared via #[Route] attributes directly on controller methods. No separate route files needed.</p>
            </div>
            <div class="card">
                <h3>🧩 Explicit DI</h3>
                <p>Constructor injection only. No facades, no magic. Every dependency is traceable by static analysis and AI.</p>
            </div>
            <div class="card">
                <h3>🤖 AI-Native</h3>
                <p>Built-in agent protocol, schema-first design, and compiled routes. AI generates clean code that passes review.</p>
            </div>
            <div class="card">
                <h3>📦 Zero Bloat</h3>
                <p>Core &lt;2MB. Routing, DI, HTTP, Template. Everything else is a Composer package.</p>
            </div>
        </div>

        <div class="routes">
            <h3>📋 Registered Routes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Path</th>
                        <th>Handler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($routes as $route): ?>
                    <tr>
                        <td><span class="method method-<?= strtolower($route['method']) ?>"><?= $route['method'] ?></span></td>
                        <td><code><?= $route['path'] ?></code></td>
                        <td><?= $route['handler'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
            <a href="/docs" style="display: inline-block; padding: 0.6rem 1.5rem; background: #818cf8; color: #fff; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.9rem;">📖 Read the Docs</a>
            <a href="/docs/getting-started" style="display: inline-block; padding: 0.6rem 1.5rem; border: 1px solid #334155; color: #e2e8f0; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.9rem;">🚀 Getting Started</a>
            <a href="/docs/ai-agent-protocol" style="display: inline-block; padding: 0.6rem 1.5rem; border: 1px solid #334155; color: #e2e8f0; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.9rem;">🤖 AI Agent Protocol</a>
        </div>
        <footer>
            Built with ❤️ for humans + AI agents &mdash;
            <a href="/docs">docs</a>
        </footer>
    </div>
</body>
</html>
