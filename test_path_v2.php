<?php
require_once __DIR__ . '/vendor/autoload.php';

$appDir = realpath(__DIR__ . '/app') . DIRECTORY_SEPARATOR;
$fullPath = realpath(__DIR__ . '/app/Http/Controllers/HomeController.php');

echo "appDir: $appDir" . PHP_EOL;
echo "fullPath: $fullPath" . PHP_EOL;

$relativePath = 'App\\' . str_replace(
    [$appDir, '.php'],
    ['', ''],
    $fullPath
);

echo "relativePath: $relativePath" . PHP_EOL;
echo "class_exists: " . (class_exists($relativePath) ? 'true' : 'false') . PHP_EOL;

$relativePath2 = 'App\\' . str_replace(DIRECTORY_SEPARATOR, '\\', substr($fullPath, strlen($appDir), -4));
echo "relativePath2: $relativePath2" . PHP_EOL;
echo "class_exists2: " . (class_exists($relativePath2) ? 'true' : 'false') . PHP_EOL;
