<?php

use function PHPUnit\Framework\assertEquals;

function assertRoute(\Fabriciope\Router\Routing\Route $expected, \Fabriciope\Router\Routing\Route $actual, string $message = ''): void
{
    assertEquals($expected->method->name, $actual->method->name, $message . '. Error: method does not match');
    assertEquals($expected->path, $actual->path, $message . '. Error: path does not match');
    assertEquals($expected->controllerClass, $actual->controllerClass, $message . '. Error: controller class does not match');
    assertEquals($expected->actionName, $actual->actionName, $message . '. Error: action name does not match');
    assertEquals($expected->middlewares, $actual->middlewares, $message . '. Error: middlewares does not match');
}
