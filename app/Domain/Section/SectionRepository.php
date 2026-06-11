<?php

namespace App\Domain\Section;

use Helix\Database\Repository;

class SectionRepository extends Repository
{
    protected string $table = 'sections';
    protected string $entityClass = Section::class;

    public function findByPage(string $page): array
    {
        return $this->findBy('page', $page);
    }

    public function findOne(string $page, string $sectionKey): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE page = ? AND section_key = ? LIMIT 1"
        );
        $stmt->execute([$page, $sectionKey]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function upsert(string $page, string $sectionKey, array $data): void
    {
        $existing = $this->findOne($page, $sectionKey);
        $data['page'] = $page;
        $data['section_key'] = $sectionKey;

        if ($existing) {
            $data['updated_at'] = date('c');
            $this->update($existing['id'], $data);
        } else {
            $data['created_at'] = date('c');
            $data['updated_at'] = date('c');
            $this->create($data);
        }
    }

    public function getAllGrouped(): array
    {
        $sections = $this->findAll();
        $grouped = [];
        foreach ($sections as $s) {
            $grouped[$s['page']][] = $s;
        }
        return $grouped;
    }
}
