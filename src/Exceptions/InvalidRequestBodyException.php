<?php

namespace Fabriciope\Router\Exceptions;

class InvalidRequestBodyException extends  \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
