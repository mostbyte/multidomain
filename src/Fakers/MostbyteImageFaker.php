<?php

namespace Mostbyte\Multidomain\Fakers;

use Faker\Provider\Base;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mostbyte\Multidomain\Managers\ConsoleManager;

class MostbyteImageFaker extends Base
{
    public function mostbyteImage(string $dir = '', int $width = 500, int $height = 500, ?string $schema = null): string
    {
        $schema = $schema ?? app(ConsoleManager::class)->getSchema();

        $name = $dir.'/'.Str::random(10).'.jpg';
        Storage::disk('public')->put($name,
            file_get_contents("https://loremflickr.com/$width/$height")
        );

        return "/storage/$schema/$name";
    }
}
