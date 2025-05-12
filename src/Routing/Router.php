<?php

namespace Fabriciope\Router\Routing;

use Fabriciope\Router\Exceptions\InvalidControllerMethodSignatureException;
use Fabriciope\Router\Middleware\MiddlewareInterface;
use Fabriciope\Router\Middleware\MiddlewaresHandler;
use Fabriciope\Router\Request\DefaultRequest;
use Fabriciope\Router\Request\Request;
use Fabriciope\Router\Interfaces\Validatable;

class Router
{
    public function __construct(
        private RouteManager $routeManager,
        private Request      $request
    )
    {
    }

    public function getRouteManager(): RouteManager
    {
        return $this->routeManager;
    }

    private function getRoutes(): array
    {
        return $this->routeManager->getRoutes();
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Match route from request
     *
     * @return false|\Fabriciope\Router\Routing\Route
     */
    public function matchRequest(): false|Route
    {
        $routes = $this->getRoutes()[$this->getRequest()->getMethodName()];

        foreach ($routes as $route) {
            if (!$this->matchRequestPath($route->path)) {
                continue;
            }

            $this->request = $this->getRealRequest($route);
            return $route;
        }

        return false;
    }

    private function matchRequestPath(string $routePath): bool
    {
        $splittedRequestPath = explode("/", trim($this->getRequest()->getPath(), '/'));
        $splittedRoutePath = explode("/", trim($routePath, '/'));

        if (count($splittedRequestPath) != count($splittedRoutePath)) {
            return false;
        }

        foreach ($splittedRoutePath as $subPathIndex => $subPath) {
            if ((substr($subPath, 0, 1) == "{") and (substr($subPath, -1) == "}")) {
                continue; // NOTE: it indicates that the route has a parameter
            }

            if ($subPath != $splittedRequestPath[$subPathIndex]) {
                return false;
            }
        }

        return true;
    }

    private function getRealRequest(Route $route): ?Request
    {
        $realRequest = $this->getRequest();

        $rMethod = new \ReflectionMethod($route->controllerClass, $route->actionName);
        $rParameters = $rMethod->getParameters();

        if (count($rParameters) <= 0) {
            $realRequest->bindPathParameters($route->path);
            return $realRequest;
        }

        $requestClass = $rParameters[0]->getType()->getName();

        if ($requestClass === Request::class or $requestClass === DefaultRequest::class) {
            $realRequest->bindPathParameters($route->path);
            return $realRequest;
        }

        if (!is_subclass_of($requestClass, Request::class)) {
            throw new InvalidControllerMethodSignatureException(
                $realRequest,
                $route->controllerClass,
                $route->actionName,
                'the first controller argument must be a subclass of Request class'
            );
        }

        $realRequest = new $requestClass();
        $realRequest->bindPathParameters($route->path);
        return $realRequest;
    }

    /**
     * Dispatches the given route
     *
     * @throws Fabriciope\Router\Exceptions\InvalidRequestInputDataException
     */
    public function dispatchRoute(Route $route): void
    {
        $this->performMiddlewares($route);

        $this->performRequestValidator();

        $this->performControllerAction($route);
    }

    private function performMiddlewares(Route $route): void
    {
        $middlewareManager = new MiddlewaresHandler($route->middlewares);
        $middlewareManager->handle($this->getRequest());
    }

    private function performRequestValidator(): void
    {
        $request = $this->getRequest();
        if ($request instanceof Validatable) {
            $request->validate();
        }
    }

    private function performControllerAction(Route $route): void
    {
        $controller = new $route->controllerClass();
        $controller->{$route->actionName}($this->getRequest());
    }
}
