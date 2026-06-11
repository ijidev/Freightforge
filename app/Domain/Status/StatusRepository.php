<?php

namespace App\Domain\Status;

use Helix\Database\Repository;

class StatusRepository extends Repository
{
    protected string $table = 'statuses';

    public function findAllOrdered(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY sort_order ASC");
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->findOneBy('slug', $slug);
    }

    public function exists(string $slug): bool
    {
        return $this->findBySlug($slug) !== null;
    }
}
