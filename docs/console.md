# Console

Helix-Forge ships with a powerful CLI built on Symfony Console.

## Usage

```bash
php helix <command> [options] [arguments]
```

## Available Commands

### `serve`

Start the development server:

```bash
php helix serve
php helix serve --port=3000
php helix serve --host=0.0.0.0
```

### `make:controller`

Generate a controller with `#[Route]` attribute:

```bash
php helix make:controller User
php helix make:controller Admin/Dashboard
```

Creates `app/Http/Controllers/UserController.php`.

### `make:model`

Generate an entity + repository pair:

```bash
php helix make:model User name:string email:string
php helix make:model Product title:string price:float
```

Creates `app/Domain/{Name}/{Name}.php` and `.../{Name}Repository.php`.

### `route:list`

Display all registered routes:

```bash
php helix route:list
```

Output:

```
+--------+-----------------+------+-------------------------------------------+
| Method | Path            | Name | Handler                                   |
+--------+-----------------+------+-------------------------------------------+
| GET    | /api/users      | -    | App\Http\Controllers\UserController@index |
| GET    | /api/users/{id} | -    | App\Http\Controllers\UserController@show  |
| POST   | /api/users      | -    | App\Http\Controllers\UserController@store |
+--------+-----------------+------+-------------------------------------------+
```

### `cache:routes`

Compile route attributes to cached PHP arrays (zero reflection in production):

```bash
php helix cache:routes
```

## Adding Custom Commands

Create a command class in `src/Helix/Console/Commands/` with the `#[AsCommand]` attribute:

```php
<?php

namespace Helix\Console\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:hello', description: 'Say hello')]
class HelloCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Hello from Helix-Forge!</info>');
        return Command::SUCCESS;
    }
}
```

Register it in `Helix\Console\Application`.
