<?php

return [
    'name' => 'FreightForge',
    'env' => $_ENV['APP_ENV'] ?? 'development',
    'debug' => (bool) ($_ENV['APP_DEBUG'] ?? true),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost:8080',
];
