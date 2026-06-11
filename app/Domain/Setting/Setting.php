<?php

namespace App\Domain\Setting;

use Helix\Database\Attributes\Column;
use Helix\Database\Attributes\Entity;

#[Entity(table: 'settings')]
class Setting
{
    #[Column(type: 'id')]
    public int $id;

    #[Column(type: 'string', unique: true)]
    public string $key;

    #[Column(type: 'text', nullable: true)]
    public ?string $value = null;

    #[Column(type: 'datetime', nullable: true)]
    public ?\DateTime $created_at = null;

    #[Column(type: 'datetime', nullable: true)]
    public ?\DateTime $updated_at = null;
}
