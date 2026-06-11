# Architecture

## Core Philosophy

Helix-Forge is not "another MVC". It's built for humans + AI agents to build fast APIs and web apps without magic or bloat.

### Principles

1. **Explicit over Magic**
   No facades, no `__call`, no global helpers unless imported. Everything traceable by static analysis and AI.

2. **Attribute-Driven**
   Routing, validation, DI, permissions declared via PHP 8.3 `#[Attributes]` directly on classes/methods.

3. **Schema-First**
   The code itself is the source of truth. No separate route files, config arrays scattered everywhere.

4. **Zero Bloat Core**
   Core is <2MB. Routing, DI, Request/Response, Template. Everything else is a Composer package.

5. **AI-Native**
   Ship with an agent protocol so AI can safely generate, modify, and test code without breaking the app.

## Request Lifecycle

```
Request → public/index.php → Application::run()
    → Kernel::handle()
        → Middleware Pipeline
            → Router::dispatch()
                → Controller Method
            → Response
    → Response::send()
```

## Core Packages

| Package | Purpose | Size |
|---------|---------|------|
| `Helix\Container` | PSR-11 DI container, auto-wiring | 8kb |
| `Helix\Http` | Request/Response, middleware, kernel | 12kb |
| `Helix\Routing` | Attribute routing, compiled routes | 15kb |
| `Helix\Validation` | Attribute validation | 10kb |
| `Helix\Database` | Query builder, repository base | 40kb |
| `Helix\View` | PHP template rendering | 4kb |
| `Helix\Console` | CLI commands | 10kb |

## Why This Works for AI

| Feature | Why AI Likes It |
|---------|----------------|
| Attributes | Self-documenting. AI sees route, validation, type in one place. |
| Explicit DI | No hidden dependencies. Constructor shows everything. |
| Compiled Routes | No reflection. AI generates static PHP easily. |
| Agent Protocol | AI doesn't guess file paths. It calls official commands. |
| No Magic | PHPStan and AI can trace 100% of code paths. |
