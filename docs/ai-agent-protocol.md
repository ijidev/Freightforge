# AI Agent Protocol

Helix-Forge is built for AI agents. The `.helix/agent.yml` file defines how AI interacts with your project.

## The `agent.yml` File

```yaml
project:
  name: "My API"
  php_version: "8.3"
  db: "pgsql"
  coding_style: "psr-12"

rules:
    - "No facades. Use DI only."
    - "All routes must use #[Route] attribute."
    - "Repositories only. No direct DB queries in controllers."

packages:
  allowed: ["helix/*", "symfony/*"]
  forbidden: []

endpoints:
  generate_model: "php helix make:model {name} {fields}"
  generate_controller: "php helix make:controller {name}"
  list_routes: "php helix route:list"
  cache_routes: "php helix cache:routes"
  run_tests: "vendor/bin/pest"
  serve: "php helix serve"
```

## Agent Commands

AI agents can call the framework via safe, predictable commands:

| Command | Purpose |
|---------|---------|
| `helix make:controller User` | Generate a controller |
| `helix make:model User name:string email:string` | Generate a model + repo |
| `helix route:list` | Inspect all routes |
| `helix cache:routes` | Compile routes for prod |
| `vendor/bin/pest` | Run tests |

## Why Agents Love Helix-Forge

1. **Attributes are self-documenting** — AI sees route, validation, and type in one place.
2. **Explicit DI** — No hidden dependencies. The constructor tells AI everything.
3. **Compiled Routes** — No runtime reflection. AI generates static PHP easily.
4. **Agent Protocol** — AI doesn't guess file paths. It calls official commands.
5. **No Magic** — PHPStan and AI can trace 100% of code paths.

## Safety Rules for AI

The `rules` section in `agent.yml` enforces coding standards:

```yaml
rules:
    - "No facades. Use DI only."
    - "All routes must use #[Route] attribute."
    - "Repositories only. No direct DB queries in controllers."
    - "Use #[Validate] attribute for validation rules."
    - "Use #[Entity] and #[Column] for database schema."
```

## Integration with Cursor/Claude Code

When a developer opens a Helix-Forge project in Cursor or Claude Code, the agent loads `.helix/agent.yml` and knows:

- Project structure and conventions
- Allowed packages
- Available CLI commands
- Coding standards
- Database configuration

No more "AI wrote Laravel code in a Symfony project".
