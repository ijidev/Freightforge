# Database

Helix-Forge uses a **schema-first, repository-based** approach to databases. Define your schema using `#[Entity]` and `#[Column]` attributes on plain PHP classes.

## Configuration

```php
// config/database.php
return [
    'default' => 'sqlite',
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'path' => __DIR__ . '/../storage/database.sqlite',
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' => '5432',
            'database' => 'helix',
            'username' => 'postgres',
            'password' => '',
        ],
    ],
];
```

## Defining an Entity

```php
<?php

namespace App\Domain\User;

use Helix\Database\Attributes\Entity;
use Helix\Database\Attributes\Column;

#[Entity(table: 'users')]
class User
{
    #[Column(type: 'id')]
    public int $id;

    #[Column(type: 'string', length: 255)]
    public string $name;

    #[Column(type: 'string', unique: true)]
    public string $email;

    #[Column(type: 'datetime', nullable: true)]
    public ?\DateTime $created_at = null;
}
```

## Column Types

| Type | Description |
|------|-------------|
| `id` | Auto-increment primary key |
| `string` | VARCHAR with optional length |
| `integer` | INT |
| `boolean` | BOOLEAN |
| `text` | TEXT |
| `datetime` | DATETIME/TIMESTAMP |
| `decimal` | DECIMAL with precision |

## Repository Pattern

No Active Record. Use explicit repositories:

```php
<?php

namespace App\Domain\User;

use Helix\Database\Repository;

class UserRepository extends Repository
{
    protected string $table = 'users';
    protected string $entityClass = User::class;
}
```

### Repository Methods

```php
$users = $repo->findAll();
$user = $repo->findById(1);
$users = $repo->findBy('email', 'john@test.com');
$user = $repo->findOneBy('email', 'john@test.com');
$user = $repo->create(['name' => 'John', 'email' => 'john@test.com']);
$user = $repo->update(1, ['name' => 'Jane']);
$repo->delete(1);
```

## Generating Models

```bash
php helix make:model User name:string email:string
```

This creates:
- `app/Domain/User/User.php` — Entity with attributes
- `app/Domain/User/UserRepository.php` — Repository class

## Migration Generation

*(Coming soon)* `helix make:migration` will read entity attributes and generate SQL.
