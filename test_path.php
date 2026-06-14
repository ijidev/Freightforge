<?php
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
