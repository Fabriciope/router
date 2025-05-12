<?php

namespace Fabriciope\Router\Exceptions;

class RouteNotFoundException extends \Exception
{
    private string $path;

    public function __construct(string $path, string $message = '')
    {
        parent::__construct("Invalid path, path: {$path} not found");

        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
