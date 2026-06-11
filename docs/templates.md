# Templates

Helix-Forge includes a lightweight PHP template engine for rendering views.

## Basic Usage

```php
use Helix\View\Template;

$template = new Template(__DIR__ . '/../app/Http/Views');
$html = $template->render('welcome', [
    'name' => 'Helix-Forge',
    'routes' => $routes,
]);
```

## View Files

Views are plain PHP files in `app/Http/Views/`. Variables are extracted into scope:

```php
<!-- app/Http/Views/welcome.php -->
<h1>Welcome to <?= $name ?></h1>

<ul>
<?php foreach ($routes as $route): ?>
    <li><?= $route['method'] ?> <?= $route['path'] ?></li>
<?php endforeach; ?>
</ul>
```

## Shared Data

Data shared across all views:

```php
$template->share('appName', 'Helix-Forge');
$template->share('year', date('Y'));
```

Now every rendered view has access to `$appName` and `$year`.

## In Controllers

```php
use Helix\View\Template;
use Helix\Http\Response;

class WelcomeController
{
    public function __construct(
        private Template $template
    ) {}

    public function index(): Response
    {
        $html = $this->template->render('welcome', [
            'message' => 'Hello World',
        ]);
        return Response::html($html);
    }
}
```

## Nested Views

Use dot notation for subdirectories:

```php
// Renders app/Http/Views/partials/header.php
$template->render('partials.header', ['title' => 'My Page']);
```

## Checking View Existence

```php
if ($template->exists('admin.dashboard')) {
    return $template->render('admin.dashboard', $data);
}
```
