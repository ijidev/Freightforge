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
        $this->installer->run(silent: true);
        return $this->installer->getResults();
    }

    public function runSetup(array $config = []): array
    {
        $adminName = $config['ADMIN_NAME'] ?? '';
        $adminEmail = $config['ADMIN_EMAIL'] ?? '';
        $adminPassword = $config['ADMIN_PASSWORD'] ?? '';

        $dbDriver = $config['DB_DRIVER'] ?? 'sqlite';
        $envVars = [
            'APP_ENV' => 'development',
            'APP_DEBUG' => 'true',
            'APP_URL' => $config['APP_URL'] ?? 'http://localhost:8080',
            'DB_DRIVER' => $dbDriver,
        ];

        if ($dbDriver === 'sqlite') {
            $envVars['DB_PATH'] = $config['DB_PATH'] ?? 'storage/database.sqlite';
        } else {
            $envVars['DB_HOST'] = $config['DB_HOST'] ?? '127.0.0.1';
            $envVars['DB_PORT'] = $config['DB_PORT'] ?? ($dbDriver === 'mysql' ? '3306' : '5432');
            $envVars['DB_NAME'] = $config['DB_NAME'] ?? 'freightforge';
            $envVars['DB_USER'] = $config['DB_USER'] ?? 'root';
            $envVars['DB_PASS'] = $config['DB_PASS'] ?? '';
        }

        $content = '';
        foreach ($envVars as $key => $value) {
            $content .= "{$key}={$value}\n";
        }
        file_put_contents($this->installer->getProjectDir() . '/.env', $content);

        try {
            $ok = $this->installer->run(silent: true);
        } catch (\Throwable $e) {
            @unlink($this->installer->getProjectDir() . '/.env');
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'results' => $this->installer->getResults(),
            ];
        }

        if (!$ok) {
            @unlink($this->installer->getProjectDir() . '/.env');
            return [
                'success' => false,
                'error' => 'Some checks failed. Check the results for details.',
                'results' => $this->installer->getResults(),
            ];
        }

        if (!$this->installer->setupSchema()) {
            @unlink($this->installer->getProjectDir() . '/.env');
            return [
                'success' => false,
                'error' => 'Failed to create database tables.',
                'results' => $this->installer->getResults(),
            ];
        }

        if (!$this->installer->setupAdmin($adminName, $adminEmail, $adminPassword)) {
            @unlink($this->installer->getProjectDir() . '/.env');
            return [
                'success' => false,
                'error' => 'Failed to create admin account.',
                'results' => $this->installer->getResults(),
            ];
        }

        return [
            'success' => true,
            'results' => $this->installer->getResults(),
        ];
    }
}
