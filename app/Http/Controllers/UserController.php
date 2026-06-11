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
        return new JsonResponse([
            'data' => [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => 'Bob'],
            ]
        ]);
    }

    #[Route('/api/users/{id}', method: 'GET')]
    public function show(Request $request, int $id): JsonResponse
    {
        return new JsonResponse([
            'id' => (int) $id,
            'name' => 'User ' . $id,
            'email' => "user{$id}@example.com",
        ]);
    }

    #[Route('/api/users', method: 'POST')]
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        return new JsonResponse([
            'message' => 'User created',
            'user' => $data,
        ], 201);
    }
}
