<?php

namespace Fabriciope\Router\Middleware\Middlewares;

use Fabriciope\Router\Middleware\MiddlewareInterface;
use Fabriciope\Router\Request\Request;
use Fabriciope\Router\Response;

class APIMiddleware implements MiddlewareInterface
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
        # NOTE: usage example
        
        #if (Request::isAPIRequest()) {
        #    $next();
        #    return;
        #}

        #Response::setContentType('text/html');
        #Response::setStatusCode(400, 'Invalid api request');

        # do something
    }
}
