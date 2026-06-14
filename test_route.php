<?php

require 'vendor/autoload.php';

use Helix\Foundation\Application;
use Helix\Http\Request;

$app = Application::create();
$app->boot();

// Mocking the request for /
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';

$request = Request::fromGlobals();
$router = $app->getContainer()->get(\Helix\Routing\Router::class);

try {
    $response = $router->dispatch($request);
    echo "Success! Response: " . $response->getContent() . PHP_EOL;
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
