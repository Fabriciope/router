<?php

namespace Fabriciope\Router\Exceptions;

class NonExistentControllerException extends \Exception
{
    private string $controller;

    public function __construct(string $controller, string $message = '')
    {
        parent::__construct($message);
        $this->controller = $controller;
    }

    public function getController(): string
    {
        return $this->controller;
    }
}
