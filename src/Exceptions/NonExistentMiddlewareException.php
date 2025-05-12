<?php

namespace Fabriciope\Router\Exceptions;

class NonExistentMiddlewareException extends \Exception
{
    private string $middleware;

    public function __construct(string $middleware, string $message = '')
    {
        parent::__construct($message);
        $this->middleware = $middleware;
    }

    public function getMiddleware(): string
    {
        return $this->middleware;
    }
}
