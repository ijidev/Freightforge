# Skill: Add Authentication (Basic)

## Steps

1. **Create Auth middleware**:
```php
<?php

namespace App\Http\Middleware;

use Helix\Http\Middleware\MiddlewareInterface;
use Helix\Http\Request;
use Helix\Http\Response;
use Helix\Http\JsonResponse;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private array $excludedPaths = ['/api/login', '/api/register', '/api/health']
    ) {}

    public function handle(Request $request, callable $next): Response
    {
        foreach ($this->excludedPaths as $path) {
            if ($request->uri() === $path) {
                return $next($request);
            }
        }

        $token = $request->header('Authorization');
        if (!$token || !str_starts_with($token, 'Bearer ')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
```

2. **Create Auth controller**:
```php
<?php

namespace App\Http\Controllers;

use Helix\Http\JsonResponse;
use Helix\Http\Request;
use Helix\Routing\Attributes\Route;

class AuthController
{
    #[Route('/api/login', method: 'POST')]
    public function login(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');
        // Verify credentials, generate token
        return new JsonResponse(['token' => 'generated-jwt-token']);
    }

    #[Route('/api/me', method: 'GET')]
    public function me(Request $request): JsonResponse
    {
        // Decode token, return user info
        return new JsonResponse(['user' => ['id' => 1, 'email' => 'user@test.com']]);
    }
}
```

3. **Register middleware**:
```php
// In Application
$kernel->pushMiddleware(AuthMiddleware::class);
```

4. **Create User service** with token generation:
```php
class AuthService
{
    public function __construct(private UserRepository $users) {}

    public function attempt(string $email, string $password): ?string
    {
        $user = $this->users->findOneBy('email', $email);
        if ($user && password_verify($password, $user['password'])) {
            return bin2hex(random_bytes(32)); // Generate token
        }
        return null;
    }
}
```

## Protect Specific Routes

To protect only certain routes, check the URI in middleware or create route-specific middleware groups.
