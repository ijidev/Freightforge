<?php

namespace Helix\Installer;

class WebInstaller
{
    private Installer $installer;

    public function __construct(string $projectDir)
    {
        $this->installer = new Installer($projectDir);
    }

    public function isInstalled(): bool
    {
        return file_exists($this->installer->getProjectDir() . '/.env');
    }

    public function runChecks(): array
    {
        $this->installer->runSystemChecks();
        return $this->installer->getResults();
    }

    public function runSetup(array $config = []): array
    {
        $adminName = $config['ADMIN_NAME'] ?? '';
        $adminEmail = $config['ADMIN_EMAIL'] ?? '';
        $adminPassword = $config['ADMIN_PASSWORD'] ?? '';

        // Load defaults from .env.example
        $envExample = $this->installer->getProjectDir() . '/.env.example';
        $defaults = file_exists($envExample) ? (parse_ini_file($envExample) ?: []) : [];

        // Merge defaults with provided config
        $fullConfig = array_merge($defaults, $config);

        // Set the config in the installer
        $this->installer->setConfig($fullConfig);

        try {
            // Perform checks and setup without creating .env yet
            $this->installer->checkPhpVersion();
            $this->installer->checkExtensions();
            $this->installer->checkPermissions();
            $this->installer->setupDatabase();
            $this->installer->setupDirectories();
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'results' => $this->installer->getResults(),
            ];
        }

        $failed = array_filter($this->installer->getResults(), fn($r) => $r['status'] === 'fail');
        if (!empty($failed)) {
            return [
                'success' => false,
                'error' => 'Some checks failed. Check the results for details.',
                'results' => $this->installer->getResults(),
            ];
        }

        if (!$this->installer->setupSchema()) {
            return [
                'success' => false,
                'error' => 'Failed to create database tables.',
                'results' => $this->installer->getResults(),
            ];
        }

        if (!$this->installer->setupAdmin($adminName, $adminEmail, $adminPassword)) {
            return [
                'success' => false,
                'error' => 'Failed to create admin account.',
                'results' => $this->installer->getResults(),
            ];
        }

        // Post-installation steps
        $this->installer->seedDatabase();
        $this->installer->createSqlDump();
        $this->installer->checkTables(['users', 'settings', 'sections', 'shipments', 'statuses', 'shipment_statuses']);

        // Installation successful! Now create the .env file.
        $dbDriver = $fullConfig['DB_DRIVER'] ?? 'mysql';
        $envVars = [
            'APP_ENV' => $fullConfig['APP_ENV'] ?? 'development',
            'APP_DEBUG' => $fullConfig['APP_DEBUG'] ?? 'true',
            'APP_URL' => $fullConfig['APP_URL'] ?? 'http://localhost:8080',
            'DB_DRIVER' => $dbDriver,
        ];

        if ($dbDriver === 'sqlite') {
            $envVars['DB_PATH'] = $fullConfig['DB_PATH'] ?? 'storage/database.sqlite';
        } else {
            $envVars['DB_HOST'] = $fullConfig['DB_HOST'] ?? '127.0.0.1';
            $envVars['DB_PORT'] = $fullConfig['DB_PORT'] ?? ($dbDriver === 'mysql' ? '3306' : '5432');
            $envVars['DB_NAME'] = $fullConfig['DB_NAME'] ?? 'freightforge';
            $envVars['DB_USER'] = $fullConfig['DB_USER'] ?? 'root';
            $envVars['DB_PASS'] = $fullConfig['DB_PASS'] ?? '';
        }

        $content = '';
        foreach ($envVars as $key => $value) {
            $content .= "{$key}={$value}\n";
        }
        file_put_contents($this->installer->getProjectDir() . '/.env', $content);

        return [
            'success' => true,
            'results' => $this->installer->getResults(),
        ];
    }
}
