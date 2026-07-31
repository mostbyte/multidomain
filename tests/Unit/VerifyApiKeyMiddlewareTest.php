<?php

use Illuminate\Http\Request;
use Mostbyte\Multidomain\Http\Middlewares\VerifyApiKeyMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

function apiKeyRequest(?string $key = null): Request
{
    $headers = $key === null ? [] : ['HTTP_X_API_KEY' => $key];

    return Request::create('/tenant/multidomain/schema', 'POST', [], [], [], $headers);
}

it('rejects requests when no api key is configured', function () {
    config(['multidomain.api_key' => null]);

    $middleware = new VerifyApiKeyMiddleware;

    try {
        $middleware->handle(apiKeyRequest('anything'), fn () => response('ok'));
        $this->fail('Expected an HttpException.');
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(403);
    }
});

it('rejects requests when the configured api key is an empty string', function () {
    config(['multidomain.api_key' => '']);

    $middleware = new VerifyApiKeyMiddleware;

    try {
        $middleware->handle(apiKeyRequest(''), fn () => response('ok'));
        $this->fail('Expected an HttpException.');
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(403);
    }
});

it('rejects requests without the api key header', function () {
    config(['multidomain.api_key' => 'secret']);

    $middleware = new VerifyApiKeyMiddleware;

    try {
        $middleware->handle(apiKeyRequest(), fn () => response('ok'));
        $this->fail('Expected an HttpException.');
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(401);
    }
});

it('rejects requests with a wrong api key', function () {
    config(['multidomain.api_key' => 'secret']);

    $middleware = new VerifyApiKeyMiddleware;

    try {
        $middleware->handle(apiKeyRequest('wrong-secret'), fn () => response('ok'));
        $this->fail('Expected an HttpException.');
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(403);
    }
});

it('passes the request through with the correct api key', function () {
    config(['multidomain.api_key' => 'secret']);

    $middleware = new VerifyApiKeyMiddleware;
    $called = false;

    $response = $middleware->handle(apiKeyRequest('secret'), function () use (&$called) {
        $called = true;

        return response('ok');
    });

    expect($called)->toBeTrue();
    expect($response->getStatusCode())->toBe(200);
});
