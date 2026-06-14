<?php
require_once __DIR__ . '/vendor/autoload.php';

$className = 'App\\Http\\Controllers\\HomeController';
echo "Checking class: $className" . PHP_EOL;
echo "class_exists: " . (class_exists($className) ? 'true' : 'false') . PHP_EOL;
