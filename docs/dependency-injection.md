# Dependency Injection

Helix-Forge uses a **PSR-11 compatible** container with auto-wiring. Constructor injection only — no facades, no static service locators.

## The Container

```php
use Helix\Container\Container;

$container = new Container();
```

## Auto-Wiring

The container automatically resolves dependencies by reading constructor type-hints:

```php
class UserService
{
    public function __construct(
        private UserRepository $users,
        private Mailer $mailer,
        private EventDispatcher $events
    ) {}
}

// No configuration needed:
$service = $container->get(UserService::class);
// Resolves UserRepository, Mailer, EventDispatcher recursively
```

## Binding

```php
// Bind a class
$container->set(LoggerInterface::class, FileLogger::class);

// Bind a factory
$container->set(Mailer::class, function ($c) {
    return new Mailer($_ENV['MAIL_DSN']);
});

// Singleton (shared instance)
$container->singleton(Database::class, function ($c) {
    return new Database($_ENV['DB_DSN']);
});
```

## Aliases

```php
$container->alias(LoggerInterface::class, Logger::class);
```

## PSR-11 Methods

```php
$container->has(Service::class);   // bool
$container->get(Service::class);   // mixed
```

## Why Not Facades?

Laravel facades hide dependencies and break static analysis:

```php
// Laravel — where does Auth come from?
Auth::user()->posts;

// Helix-Forge — explicit traceability
class UserController
{
    public function __construct(
        private AuthService $auth,
        private PostRepository $posts
    ) {}
}
```

## Best Practices

- Always use constructor injection
- Type-hint interfaces, not concrete classes (when possible)
- Register bindings during application boot
- Use singletons for stateless services (database, mailer, logger)
