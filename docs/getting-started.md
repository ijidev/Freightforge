# Getting Started

## Requirements

- PHP 8.2+
- Composer
- SQLite (default) or PostgreSQL

## Installation

```bash
composer create-project helix/forge my-app
cd my-app
```

## Directory Structure

```
my-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Your controllers with #[Route] attributes
│   │   └── Views/            # PHP template files
│   └── Domain/
│       ├── User/             # Domain models grouped by feature
│       └── Product/
├── config/
│   ├── app.php               # Application configuration
│   └── database.php          # Database connections
├── routes/
│   ├── api.php               # API routes (optional fallback)
│   └── web.php               # Web routes (optional fallback)
├── public/
│   └── index.php             # Entry point
├── storage/                  # Cache, logs, SQLite database
├── .helix/
│   └── agent.yml             # AI agent configuration
└── helix                     # CLI entry point
```

## Running the Development Server

```bash
php helix serve
# or
composer run serve
```

The server starts at `http://127.0.0.1:8080`.

## Your First Route

Create a controller:

```bash
php helix make:controller Welcome
```

This generates `app/Http/Controllers/WelcomeController.php`:

```php
<?php

namespace App\Http\Controllers;

use Helix\Http\JsonResponse;
use Helix\Http\Request;
use Helix\Routing\Attributes\Route;

class WelcomeController
{
    #[Route('/welcome', method: 'GET')]
    public function index(Request $request): JsonResponse
    {
        return new JsonResponse(['message' => 'Hello from WelcomeController!']);
    }
}
```

Restart the server and visit `http://127.0.0.1:8080/welcome`.

## Available Commands

```bash
php helix serve                    # Start dev server
php helix make:controller <name>   # Generate a controller
php helix make:model <name>        # Generate a model + repository
php helix route:list                # Show all routes
php helix cache:routes              # Compile routes to cache
```
