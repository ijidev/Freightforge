<?php

namespace Helix\Http;

class Response
{
    public function __construct(
        private string $content = '',
        private int $status = 200,
        private array $headers = []
    ) {}

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function withHeader(string $name, string $value): static
    {
        $clone = clone $this;
        $clone->headers[$name] = $value;
        return $clone;
    }

    public function withStatus(int $status): static
    {
        $clone = clone $this;
        $clone->status = $status;
        return $clone;
    }

    public function withContent(string $content): static
    {
        $clone = clone $this;
        $clone->content = $content;
        return $clone;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->content;
    }

    public static function html(string $content, int $status = 200): static
    {
        return new static($content, $status, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    public static function text(string $content, int $status = 200): static
    {
        return new static($content, $status, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    public static function redirect(string $url, int $status = 302): static
    {
        return new static('', $status, ['Location' => $url]);
    }
}
