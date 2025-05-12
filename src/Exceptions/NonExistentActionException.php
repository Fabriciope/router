<?php

namespace Fabriciope\Router\Exceptions;

class NonExistentActionException extends \Exception
{
    private string $actionName;

    public function __construct(string $actionName, string $message = '')
    {
        parent::__construct($message);
        $this->actionName = $actionName;
    }

    public function getAction(): string
    {
        return $this->actionName;
    }
}
