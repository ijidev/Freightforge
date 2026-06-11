<?php

namespace Helix\Validation;

class ValidationException extends \RuntimeException
{
    public function __construct(
        private readonly array $errors,
        string $message = 'Validation failed'
    ) {
        parent::__construct($message);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
