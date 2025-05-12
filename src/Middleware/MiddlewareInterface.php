<?php

namespace Fabriciope\Router\Middleware;

use Fabriciope\Router\Request\Request;

interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): void;
}
