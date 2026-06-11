<?php

namespace App\Http\Controllers;

use Helix\Http\JsonResponse;
use Helix\Http\Request;
use Helix\Routing\Attributes\Route;

class ProductController
{
    #[Route('/product', method: 'GET')]
    public function index(Request $request): JsonResponse
    {
        return new JsonResponse(['message' => 'Hello from ProductController!']);
    }
}
