<?php

namespace Helix\Database\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Entity
{
    public function __construct(
        public readonly string $table,
        public readonly ?string $connection = null
    ) {}
}
