# Controllers

Controllers handle incoming requests and return responses. They use constructor injection for dependencies and `#[Route]` attributes for routing.

## Basic Controller

```php
<?php

namespace App\Http\Controllers;

use Helix\Http\JsonResponse;
use Helix\Http\Request;
use Helix\Routing\Attributes\Route;

class UserController
{
    #[Route('/api/users', method: 'GET')]
    public function index(Request $request): JsonResponse
    {
        return new JsonResponse(['data' => []]);
    }
}
```

## Generating Controllers

```bash
php helix make:controller User
```

This generates a controller with a default `index` method and route.

## Request Access

The `Request` object is automatically injected:

```php
#[Route('/api/users', method: 'POST')]
public function store(Request $request): JsonResponse
{
    $data = $request->all();
    $name = $request->input('name');
    $headers = $request->header('Content-Type');
    return new JsonResponse(['created' => $data], 201);
}
```

## Dependency Injection in Controllers

Dependencies declared in the constructor or method signature are auto-resolved:

```php
class UserController
{
    public function __construct(
        private UserRepository $users,
        private Logger $logger
    ) {}

    #[Route('/api/users', method: 'GET')]
    public function index(): JsonResponse
    {
        $this->logger->info('Fetching users');
        return new JsonResponse(['data' => $this->users->findAll()]);
    }
}
```

## Responses

| Type | Usage |
|------|-------|
| `JsonResponse` | `new JsonResponse($data, 200)` — JSON API responses |
| `Response::html()` | `Response::html('<h1>Hello</h1>')` — HTML content |
| `Response::json()` | `new JsonResponse($data)` — Shorthand for JSON |
| `Response::redirect()` | `Response::redirect('/login')` — Redirects |

## Middleware

Middleware runs before your controller. Implement `MiddlewareInterface`:

```php
<?php

namespace App\Http\Middleware;

use Helix\Http\Middleware\MiddlewareInterface;
use Helix\Http\Request;
use Helix\Http\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        if (!$request->header('Authorization')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
```

Register middleware on the kernel:

```php
// In your Application boot
$kernel->pushMiddleware(AuthMiddleware::class);
```
