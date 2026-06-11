<?php

namespace Helix\Routing;

class RouteCollection
{
    private array $routes = [];

    public function add(string $method, string $path, callable|array $handler, ?string $name = null): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'pattern' => $this->pathToRegex($path),
            'handler' => $handler,
            'name' => $name,
            'middleware' => [],
        ];
    }

    public function addRoute(array $route): void
    {
        if (!isset($route['pattern'])) {
            $route['pattern'] = $this->pathToRegex($route['path']);
        }
        $route['method'] = strtoupper($route['method']);
        $this->routes[] = $route;
    }

    public function match(string $method, string $uri): ?array
    {
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
                return [
                    'handler' => $route['handler'],
                    'params' => $params,
                    'name' => $route['name'] ?? null,
                    'middleware' => $route['middleware'] ?? [],
                ];
            }
        }

        return null;
    }

    public function all(): array
    {
        return $this->routes;
    }

    public function toArray(): array
    {
        return array_map(fn($r) => [
            'method' => $r['method'],
            'path' => $r['path'],
            'pattern' => $r['pattern'],
            'name' => $r['name'] ?? null,
            'handler' => is_array($r['handler']) ? implode('@', $r['handler']) : 'Closure',
            'middleware' => $r['middleware'] ?? [],
        ], $this->routes);
    }

    private function pathToRegex(string $path): string
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
