# Skill: Create Middleware

## Steps

1. **Implement `MiddlewareInterface`**:
```php
<?php

namespace App\Http\Middleware;

use Helix\Http\Middleware\MiddlewareInterface;
use Helix\Http\Request;
use Helix\Http\Response;

class RateLimitMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        // Logic before controller
        $response = $next($request);
        // Logic after controller
        return $response;
    }
}
```

2. **Register middleware** on the Kernel:
```php
// In Application boot or entry point
$kernel = $container->get(Kernel::class);
$kernel->pushMiddleware(RateLimitMiddleware::class);
// or with instance:
$kernel->pushMiddleware(new RateLimitMiddleware());
```

## Pattern

```
Request → Middleware 1 → Middleware 2 → Controller → Middleware 2 → Middleware 1 → Response
```

## Example: Auth Middleware

```php
class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $token = $request->header('Authorization');
        if (!$token || !str_starts_with($token, 'Bearer ')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
```

## Example: Logging Middleware

```php
class LoggingMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = (microtime(true) - $start) * 1000;
        error_log(sprintf('%s %s — %d (%.2fms)',
            $request->method(), $request->uri(),
            $response->getStatus(), $duration
        ));
        return $response;
    }
}
```
