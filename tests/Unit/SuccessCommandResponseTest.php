<?php

use Illuminate\Http\Request;
use Mostbyte\Multidomain\Http\Responses\SuccessCommandResponse;

it('returns success true when status is 0', function () {
    $response = new SuccessCommandResponse('done', 0);
    $httpResponse = $response->toResponse(Request::create('/'));

    $data = json_decode($httpResponse->getContent(), true);

    expect($data['success'])->toBeTrue();
    expect($data['status'])->toBe(0);
    expect($data['message'])->toBe('done');
});

it('returns success false when status is non-zero', function () {
    $response = new SuccessCommandResponse('failed', 1);
    $httpResponse = $response->toResponse(Request::create('/'));

    $data = json_decode($httpResponse->getContent(), true);

    expect($data['success'])->toBeFalse();
    expect($data['status'])->toBe(1);
    expect($data['message'])->toBe('failed');
});
