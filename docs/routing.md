# Routing

Helix-Forge uses **attribute-driven routing** — routes are declared via `#[Route]` attributes directly on controller methods.

## Basic Usage

```php
<?php

namespace App\Http\Controllers;

use Helix\Routing\Attributes\Route;
use Helix\Http\JsonResponse;

class UserController
{
    #[Route('/api/users', method: 'GET')]
    public function index(): JsonResponse
    {
        return new JsonResponse(['data' => []]);
    }
}
```

## Route Parameters

Use `{param}` syntax in the path. Values are automatically passed to the method:

```php
#[Route('/api/users/{id}', method: 'GET')]
public function show(int $id): JsonResponse
{
    return new JsonResponse(['id' => $id]);
}
```

## HTTP Methods

| Attribute | Method |
|-----------|--------|
| `method: 'GET'` | GET |
| `method: 'POST'` | POST |
| `method: 'PUT'` | PUT |
| `method: 'PATCH'` | PATCH |
| `method: 'DELETE'` | DELETE |

## Named Routes

```php
#[Route('/api/users/{id}', method: 'GET', name: 'users.show')]
```

## Route Groups (via routes/*.php)

While most routes use attributes, you can add fallback routes in route files:

```php
// routes/api.php
$router->add('GET', '/api/health', function () {
    return new JsonResponse(['status' => 'ok']);
});
```

## Route Caching

For production, compile attributes to plain PHP arrays:

```bash
php helix cache:routes
```

This eliminates reflection at runtime. Resolution time: ~0.2ms.

## View Routes

```php
// routes/web.php
$router->add('GET', '/', function () {
    return Response::html('<h1>Hello</h1>');
});
```

## Viewing Routes

```bash
php helix route:list
```
