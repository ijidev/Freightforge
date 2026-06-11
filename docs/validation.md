# Validation

Validation is declared via the `#[Validate]` attribute on controller methods or entity properties.

## On Controller Methods

```php
use Helix\Routing\Attributes\Route;
use Helix\Validation\Attributes\Validate;

class UserController
{
    #[Route('/api/users', method: 'POST')]
    #[Validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'age' => 'required|int|min:18',
    ])]
    public function store(Request $request): JsonResponse
    {
        // Validation passes — data is safe to use
        return new JsonResponse($request->all(), 201);
    }
}
```

## Available Rules

| Rule | Description |
|------|-------------|
| `required` | Value must not be empty |
| `string` | Must be a string |
| `int` | Must be an integer |
| `email` | Must be a valid email |
| `boolean` | Must be a boolean |
| `array` | Must be an array |
| `max:N` | Maximum length/value |
| `min:N` | Minimum length/value |

## On Entity Properties

```php
use Helix\Database\Attributes\Column;
use Helix\Validation\Attributes\Validate;

class User
{
    #[Column(type: 'string', length: 255)]
    #[Validate('required|string|max:255')]
    public string $name;

    #[Column(type: 'string', unique: true)]
    #[Validate('required|email')]
    public string $email;
}
```

## Custom Validation

Extend the `Validator` class to add custom rules:

```php
use Helix\Validation\Validator;

class CustomValidator extends Validator
{
    private function rulePhone(string $field, mixed $value, array $params, array $data): void
    {
        if ($value !== null && !preg_match('/^\+?[\d\s\-()]{7,15}$/', $value)) {
            $this->addError($field, "{$field} is not a valid phone number");
        }
    }
}
```

## Validation Errors

When validation fails, a `ValidationException` is thrown with a 422 status code and error details.
