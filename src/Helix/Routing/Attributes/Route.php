<?php

namespace Helix\Routing\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Route
{
    public function __construct(
        public readonly string $path,
        public readonly string $method = 'GET',
        public readonly ?string $name = null,
        public readonly array $middleware = []
    ) {}
}
