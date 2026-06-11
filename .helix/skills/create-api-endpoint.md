# Skill: Create API Endpoint

## Steps

1. **Create a controller** (or use existing):
```bash
php helix make:controller ResourceName
```

2. **Add route attribute** on the method:
```php
#[Route('/api/resources/{id}', method: 'GET')]
public function show(int $id): JsonResponse
```

3. **Inject dependencies** via constructor or method parameter:
```php
public function __construct(private ResourceRepository $repo) {}
```

4. **Return a response**:
```php
return new JsonResponse(['data' => $result], 200);
```

## Patterns

| Action | Method | Path Pattern |
|--------|--------|-------------|
| List | GET | `/api/resources` |
| Show | GET | `/api/resources/{id}` |
| Create | POST | `/api/resources` |
| Update | PUT | `/api/resources/{id}` |
| Delete | DELETE | `/api/resources/{id}` |

## Example

```php
#[Route('/api/products', method: 'GET')]
public function index(): JsonResponse
{
    return new JsonResponse(['data' => $this->products->findAll()]);
}

#[Route('/api/products', method: 'POST')]
#[Validate(['name' => 'required|string|max:255'])]
public function store(Request $request): JsonResponse
{
    $product = $this->products->create($request->all());
    return new JsonResponse(['data' => $product], 201);
}
```

## Testing

```php
test('list products', function () {
    $controller = new ProductController(new ProductRepository());
    $response = $controller->index();
    expect($response->getStatus())->toBe(200);
});
```
