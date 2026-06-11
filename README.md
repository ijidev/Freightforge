# Helix-Forge

**The AI-native PHP framework for building APIs and web apps 10x faster with agents.**

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

## Quick Start

```bash
composer create-project helix/forge my-app
cd my-app
php helix serve
```

Visit `http://127.0.0.1:8080` in your browser.

## Why Helix-Forge?

| Problem | How Helix-Forge Solves It |
|---------|--------------------------|
| AI generates messy code in Laravel | Attribute-driven + explicit DI = clean output |
| Magic facades break static analysis | No facades, no `__call`. Constructor injection only |
| Routes scattered across files | `#[Route]` attributes on controller methods |
| Schema defined in 4 places | `#[Entity]` / `#[Column]` — one source of truth |
| AI breaks things by guessing paths | Agent protocol: safe CLI commands for AI |

## Features

- **⚡ Attribute Routing** — `#[Route('/api/users', method: 'GET')]` on controller methods
- **🧩 Explicit DI** — PSR-11 container with auto-wiring. No facades
- **📦 Schema-First DB** — `#[Entity]` / `#[Column]` attributes, repository pattern
- **✅ Attribute Validation** — `#[Validate]` rules on methods and properties
- **🤖 AI-Native** — `.helix/agent.yml` protocol for safe AI code generation
- **🔥 Compiled Routes** — 0.2ms resolution, no reflection in prod
- **📖 Built-in Docs** — Visit `/docs` when the server is running

## Commands

```bash
php helix serve              # Start dev server
php helix make:controller    # Generate controller
php helix make:model         # Generate model + repository
php helix route:list         # Show registered routes
php helix cache:routes       # Compile routes for production
```

## Documentation

Full documentation is available at `/docs` when the dev server is running, or in the `docs/` directory as markdown.

## License

Helix-Forge is open-source software licensed under the [MIT license](LICENSE).
