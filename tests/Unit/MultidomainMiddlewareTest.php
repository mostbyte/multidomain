<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Mostbyte\Multidomain\Http\Middlewares\MultidomainMiddleware;

it('rejects empty domain', function () {
    $middleware = new MultidomainMiddleware;
    $request = Request::create('/test', 'GET');
    $route = new Route('GET', '/{domain}/test', fn () => 'ok');
    $route->bind($request);
    $route->setParameter('domain', '');
    $request->setRouteResolver(fn () => $route);

    $middleware->handle($request, fn () => response('ok'));
})->throws(\Symfony\Component\HttpKernel\Exception\HttpException::class);

it('rejects domain with special characters', function () {
    $middleware = new MultidomainMiddleware;
    $request = Request::create('/test', 'GET');
    $route = new Route('GET', '/{domain}/test', fn () => 'ok');
    $route->bind($request);
    $route->setParameter('domain', 'test@domain');
    $request->setRouteResolver(fn () => $route);

    $middleware->handle($request, fn () => response('ok'));
})->throws(\Symfony\Component\HttpKernel\Exception\HttpException::class);

it('rejects domain with spaces', function () {
    $middleware = new MultidomainMiddleware;
    $request = Request::create('/test', 'GET');
    $route = new Route('GET', '/{domain}/test', fn () => 'ok');
    $route->bind($request);
    $route->setParameter('domain', 'my domain');
    $request->setRouteResolver(fn () => $route);

    $middleware->handle($request, fn () => response('ok'));
})->throws(\Symfony\Component\HttpKernel\Exception\HttpException::class);

it('accepts valid domain with numbers', function () {
    config(['multidomain.schema_validation.regex' => '/^[a-zA-Z0-9_\-]+$/']);

    $middleware = new MultidomainMiddleware;
    $request = Request::create('/tenant123/multidomain/schema', 'POST');
    $route = new Route('POST', '/{domain}/multidomain/{type}', fn () => 'ok');
    $route->name('mostbyte.multidomain.type');
    $route->bind($request);
    $route->setParameter('domain', 'tenant123');
    $request->setRouteResolver(fn () => $route);

    $response = $middleware->handle($request, fn () => response('ok'));
    expect($response->getStatusCode())->toBe(200);
});

it('uses custom regex from config', function () {
    config(['multidomain.schema_validation.regex' => '/^[a-z]+$/']);

    $middleware = new MultidomainMiddleware;
    $request = Request::create('/test', 'GET');
    $route = new Route('GET', '/{domain}/test', fn () => 'ok');
    $route->bind($request);
    $route->setParameter('domain', 'tenant-1');
    $request->setRouteResolver(fn () => $route);

    $middleware->handle($request, fn () => response('ok'));
})->throws(\Symfony\Component\HttpKernel\Exception\HttpException::class);
