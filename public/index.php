<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Helix\Foundation\Application;
use Helix\Http\Response;

$installed = file_exists(__DIR__ . '/../.env');
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH);

if (!$installed && $path !== '/install' && !str_starts_with($path, '/install/')) {
    $response = Response::redirect('/install');
    $response->send();
    return;
}

$app = Application::create();
$app->run();
