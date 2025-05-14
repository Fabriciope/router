<?php

namespace Fabriciope\Router\Support;

trait ClassAndMethodChecker
{
    private function classExists(string $controller): bool
    {
        return is_string($controller) && class_exists($controller);
    }

    private function methodExists(string $controller, string $action): bool
    {
        return is_string($action) && method_exists($controller, $action);
    }
}
