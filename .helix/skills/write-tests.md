# Skill: Write Tests

## Steps

1. **Create test file** in `tests/`:
```php
<?php

use Helix\Http\JsonResponse;

test('description of what is tested', function () {
    // Arrange
    // Act
    // Assert
});
```

2. **Run tests**:
```bash
vendor/bin/pest
# or
composer test
```

## Test Types

### Unit Tests — test a single class

```php
test('validator rejects empty required field', function () {
    $validator = new \Helix\Validation\Validator();
    expect($validator->validate([], ['name' => 'required']))->toBeFalse();
    expect($validator->errors())->toHaveKey('name');
});
```

### Controller Tests

```php
test('show user returns json', function () {
    $controller = new \App\Http\Controllers\UserController();
    $response = $controller->show(new Request([], [], [], [], [], []), 1);
    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getStatus())->toBe(200);
});
```

### Feature Tests — test full flow

```php
test('full user CRUD flow', function () {
    $repo = new \App\Domain\User\UserRepository();

    $created = $repo->create(['name' => 'Test', 'email' => 't@t.com']);
    expect($created['name'])->toBe('Test');

    $found = $repo->findById($created['id']);
    expect($found['email'])->toBe('t@t.com');

    $updated = $repo->update($created['id'], ['name' => 'Updated']);
    expect($updated['name'])->toBe('Updated');

    $repo->delete($created['id']);
    expect($repo->findById($created['id']))->toBeNull();
});
```

## Best Practices

- **Arrange-Act-Assert**: Set up, execute, verify
- **Test one thing per test**: Clear test names
- **Use meaningful names**: `test('unauthenticated request returns 401')`
- **Test edge cases**: Empty data, invalid input, missing fields
- **Cover validation rules**: Test both passes and failures
