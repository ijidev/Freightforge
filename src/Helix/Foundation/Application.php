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
        try {
            $this->boot();

            $kernel = $this->container->get(Kernel::class);
            $request = Request::fromGlobals();
            $this->container->singleton(Request::class, $request);
            $response = $kernel->handle($request);
            $response->send();
        } catch (\Throwable $e) {
            $config = $this->container->has('config.app')
                ? $this->container->get('config.app')
                : ['debug' => ($_ENV['APP_DEBUG'] ?? true)];

            $debug = $config['debug'] ?? false;
            $status = 500;
            $message = $e->getMessage();

            if ($debug) {
                $html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
                    . '<meta name="viewport" content="width=device-width,initial-scale=1">'
                    . '<title>500 — FreightForge</title>'
                    . '<style>'
                    . '*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}'
                    . 'body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#f8fafc;color:#1e293b;min-height:100vh;display:flex;align-items:center;justify-content:center}'
                    . '.card{background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:3rem;max-width:720px;width:90%;text-align:left}'
                    . '.code{font-size:4rem;font-weight:800;color:#f97316;line-height:1;margin-bottom:0.5rem;text-align:center}'
                    . 'h1{font-size:1.5rem;font-weight:700;margin-bottom:0.75rem;text-align:center}'
                    . 'h2{font-size:1rem;font-weight:600;margin-top:1.5rem;margin-bottom:0.5rem;color:#475569}'
                    . '.meta{font-size:0.85rem;color:#94a3b8;background:#f1f5f9;padding:0.75rem 1rem;border-radius:0.5rem;margin-bottom:1rem;word-break:break-word}'
                    . '.trace{font-size:0.8rem;color:#64748b;background:#f8fafc;border:1px solid #e2e8f0;border-radius:0.5rem;padding:1rem;margin-bottom:1.5rem;overflow-x:auto;white-space:pre;font-family:monospace;line-height:1.5}'
                    . '</style></head><body><div class="card">'
                    . '<div class="code">' . $status . '</div>'
                    . '<h1>' . htmlspecialchars(get_class($e)) . '</h1>'
                    . '<div class="meta"><strong>Message:</strong> ' . htmlspecialchars($message) . '</div>'
                    . '<div class="meta"><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ' : ' . $e->getLine() . '</div>'
                    . '<h2>Stack Trace</h2>'
                    . '<div class="trace">' . htmlspecialchars($e->getTraceAsString()) . '</div>'
                    . '</div></body></html>';
            } else {
                $html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
                    . '<meta name="viewport" content="width=device-width,initial-scale=1">'
                    . '<title>500 — FreightForge</title>'
                    . '<style>'
                    . '*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}'
                    . 'body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#f8fafc;color:#1e293b;min-height:100vh;display:flex;align-items:center;justify-content:center}'
                    . '.card{background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:3rem;max-width:520px;width:90%;text-align:center}'
                    . '.code{font-size:4rem;font-weight:800;color:#f97316;line-height:1;margin-bottom:0.5rem}'
                    . 'h1{font-size:1.5rem;font-weight:700;margin-bottom:0.75rem}'
                    . 'p{color:#64748b;margin-bottom:1.5rem;line-height:1.6}'
                    . '</style></head><body><div class="card">'
                    . '<div class="code">500</div>'
                    . '<h1>Internal Server Error</h1>'
                    . '<p>Something went wrong on our end.</p>'
                    . '</div></body></html>';
            }

            $response = new Response($html, $status, ['Content-Type' => 'text/html; charset=utf-8']);
            $response->send();
        }
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
