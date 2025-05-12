<?php

namespace Tests;

use Fabriciope\Router\HttpMethods;
use Fabriciope\Router\Routing\Route;
use Fabriciope\Router\Routing\RouteManager;
use Fabriciope\Router\Routing\RouteRecorder;
use Tests\Stubs\{SiteController, AuthController, AuthenticateMiddleware, GuestMiddleware };

beforeEach(function () {
    $this->routeManager = new RouteManager();
});

test('test if grouped routes matches', function (Route $expectedRoute, array $groupedRouteData) {
    $this->routeManager->newGroup()
        ->setPrefix('user')
        ->setController(SiteController::class)
        ->group(function (RouteRecorder $route) {
            $route->get('/all', 'home');
            $route->post('/create', 'home');

            $route->newGroup()
                ->setController(AuthController::class)
                ->setMiddlewares(
                    GuestMiddleware::class
                )->group(function (RouteRecorder $route) {
                    $route->patch('/auth', 'test');
                    $route->delete('/auth/delete', 'test')
                        ->setMiddlewares(AuthenticateMiddleware::class);
                    $route->post('/auth/create', 'test');

                    $route->newGroup()
                        ->setPrefix('admin')
                        ->group(function (RouteRecorder $route) {
                            $route->get('/all/created', 'test');

                            $route->newGroup()
                                ->setPrefix('allowed')
                                ->setController(SiteController::class)
                                ->group(function (RouteRecorder $route) {
                                    $route->post('/{adminId}/get', 'home');
                                });
                        });
                });
        });

    $routes = $this->routeManager->getRoutes();
    $this->assertIsArray($routes);
    foreach (['GET', 'POST', 'PATCH', 'DELETE'] as $methodName) {
        $this->assertNotEmpty($routes[$methodName]);
    }
    $this->assertEmpty($routes['PUT']);

    [$methodName, $routeIndex] = $groupedRouteData;
    assertRoute(
        $expectedRoute,
        $routes[$methodName][$routeIndex],
        'this grouped routes dooes not match'
    );
})->with([
    [
        new Route(
            path: '/user/all',
            method: HttpMethods::GET,
            controllerClass: SiteController::class,
            actionName: 'home'
        ),
        ['GET', 0],
    ],
    [
        new Route(
            method: HttpMethods::POST,
            path: '/user/create',
            controllerClass: SiteController::class,
            actionName: 'home'
        ),
        ['POST', 0]
    ],
    [
        (new Route(
            method: HttpMethods::PATCH,
            path: '/user/auth',
            controllerClass: AuthController::class,
            actionName: 'test'
        ))->setMiddlewares(GuestMiddleware::class),
        ['PATCH', 0]
    ],
    [
        (new Route(
            method: HttpMethods::DELETE,
            path: '/user/auth/delete',
            controllerClass: AuthController::class,
            actionName: 'test'
        ))->setMiddlewares(GuestMiddleware::class, AuthenticateMiddleware::class),
        ['DELETE', 0]
    ],
    [
        (new Route(
            method: HttpMethods::POST,
            path: '/user/auth/create',
            controllerClass: AuthController::class,
            actionName: 'test'
        ))->setMiddlewares(GuestMiddleware::class),
        ['POST', 1]
    ],
    [
        (new Route(
            method: HttpMethods::GET,
            path: '/user/admin/all/created',
            controllerClass: AuthController::class,
            actionName: 'test'
        ))->setMiddlewares(GuestMiddleware::class),
        ['GET', 1]
    ],
    [
        (new Route(
            method: HttpMethods::POST,
            path: '/user/admin/allowed/{adminId}/get',
            controllerClass: SiteController::class,
            actionName: 'home'
        ))->setMiddlewares(GuestMiddleware::class),
        ['POST', 2]
    ]
]);
