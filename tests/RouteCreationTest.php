<?php

namespace Tests;

use Fabriciope\Router\Exceptions\NonExistentActionException;
use Fabriciope\Router\Exceptions\NonExistentControllerException;
use Fabriciope\Router\HttpMethods;
use Fabriciope\Router\Routing\Route;
use Fabriciope\Router\Routing\RouteManager;
use Fabriciope\Router\Middleware\Middlewares\APIMiddleware;
use Src\Framework\Http\Middleware\Middlewares\APIMiddleware as SrcAPIMiddleware;
use Tests\Stubs\{SiteController, GuestMiddleware};

beforeEach(function () {
    $this->routeManager = new RouteManager();
});

test('test if can be created', function () {
    $route = $this->routeManager;
    $route->get('/', [SiteController::class, 'home']);
    $route->post('/create', [SiteController::class, 'home']);
    $route->put('/update', [SiteController::class, 'home']);
    $route->patch('/update-name', [SiteController::class, 'home']);
    $route->delete('/remove', [SiteController::class, 'home']);

    $routes = $this->routeManager->getRoutes();
    $this->assertIsArray($routes);

    assertRoute(
        new Route(
            path: '/',
            method: HttpMethods::GET,
            controllerClass: SiteController::class,
            actionName: 'home'
        ),
        $routes['GET'][0]
    );

    assertRoute(
        new Route(
            path: '/create',
            method: HttpMethods::POST,
            controllerClass: SiteController::class,
            actionName: 'home'
        ),
        $routes['POST'][0]
    );

    assertRoute(
        new Route(
            path: '/update',
            method: HttpMethods::PUT,
            controllerClass: SiteController::class,
            actionName: 'home'
        ),
        $routes['PUT'][0]
    );

    assertRoute(
        new Route(
            path: '/update-name',
            method: HttpMethods::PATCH,
            controllerClass: SiteController::class,
            actionName: 'home'
        ),
        $routes['PATCH'][0]
    );

    assertRoute(
        new Route(
            path: '/remove',
            method: HttpMethods::DELETE,
            controllerClass: SiteController::class,
            actionName: 'home'
        ),
        $routes['DELETE'][0]
    );
});


test('test should throw an exception when passing an invalid controller', function () {
    $this->expectException(NonExistentControllerException::class);

    $this->routeManager->get('/home', ['Wrong\\Controller\\', 'home']);
});

test('test should throw an exception when passing an invalid controller action', function () {
    $this->expectException(NonExistentActionException::class);

    $this->routeManager->get('/home', [SiteController::class, 'invalidAction']);
});

test('test should throw an exception when the same middleware is passed twice', function () {
    $this->expectException(\InvalidArgumentException::class);

    $this->routeManager->get('/home', [SiteController::class, 'home'])
        ->setMiddlewares(
            GuestMiddleware::class,
            GuestMiddleware::class
        );
});
