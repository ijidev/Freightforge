# Skill: Create Full CRUD

Generate a complete CRUD API for any resource in one flow.

## Steps

1. **Create the model**:
```bash
php helix make:model Resource field1:string field2:int
```

2. **Create the controller**:
```bash
php helix make:controller Resource
```

3. **Build the full CRUD controller**:

```php
<?php

namespace App\Http\Controllers;

use Helix\Http\JsonResponse;
use Helix\Http\Request;
use Helix\Routing\Attributes\Route;
use Helix\Validation\Attributes\Validate;
use App\Domain\Resource\ResourceRepository;

class ResourceController
{
    public function __construct(
        private ResourceRepository $repo
    ) {}

    #[Route('/api/resources', method: 'GET')]
    public function index(): JsonResponse
    {
        return new JsonResponse(['data' => $this->repo->findAll()]);
    }

    #[Route('/api/resources/{id}', method: 'GET')]
    public function show(int $id): JsonResponse
    {
        $resource = $this->repo->findById($id);
        if (!$resource) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }
        return new JsonResponse(['data' => $resource]);
    }

    #[Route('/api/resources', method: 'POST')]
    #[Validate(['name' => 'required|string|max:255'])]
    public function store(Request $request): JsonResponse
    {
        $resource = $this->repo->create($request->all());
        return new JsonResponse(['data' => $resource], 201);
    }

    #[Route('/api/resources/{id}', method: 'PUT')]
    #[Validate(['name' => 'string|max:255'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $resource = $this->repo->update($id, $request->all());
        if (!$resource) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }
        return new JsonResponse(['data' => $resource]);
    }

    #[Route('/api/resources/{id}', method: 'DELETE')]
    public function destroy(int $id): JsonResponse
    {
        if (!$this->repo->findById($id)) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }
        $this->repo->delete($id);
        return new JsonResponse(['message' => 'Deleted'], 204);
    }
}
```

4. **Verify routes**:
```bash
php helix route:list
```

5. **Test the endpoint**:
```bash
curl http://localhost:8080/api/resources
curl http://localhost:8080/api/resources/1
curl -X POST -H "Content-Type: application/json" -d '{"name":"Test"}' http://localhost:8080/api/resources
```
