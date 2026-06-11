<?php

namespace Helix\Http\Middleware;

use Helix\Http\Request;
use Helix\Http\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response;
}
