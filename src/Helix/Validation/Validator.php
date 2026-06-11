<?php

namespace Helix\Validation;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleSet) {
            $ruleList = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;
            $value = $data[$field] ?? null;

            foreach ($ruleList as $rule) {
                $this->applyRule($field, $value, $rule, $data);
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, mixed $value, string $rule, array $data): void
    {
        $params = [];
        if (str_contains($rule, ':')) {
            [$rule, $paramStr] = explode(':', $rule, 2);
            $params = explode(',', $paramStr);
        }

        $method = 'rule' . ucfirst($rule);
        if (method_exists($this, $method)) {
            $this->$method($field, $value, $params, $data);
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    private function ruleRequired(string $field, mixed $value, array $params, array $data): void
    {
        if ($value === null || $value === '') {
            $this->addError($field, "{$field} is required");
        }
    }

    private function ruleString(string $field, mixed $value, array $params, array $data): void
    {
        if ($value !== null && !is_string($value)) {
            $this->addError($field, "{$field} must be a string");
        }
    }

    private function ruleInt(string $field, mixed $value, array $params, array $data): void
    {
        if ($value !== null && !is_int($value) && !ctype_digit((string) $value)) {
            $this->addError($field, "{$field} must be an integer");
        }
    }

    private function ruleEmail(string $field, mixed $value, array $params, array $data): void
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "{$field} must be a valid email");
        }
    }

    private function ruleMax(string $field, mixed $value, array $params, array $data): void
    {
        if ($value !== null) {
            $max = (int) ($params[0] ?? 0);
            if (is_string($value) && mb_strlen($value) > $max) {
                $this->addError($field, "{$field} must not exceed {$max} characters");
            } elseif (is_numeric($value) && $value > $max) {
                $this->addError($field, "{$field} must not exceed {$max}");
            }
        }
    }

    private function ruleMin(string $field, mixed $value, array $params, array $data): void
    {
        if ($value !== null) {
            $min = (int) ($params[0] ?? 0);
            if (is_string($value) && mb_strlen($value) < $min) {
                $this->addError($field, "{$field} must be at least {$min} characters");
            } elseif (is_numeric($value) && $value < $min) {
                $this->addError($field, "{$field} must be at least {$min}");
            }
        }
    }

    private function ruleBoolean(string $field, mixed $value, array $params, array $data): void
    {
        if ($value !== null && !in_array($value, [true, false, 1, 0, '1', '0'], true)) {
            $this->addError($field, "{$field} must be a boolean");
        }
    }

    private function ruleArray(string $field, mixed $value, array $params, array $data): void
    {
        if ($value !== null && !is_array($value)) {
            $this->addError($field, "{$field} must be an array");
        }
    }
}
