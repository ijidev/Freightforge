<?php

use Helix\Http\JsonResponse;

// Additional explicit routes (optional - most routes come from #[Route] attributes)
$router->add('GET', '/api/health', function () {
    return new JsonResponse(['status' => 'ok', 'timestamp' => date('c')]);
});
