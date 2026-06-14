<?php

namespace Helix\Installer;

class Installer
{
    private array $results = [];
    private array $config = [];

    private const MIN_PHP_MAJOR = 8;
    private const MIN_PHP_MINOR = 2;

    public function __construct(private readonly string $projectDir)
    {
    }

    public function runSystemChecks(): bool
    {
        $this->checkPhpVersion();
        $this->checkExtensions();
        $this->checkPermissions();

        $failed = array_filter($this->results, fn($r) => $r['status'] === 'fail');
        return empty($failed);
    }

    public function run(bool $silent = false): bool
    {
        if (!$silent) {
            $this->printBanner();
        }

        $this->checkPhpVersion();
        $this->checkExtensions();
        $this->checkPermissions();
        $this->setupEnv();
        $this->setupDatabase();
        $this->setupDirectories();

        if (!$silent) {
            $this->printSummary();
        }

        $failed = array_filter($this->results, fn($r) => $r['status'] === 'fail');
        return empty($failed);
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    private function printBanner(): void
    {
        echo "\n";
        echo "  ╔══════════════════════════════════════════════╗\n";
        echo "  ║          FreightForge Installer              ║\n";
        echo "  ║          Logistics Management System          ║\n";
        echo "  ╚══════════════════════════════════════════════╝\n\n";
    }

    public function addResult(string $check, string $status, string $message): void
    {
        $this->results[] = [
            'check' => $check,
            'status' => $status,
            'message' => $message,
        ];
    }

    public function checkPhpVersion(): void
    {
        $version = PHP_VERSION;
        $ok = version_compare($version, self::MIN_PHP_MAJOR . '.' . self::MIN_PHP_MINOR, '>=');

        $this->addResult(
            'PHP Version',
            $ok ? 'pass' : 'fail',
            $ok
                ? "PHP {$version} (>= " . self::MIN_PHP_MAJOR . '.' . self::MIN_PHP_MINOR . ' required)'
                : "PHP {$version} — minimum required is " . self::MIN_PHP_MAJOR . '.' . self::MIN_PHP_MINOR
        );
    }

    public function checkExtensions(): void
    {
        $required = ['pdo', 'pdo_sqlite', 'mbstring', 'ctype', 'json', 'fileinfo'];
        foreach ($required as $ext) {
            $loaded = extension_loaded($ext);
            $this->addResult(
                "Extension: {$ext}",
                $loaded ? 'pass' : 'fail',
                $loaded ? 'Loaded' : 'Missing — install php-' . $ext
            );
        }
    }

    public function checkPermissions(): void
    {
        $dirs = [
            $this->projectDir . '/storage',
            $this->projectDir . '/storage/framework',
            $this->projectDir . '/storage/framework/cache',
            $this->projectDir . '/storage/logs',
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $writable = is_writable($dir);
            $this->addResult(
                "Permission: " . str_replace($this->projectDir . '/', '', $dir) . '/',
                $writable ? 'pass' : 'fail',
                $writable ? 'Writable' : 'Not writable'
            );
        }
    }

    private function setupEnv(): void
    {
        $envFile = $this->projectDir . '/.env';
        $envExample = $this->projectDir . '/.env.example';

        if (file_exists($envFile)) {
            $this->config = parse_ini_file($envFile) ?: [];
            $this->addResult('.env file', 'pass', 'Already exists, using existing config');
            return;
        }

        if (!empty($this->config)) {
            $this->addResult('.env file', 'pass', 'Using provided configuration');
            return;
        }

        if (!file_exists($envExample)) {
            $this->addResult('.env file', 'fail', '.env.example not found');
            return;
        }

        $defaults = parse_ini_file($envExample) ?: [];
        $dbDriver = $this->prompt('Database driver', $defaults['DB_DRIVER'] ?? 'mysql', ['sqlite', 'mysql', 'pgsql']);

        $env = $defaults;
        $env['DB_DRIVER'] = $dbDriver;
        $env['APP_ENV'] = 'development';
        $env['APP_DEBUG'] = 'true';
        $env['APP_URL'] = $this->prompt('App URL', $defaults['APP_URL'] ?? 'http://localhost:8080');

        if ($dbDriver === 'sqlite') {
            $env['DB_PATH'] = $env['DB_PATH'] ?? 'storage/database.sqlite';
        } else {
            $env['DB_HOST'] = $this->prompt('Database host', $defaults['DB_HOST'] ?? '127.0.0.1');
            $env['DB_PORT'] = $this->prompt('Database port', $defaults['DB_PORT'] ?? ($dbDriver === 'mysql' ? '3306' : '5432'));
            $env['DB_NAME'] = $this->prompt('Database name', $defaults['DB_NAME'] ?? 'freightforge');
            $env['DB_USER'] = $this->prompt('Database user', $defaults['DB_USER'] ?? 'root');
            $env['DB_PASS'] = $this->prompt('Database password', $defaults['DB_PASS'] ?? '');
        }

        $content = '';
        foreach ($env as $key => $value) {
            $content .= "{$key}={$value}\n";
        }

        file_put_contents($envFile, $content);
        $this->config = $env;
        $this->addResult('.env file', 'pass', 'Created from .env.example');
    }

    public function setupDatabase(): void
    {
        $driver = $this->config['DB_DRIVER'] ?? 'mysql';

        if ($driver === 'sqlite') {
            $path = $this->config['DB_PATH'] ?? 'storage/database.sqlite';
            $fullPath = $this->projectDir . '/' . $path;

            if (!file_exists($fullPath)) {
                file_put_contents($fullPath, '');
                $this->addResult('Database', 'pass', "Created SQLite at {$path}");
            } else {
                $this->addResult('Database', 'pass', "SQLite database exists at {$path}");
            }
            return;
        }

        try {
            $dsn = "{$driver}:host={$this->config['DB_HOST']};dbname={$this->config['DB_NAME']}";
            $pdo = new \PDO($dsn, $this->config['DB_USER'] ?? '', $this->config['DB_PASS'] ?? '', [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 5,
            ]);
            $this->addResult('Database', 'pass', "Connected to {$driver}://{$this->config['DB_HOST']}/{$this->config['DB_NAME']}");
        } catch (\PDOException $e) {
            $this->addResult('Database', 'fail', "Could not connect: {$e->getMessage()}");
        }
    }

    public function setupDirectories(): void
    {
        $dirs = [
            $this->projectDir . '/storage/logs',
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
                $this->addResult('Directory', 'pass', 'Created ' . basename($dir) . '/');
            }
        }
    }

    public function setupSchema(): bool
    {
        try {
            $driver = $this->config['DB_DRIVER'] ?? 'mysql';
            $dbUser = $this->config['DB_USER'] ?? '';
            $dbPass = $this->config['DB_PASS'] ?? '';

            if ($driver === 'sqlite') {
                $path = $this->projectDir . '/' . ($this->config['DB_PATH'] ?? 'storage/database.sqlite');
                $dsn = "sqlite:{$path}";
            } else {
                $host = $this->config['DB_HOST'] ?? '127.0.0.1';
                $dbname = $this->config['DB_NAME'] ?? 'freightforge';
                $dsn = "{$driver}:host={$host};dbname={$dbname}";
            }

            $pdo = new \PDO($dsn, $dbUser, $dbPass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $tables = [];

            if ($driver === 'sqlite') {
                $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    password TEXT NOT NULL,
                    created_at TEXT NOT NULL DEFAULT (datetime('now'))
                )");
                $tables[] = 'users';

                $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    key TEXT NOT NULL UNIQUE,
                    value TEXT,
                    created_at TEXT NOT NULL DEFAULT (datetime('now')),
                    updated_at TEXT NOT NULL DEFAULT (datetime('now'))
                )");
                $tables[] = 'settings';

                $pdo->exec("CREATE TABLE IF NOT EXISTS sections (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    page TEXT NOT NULL,
                    section_key TEXT NOT NULL,
                    title TEXT,
                    subtitle TEXT,
                    content TEXT,
                    image_path TEXT,
                    created_at TEXT NOT NULL DEFAULT (datetime('now')),
                    updated_at TEXT NOT NULL DEFAULT (datetime('now'))
                )");
                $tables[] = 'sections';

                $pdo->exec("CREATE TABLE IF NOT EXISTS shipments (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    tracking_number TEXT NOT NULL UNIQUE,
                    origin TEXT NOT NULL,
                    destination TEXT NOT NULL,
                    status TEXT NOT NULL DEFAULT 'pending',
                    sender_name TEXT NOT NULL,
                    sender_email TEXT NOT NULL,
                    sender_phone TEXT,
                    recipient_name TEXT NOT NULL,
                    recipient_email TEXT NOT NULL,
                    recipient_phone TEXT,
                    weight REAL,
                    description TEXT,
                    created_at TEXT NOT NULL DEFAULT (datetime('now')),
                    updated_at TEXT NOT NULL DEFAULT (datetime('now'))
                )");
                $tables[] = 'shipments';

                $pdo->exec("CREATE TABLE IF NOT EXISTS statuses (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    slug TEXT NOT NULL UNIQUE,
                    color TEXT NOT NULL DEFAULT 'blue',
                    sort_order INTEGER NOT NULL DEFAULT 0,
                    created_at TEXT NOT NULL DEFAULT (datetime('now'))
                )");
                $tables[] = 'statuses';

                $pdo->exec("CREATE TABLE IF NOT EXISTS shipment_statuses (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    shipment_id INTEGER NOT NULL,
                    status TEXT NOT NULL,
                    remark TEXT,
                    created_at TEXT NOT NULL DEFAULT (datetime('now'))
                )");
                $tables[] = 'shipment_statuses';

                $stmt = $pdo->query("SELECT COUNT(*) FROM statuses");
                if ($stmt->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO statuses (name, slug, color, sort_order) VALUES
                        ('Pending', 'pending', 'yellow', 1),
                        ('In Transit', 'in_transit', 'blue', 2),
                        ('Delivered', 'delivered', 'green', 3),
                        ('Cancelled', 'cancelled', 'red', 4)
                    ");
                }
            } else {
                $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                $tables[] = 'users';

                $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    `key` VARCHAR(255) NOT NULL UNIQUE,
                    `value` TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )");
                $tables[] = 'settings';

                $pdo->exec("CREATE TABLE IF NOT EXISTS sections (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    page VARCHAR(100) NOT NULL,
                    section_key VARCHAR(100) NOT NULL,
                    title VARCHAR(255),
                    subtitle VARCHAR(255),
                    content TEXT,
                    image_path VARCHAR(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )");
                $tables[] = 'sections';

                $pdo->exec("CREATE TABLE IF NOT EXISTS shipments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    tracking_number VARCHAR(50) NOT NULL UNIQUE,
                    origin VARCHAR(255) NOT NULL,
                    destination VARCHAR(255) NOT NULL,
                    status VARCHAR(50) NOT NULL DEFAULT 'pending',
                    sender_name VARCHAR(255) NOT NULL,
                    sender_email VARCHAR(255) NOT NULL,
                    sender_phone VARCHAR(50),
                    recipient_name VARCHAR(255) NOT NULL,
                    recipient_email VARCHAR(255) NOT NULL,
                    recipient_phone VARCHAR(50),
                    weight DECIMAL(10,2),
                    description TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )");
                $tables[] = 'shipments';

                $pdo->exec("CREATE TABLE IF NOT EXISTS statuses (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    slug VARCHAR(100) NOT NULL UNIQUE,
                    color VARCHAR(50) NOT NULL DEFAULT 'blue',
                    sort_order INT NOT NULL DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                $tables[] = 'statuses';

                $pdo->exec("CREATE TABLE IF NOT EXISTS shipment_statuses (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    shipment_id INT NOT NULL,
                    status VARCHAR(50) NOT NULL,
                    remark TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                $tables[] = 'shipment_statuses';

                $stmt = $pdo->query("SELECT COUNT(*) FROM statuses");
                if ($stmt->fetchColumn() == 0) {
                    $pdo->exec("INSERT INTO statuses (name, slug, color, sort_order) VALUES
                        ('Pending', 'pending', 'yellow', 1),
                        ('In Transit', 'in_transit', 'blue', 2),
                        ('Delivered', 'delivered', 'green', 3),
                        ('Cancelled', 'cancelled', 'red', 4)
                    ");
                }
            }

            $this->addResult('Database Schema', 'pass', 'Created tables: ' . implode(', ', $tables));
            return true;
        } catch (\Throwable $e) {
            $this->addResult('Database Schema', 'fail', "Could not create tables: {$e->getMessage()}");
            return false;
        }
    }

    public function setupAdmin(string $name, string $email, string $password): bool
    {
        if (empty($name) || empty($email) || empty($password)) {
            $this->addResult('Admin User', 'fail', 'Name, email, and password are required');
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addResult('Admin User', 'fail', "Invalid email address: {$email}");
            return false;
        }

        if (strlen($password) < 6) {
            $this->addResult('Admin User', 'fail', 'Password must be at least 6 characters');
            return false;
        }

        try {
            $driver = $this->config['DB_DRIVER'] ?? 'mysql';
            $dbUser = $this->config['DB_USER'] ?? '';
            $dbPass = $this->config['DB_PASS'] ?? '';

            if ($driver === 'sqlite') {
                $path = $this->projectDir . '/' . ($this->config['DB_PATH'] ?? 'storage/database.sqlite');
                $dsn = "sqlite:{$path}";
            } else {
                $host = $this->config['DB_HOST'] ?? '127.0.0.1';
                $dbname = $this->config['DB_NAME'] ?? 'freightforge';
                $dsn = "{$driver}:host={$host};dbname={$dbname}";
            }

            $pdo = new \PDO($dsn, $dbUser, $dbPass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);

            $this->addResult('Admin User', 'pass', "Created admin: {$email}");
            return true;
        } catch (\Throwable $e) {
            $this->addResult('Admin User', 'fail', "Could not create admin: {$e->getMessage()}");
            return false;
        }
    }

    public function seedDatabase(): void
    {
        try {
            $driver = $this->config['DB_DRIVER'] ?? 'mysql';
            $dbUser = $this->config['DB_USER'] ?? '';
            $dbPass = $this->config['DB_PASS'] ?? '';

            if ($driver === 'sqlite') {
                $path = $this->projectDir . '/' . ($this->config['DB_PATH'] ?? 'storage/database.sqlite');
                $dsn = "sqlite:{$path}";
            } else {
                $host = $this->config['DB_HOST'] ?? '127.0.0.1';
                $dbname = $this->config['DB_NAME'] ?? 'freightforge';
                $dsn = "{$driver}:host={$host};dbname={$dbname}";
            }

            $pdo = new \PDO($dsn, $dbUser, $dbPass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            // Seed settings
            $defaultSettings = [
                'site_name' => 'FreightForge',
                'contact_email' => 'support@freightforge.com',
            ];

            if ($driver === 'sqlite') {
                $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)");
            } else {
                $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
            }

            foreach ($defaultSettings as $key => $value) {
                $stmt->execute([$key, $value]);
            }

            $this->addResult('Database Seeding', 'pass', 'Initial data seeded');
        } catch (\Throwable $e) {
            $this->addResult('Database Seeding', 'fail', "Seeding failed: {$e->getMessage()}");
        }
    }

    public function createSqlDump(): void
    {
        try {
            $driver = $this->config['DB_DRIVER'] ?? 'mysql';
            $dumpDir = $this->projectDir . '/storage/dumps';
            if (!is_dir($dumpDir)) {
                mkdir($dumpDir, 0775, true);
            }

            $timestamp = date('Ymd_His');
            $dumpFile = $dumpDir . '/db_dump_' . $timestamp . '.sql';

            if ($driver === 'sqlite') {
                $path = $this->config['DB_PATH'] ?? 'storage/database.sqlite';
                $fullPath = $this->projectDir . '/' . $path;
                
                if (!file_exists($fullPath)) {
                    throw new \Exception("SQLite database file not found at {$fullPath}");
                }

                $command = "sqlite3 " . escapeshellarg($fullPath) . " .dump > " . escapeshellarg($dumpFile);
                exec($command, $output, $returnCode);
                
                if ($returnCode !== 0) {
                    throw new \Exception("Failed to create SQLite dump. Ensure sqlite3 CLI is installed.");
                }
            } else {
                $sourceFile = $this->projectDir . '/storage/DB/freightforge.sql';
                
                if (file_exists($sourceFile)) {
                    if (copy($sourceFile, $dumpFile)) {
                        $this->addResult('SQL Dump', 'pass', "Created dump from template at {$dumpFile}");
                    } else {
                        throw new \Exception("Failed to copy template SQL dump.");
                    }
                } else {
                    $host = $this->config['DB_HOST'] ?? '127.0.0.1';
                    $dbname = $this->config['DB_NAME'] ?? 'freightforge';
                    $user = $this->config['DB_USER'] ?? 'root';
                    $pass = $this->config['DB_PASS'] ?? '';

                    // Using mysqldump
                    $command = sprintf(
                        'mysqldump -h %s -u %s %s%s > %s',
                        escapeshellarg($host),
                        escapeshellarg($user),
                        ($pass !== '' ? "-p" . escapeshellarg($pass) . " " : ""),
                        escapeshellarg($dbname),
                        escapeshellarg($dumpFile)
                    );
                    
                    exec($command, $output, $returnCode);

                    if ($returnCode !== 0) {
                        throw new \Exception("Failed to create MySQL dump. Ensure mysqldump is installed and accessible.");
                    }
                    $this->addResult('SQL Dump', 'pass', "Created dump via mysqldump at {$dumpFile}");
                }
            }

            $this->addResult('SQL Dump', 'pass', "Created dump at {$dumpFile}");
        } catch (\Throwable $e) {
            $this->addResult('SQL Dump', 'fail', "Dump failed: {$e->getMessage()}");
        }
    }

    public function checkTables(array $expectedTables): void
    {
        try {
            $driver = $this->config['DB_DRIVER'] ?? 'mysql';
            $dbUser = $this->config['DB_USER'] ?? '';
            $dbPass = $this->config['DB_PASS'] ?? '';

            if ($driver === 'sqlite') {
                $path = $this->projectDir . '/' . ($this->config['DB_PATH'] ?? 'storage/database.sqlite');
                $dsn = "sqlite:{$path}";
            } else {
                $host = $this->config['DB_HOST'] ?? '127.0.0.1';
                $dbname = $this->config['DB_NAME'] ?? 'freightforge';
                $dsn = "{$driver}:host={$host};dbname={$dbname}";
            }

            $pdo = new \PDO($dsn, $dbUser, $dbPass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $missingTables = [];
            foreach ($expectedTables as $table) {
                if ($driver === 'sqlite') {
                    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='" . $table . "'");
                } else {
                    $stmt = $pdo->query("SHOW TABLES LIKE " . $pdo->quote($table));
                }

                if (!$stmt->fetch()) {
                    $missingTables[] = $table;
                }
            }

            if (empty($missingTables)) {
                $this->addResult('Table Check', 'pass', 'All expected tables exist');
            } else {
                $this->addResult('Table Check', 'fail', 'Missing tables: ' . implode(', ', $missingTables));
            }
        } catch (\Throwable $e) {
            $this->addResult('Table Check', 'fail', "Check failed: {$e->getMessage()}");
        }
    }

    private function printSummary(): void
    {
        $pass = count(array_filter($this->results, fn($r) => $r['status'] === 'pass'));
        $warn = count(array_filter($this->results, fn($r) => $r['status'] === 'warn'));
        $fail = count(array_filter($this->results, fn($r) => $r['status'] === 'fail'));

        echo "\n  ─── Check Results ──────────────────────────────\n\n";

        foreach ($this->results as $result) {
            $icon = match ($result['status']) {
                'pass' => '  ✓',
                'warn' => '  ⚠',
                'fail' => '  ✗',
                default => '  •',
            };
            printf("  %s  %-30s %s\n", $icon, $result['check'], $result['message']);
        }

        echo "\n  ─── Summary ────────────────────────────────────\n";
        echo "  ✓ Passed: {$pass}\n";
        if ($warn > 0) echo "  ⚠ Warnings: {$warn}\n";
        if ($fail > 0) echo "  ✗ Failed: {$fail}\n";
        echo "\n";

        if ($fail === 0) {
            echo "  🚀  Start the dev server:\n";
            echo "     php helix serve\n\n";
            echo "  🌐  Open in browser:\n";
            echo "     http://127.0.0.1:8080\n\n";
        } else {
            echo "  ⚠  Fix the errors above, then run:\n";
            echo "     php helix install\n\n";
        }

        echo "  ──────────────────────────────────────────────\n\n";
    }

    private function prompt(string $label, string $default, array $valid = []): string
    {
        if (PHP_SAPI !== 'cli' || !stream_isatty(STDIN)) {
            return $default;
        }

        $validStr = $valid ? ' [' . implode('/', $valid) . ']' : '';
        $prompt = "  {$label}{$validStr} [{$default}]: ";

        if (function_exists('readline')) {
            $input = readline($prompt);
        } else {
            echo $prompt;
            $input = fgets(STDIN);
        }

        $input = ($input === false) ? '' : trim($input);

        if ($input === '') {
            return $default;
        }

        if (!empty($valid) && !in_array($input, $valid, true)) {
            echo "  Invalid choice, using default: {$default}\n";
            return $default;
        }

        return $input;
    }
}
