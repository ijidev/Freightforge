<?php

namespace Helix\Foundation;

use Helix\Container\Container;
use Helix\Http\Kernel;
use Helix\Http\Request;
use Helix\Http\Response;
use Helix\Routing\Router;

class Application
{
    private bool $booted = false;

    public function __construct(
        private readonly Container $container
    ) {}

    public static function create(): static
    {
        $container = new Container();
        $instance = new static($container);
        self::$instance = $instance;
        return $instance;
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->loadEnvironment();

        $this->container->singleton(self::class, $this);
        $this->container->singleton(Container::class, $this->container);

        $router = new Router($this->container);
        $this->container->singleton(Router::class, $router);

        $kernel = new Kernel($this->container, $router);
        $this->container->singleton(Kernel::class, $kernel);

        $this->loadConfig();
        $this->registerControllers();
        $this->loadRoutes();

        $this->booted = true;
    }

    private function loadEnvironment(): void
    {
        $envFile = __DIR__ . '/../../../.env';
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key = trim($parts[0]);
            $value = trim($parts[1]);

            if (preg_match('/^"(.*)"$/', $value, $m)) {
                $value = $m[1];
            } elseif (preg_match("/^'(.*)'$/", $value, $m)) {
                $value = $m[1];
            }

            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }

    public function run(): void
    {
        $this->boot();

        $kernel = $this->container->get(Kernel::class);
        $request = Request::fromGlobals();
        $this->container->singleton(Request::class, $request);
        $response = $kernel->handle($request);
        $response->send();
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    private function loadConfig(): void
    {
        $configDir = __DIR__ . '/../../../config';
        if (is_dir($configDir)) {
            foreach (glob($configDir . '/*.php') as $file) {
                $key = basename($file, '.php');
                $config = require $file;
                if (is_array($config)) {
                    $this->container->set("config.{$key}", $config);
                }
            }
        }
    }

    private function registerControllers(): void
    {
        $router = $this->container->get(Router::class);
        $controllersDir = realpath(__DIR__ . '/../../../app/Http/Controllers');

        if ($controllersDir === false || !is_dir($controllersDir)) {
            return;
        }

        $appDir = realpath(__DIR__ . '/../../../app/') . DIRECTORY_SEPARATOR;

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($controllersDir)
        );

        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $fullPath = $file->getPathname();
                $relativePath = 'App\\' . str_replace(
                    [$appDir, '.php'],
                    ['', ''],
                    $fullPath
                );
                if (class_exists($relativePath)) {
                    $router->registerController($relativePath);
                }
            }
        }
    }

    private static ?self $instance = null;

    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    private function loadRoutes(): void
    {
        $router = $this->container->get(Router::class);

        foreach (['api.php', 'web.php'] as $file) {
            $routesFile = __DIR__ . '/../../../routes/' . $file;
            if (file_exists($routesFile)) {
                $routeLoader = function (Router $router) use ($routesFile) {
                    require $routesFile;
                };
                $routeLoader($router);
            }
        }
    }
}
