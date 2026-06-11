<?php

namespace Helix\Database;

abstract class Repository
{
    protected \PDO $pdo;
    protected string $table = '';
    protected string $entityClass = '';

    public function __construct()
    {
        $this->connect();
    }

    protected function connect(): void
    {
        $driver = $_ENV['DB_DRIVER'] ?? 'sqlite';
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname = $_ENV['DB_NAME'] ?? 'helix';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? '';
        $path = $_ENV['DB_PATH'] ?? 'storage/database.sqlite';

        if ($driver === 'sqlite') {
            if (!str_starts_with($path, '/') && !str_contains($path, ':\\')) {
                $path = dirname(__DIR__, 3) . '/' . $path;
            }
            $dsn = "sqlite:{$path}";
        } else {
            $dsn = "{$driver}:host={$host};dbname={$dbname}";
        }

        $this->pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }

    protected function quoteIdentifier(string $name): string
    {
        $driver = $_ENV['DB_DRIVER'] ?? 'sqlite';
        if ($driver === 'mysql') {
            return "`{$name}`";
        }
        if ($driver === 'pgsql') {
            return "\"{$name}\"";
        }
        return $name;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function findById(int|string $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findBy(string $column, mixed $value): array
    {
        $col = $this->quoteIdentifier($column);
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$col} = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public function findOneBy(string $column, mixed $value): ?array
    {
        $col = $this->quoteIdentifier($column);
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$col} = ? LIMIT 1");
        $stmt->execute([$value]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): array
    {
        $columns = implode(', ', array_map(fn($col) => $this->quoteIdentifier($col), array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->pdo->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));

        $id = $this->pdo->lastInsertId();
        return $this->findById($id);
    }

    public function update(int|string $id, array $data): ?array
    {
        $sets = implode(', ', array_map(fn($col) => $this->quoteIdentifier($col) . ' = ?', array_keys($data)));
        $values = array_values($data);
        $values[] = $id;

        $stmt = $this->pdo->prepare(
            "UPDATE {$this->table} SET {$sets} WHERE id = ?"
        );
        $stmt->execute($values);

        return $this->findById($id);
    }

    public function delete(int|string $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTable(): string
    {
        return $this->table;
    }
}
