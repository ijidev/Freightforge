<?php

namespace App\Domain\User;

use Helix\Database\Attributes\Column;
use Helix\Database\Attributes\Entity;

#[Entity(table: 'users')]
class User
{
    #[Column(type: 'id')]
    public int $id;

    #[Column(type: 'string', length: 255)]
    public string $name;

    #[Column(type: 'string', unique: true)]
    public string $email;

    #[Column(type: 'string', length: 255)]
    public string $password;

    #[Column(type: 'datetime', nullable: true)]
    public ?\DateTime $created_at = null;
}
