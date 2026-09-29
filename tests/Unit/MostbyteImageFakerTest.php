<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mostbyte\Multidomain\Fakers\MostbyteImageFaker;

beforeEach(function () {
    Storage::fake('public');
});

it('does not hit the network in testing and stores a local placeholder', function () {
    $faker = new MostbyteImageFaker(new Faker\Generator);

    $path = $faker->mostbyteImage('avatars', schema: 'public');

    expect($path)->toStartWith('/storage/public/avatars/');

    $storedName = Str::after($path, '/storage/public/');
    Storage::disk('public')->assertExists($storedName);

    $content = Storage::disk('public')->get($storedName);
    // JPEG signature, а не пустая/битая заглушка.
    expect(substr($content, 0, 2))->toBe("\xFF\xD8");
});
