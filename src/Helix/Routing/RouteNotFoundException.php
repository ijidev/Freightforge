<?php

namespace Helix\Routing;

class RouteNotFoundException extends \RuntimeException
{
    public function __construct(string $method, string $uri)
    {
        parent::__construct("Route not found: {$method} {$uri}");
    }
}
