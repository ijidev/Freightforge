<?php

namespace App\Domain\Section;

use Helix\Database\Attributes\Column;
use Helix\Database\Attributes\Entity;

#[Entity(table: 'sections')]
class Section
{
    #[Column(type: 'id')]
    public int $id;

    #[Column(type: 'string', length: 100)]
    public string $page;

    #[Column(type: 'string', length: 100)]
    public string $section_key;

    #[Column(type: 'string', length: 255, nullable: true)]
    public ?string $title = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    public ?string $subtitle = null;

    #[Column(type: 'text', nullable: true)]
    public ?string $content = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    public ?string $image_path = null;

    #[Column(type: 'datetime', nullable: true)]
    public ?\DateTime $created_at = null;

    #[Column(type: 'datetime', nullable: true)]
    public ?\DateTime $updated_at = null;
}
