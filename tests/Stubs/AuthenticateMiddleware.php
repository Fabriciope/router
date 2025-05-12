<?php

namespace Tests\Stubs;

use Fabriciope\Router\Middleware\MiddlewareInterface;
use Fabriciope\Router\Request\Request;

class AuthenticateMiddleware implements MiddlewareInterface
{
    /**
     * This middleware verifies if a request is a API request
     *
     * @param \Fabriciope\Router\Request\Request $request
     * @param callable $next
     * @return void
     */
    public function handle(Request $request, callable $next): void
    {
        # for tests
    }
}
