# Testing

Helix-Forge uses [PestPHP](https://pestphp.com) for testing — zero-config, beautiful syntax.

## Setup

Tests live in the `tests/` directory. Pest is pre-configured via Composer.

```bash
composer test
# or
vendor/bin/pest
```

## Writing Tests

```php
<?php

use Helix\Http\Request;
use Helix\Http\JsonResponse;

test('health endpoint returns ok', function () {
    $request = Request::fromGlobals();
    $response = new JsonResponse(['status' => 'ok']);

    expect($response->getStatus())->toBe(200);
    expect($response->getContent())->toContain('ok');
});

test('user creation validates required fields', function () {
    $validator = new \Helix\Validation\Validator();
    $result = $validator->validate([], ['name' => 'required|string']);

    expect($result)->toBeFalse();
    expect($validator->errors())->toHaveKey('name');
});
```

## Testing Controllers

```php
test('user list returns array', function () {
    $controller = new \App\Http\Controllers\UserController();
    $response = $controller->index(new Request([], [], [], [], [], []));

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getStatus())->toBe(200);
});
```

## Testing Repositories

```php
test('repository can create and find users', function () {
    $repo = new \App\Domain\User\UserRepository();

    $user = $repo->create([
        'name' => 'Test',
        'email' => 'test@test.com',
    ]);

    expect($user['name'])->toBe('Test');

    $found = $repo->findById($user['id']);
    expect($found['email'])->toBe('test@test.com');
});
```

## Test Directory Structure

```
tests/
├── Feature/        # Feature/integration tests
├── Unit/           # Unit tests
└── Pest.php        # Pest configuration
```

## CI Configuration

```yaml
# .github/workflows/tests.yml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - run: vendor/bin/pest
```
