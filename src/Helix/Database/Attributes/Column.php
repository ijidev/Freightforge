<?php

namespace Helix\Database\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Column
{
    public function __construct(
        public readonly string $type,
        public readonly ?int $length = null,
        public readonly bool $unique = false,
        public readonly bool $nullable = false,
        public readonly mixed $default = null
    ) {}
}
