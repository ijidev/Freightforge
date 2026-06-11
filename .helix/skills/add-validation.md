# Skill: Add Validation

## Steps

1. **Add `#[Validate]` attribute** to your controller method:
```php
use Helix\Validation\Attributes\Validate;

#[Route('/api/users', method: 'POST')]
#[Validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email',
])]
public function store(Request $request): JsonResponse
```

2. **Add validation on entity properties** (schema validation):
```php
#[Column(type: 'string', length: 255)]
#[Validate('required|string|max:255')]
public string $name;
```

## Rule Reference

| Rule | Description |
|------|-------------|
| `required` | Value must not be empty |
| `string` | Must be a string |
| `int` or `integer` | Must be an integer |
| `email` | Valid email format |
| `boolean` | Must be boolean |
| `array` | Must be an array |
| `min:N` | Min length (string) or value (numeric) |
| `max:N` | Max length (string) or value (numeric) |

## Combining Rules

Pipe-delimited: `'required|string|max:255'`

## Custom Rules

Extend `Helix\Validation\Validator`:

```php
class CustomValidator extends Validator
{
    private function rulePhone(string $field, mixed $value, array $params, array $data): void
    {
        if (!preg_match('/^\+?[\d\s\-()]{7,15}$/', $value)) {
            $this->addError($field, "Invalid phone number");
        }
    }
}
```
