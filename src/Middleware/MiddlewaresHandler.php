<?php

namespace Fabriciope\Router\Middleware;

use Fabriciope\Router\Request\Request;

class MiddlewaresHandler
{
    /**
     * @param \Fabriciope\Router\Middleware\MiddlewareInterface[] $middlewares
     */
    public function __construct(
        private array $middlewares
    )
    {
    }

    public function handle(Request $request): void
    {
        $iterator = $this->createIterator($request);
        $iterator();
    }

    private function createIterator(Request $request): callable
    {
        $copiedMiddlewares = $this->middlewares;
        return $this->nextFunc($request, $copiedMiddlewares);
    }

    private function nextFunc(Request $request, array &$remainingMiddlewares): callable
    {
        return function () use ($request, $remainingMiddlewares) {
            $currentMiddlewareClass = array_shift($remainingMiddlewares);

            if (!isset($currentMiddlewareClass)) {
                return;
            }

            $currentMiddleware = new $currentMiddlewareClass();
            $currentMiddleware->handle(
                request: $request,
                next: $this->nextFunc($request, $remainingMiddlewares)
            );
        };
    }
}
