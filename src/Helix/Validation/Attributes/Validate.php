<?php

namespace Helix\Validation\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Validate
{
    public function __construct(
        public readonly array $rules = [],
        public readonly ?string $message = null
    ) {}
}
