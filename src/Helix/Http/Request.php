<?php

namespace Helix\Http;

class Request
{
    private array $attributes = [];
    private array $routeParams = [];

    public function __construct(
        private readonly array $server,
        private readonly array $query,
        private readonly array $body,
        private readonly array $cookies,
        private readonly array $files,
        private readonly array $headers = []
    ) {}

    public static function fromGlobals(): static
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace('_', '-', substr($key, 5));
                $headers[$header] = $value;
            }
        }

        $body = $_POST;
        $raw = file_get_contents('php://input');
        if ($raw !== false && $raw !== '') {
            $parsed = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $body = array_merge($body, $parsed);
            }
        }

        return new static(
            $_SERVER,
            $_GET,
            $body,
            $_COOKIE,
            $_FILES,
            $headers
        );
    }

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $pos = strpos($uri, '?');
        return $pos !== false ? substr($uri, 0, $pos) : $uri;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function header(string $key, ?string $default = null): ?string
    {
        return $this->headers[strtoupper($key)] ?? $default;
    }

    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function setRouteParam(string $key, mixed $value): void
    {
        $this->routeParams[$key] = $value;
    }

    public function routeParam(string $key, mixed $default = null): mixed
    {
        return $this->routeParams[$key] ?? $default;
    }

    public function routeParams(): array
    {
        return $this->routeParams;
    }

    public function expectsJson(): bool
    {
        $accept = $this->header('Accept');
        return $accept !== null && str_contains($accept, 'application/json');
    }
}
