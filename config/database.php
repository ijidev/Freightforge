<?php

return [
    'default' => $_ENV['DB_DRIVER'] ?? 'sqlite',
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'path' => __DIR__ . '/../storage/database.sqlite',
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['DB_PORT'] ?? '5432',
            'database' => $_ENV['DB_NAME'] ?? 'helix',
            'username' => $_ENV['DB_USER'] ?? 'postgres',
            'password' => $_ENV['DB_PASS'] ?? '',
        ],
    ],
];
