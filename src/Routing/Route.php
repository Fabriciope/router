<?php

namespace Fabriciope\Router\Routing;

use InvalidArgumentException;
use Fabriciope\Router\Middleware\MiddlewareInterface;
use Fabriciope\Router\Exceptions\{NonExistentControllerException, NonExistentActionException, NonExistentMiddlewareException};
use Fabriciope\Router\Support\ClassAndMethodChecker;
use Fabriciope\Router\HttpMethods;

class Route
{
    use ClassAndMethodChecker;

    /**
    * Registered middlewares
    *
    * @var Fabriciope\Router\Middleware\MiddlewareInterface[] $middlewares
    */
    private array $middlewares = array();


    /**
    * Route class constructor
    *
    * @throws Fabriciope\Router\Routing\Exceptions\NonExistentControllerException
    * @throws Fabriciope\Router\Routing\Exceptions\NonExistentActionException
    */
    public function __construct(
        private HttpMethods $method,
        private string $path,
        private string $controllerClass,
        private string $actionName,
    ) {
        $controller = $this->controllerClass;
        $action = $this->actionName;

        if(!$this->classExists($controller)) {
            throw new NonExistentControllerException($controller, "the {$controller} controller does not exist for the path {$this->path}");
        }

        if(!$this->methodExists($controller, $action)) {
            throw new NonExistentActionException($action, "the {$action} method does not exists in {$controller} controller");
        }
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    /**
    * sets the route middlewares
    *
    * @param string ...$middlewares
    * @throws Fabriciope\Router\Routing\Exceptions\NonExistentMiddlewareException
    * @throws \InvalidArgumentException
    */
    public function setMiddlewares(string ...$middlewares): Route
    {
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }

        return $this;
    }

    private function addMiddleware(string $middleware): void
    {
        if (in_array($middleware, $this->middlewares)) {
            throw new \InvalidArgumentException("the {$middleware} already exists");
        }

        if(!$this->classExists($middleware)) {
            throw new NonExistentMiddlewareException($middleware, "the {$middleware} middleware does not exist");
        }

        if (!in_array(MiddlewareInterface::class, class_implements($middleware))) {
            $interfaceClass = MiddlewareInterface::class;
            throw new InvalidArgumentException("the {$middleware} middleware does not implement {$interfaceClass}");
        }

        array_push($this->middlewares, $middleware);
    }

    public function toArray(): array
    {
        return [];
    }
}
