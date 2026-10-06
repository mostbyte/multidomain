<?php

namespace Mostbyte\Multidomain\Fakers;

use Faker\Provider\Base;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mostbyte\Multidomain\Managers\ConsoleManager;
use Throwable;

class MostbyteImageFaker extends Base
{
    /**
     * Минимальный валидный JPEG (1x1 px) — плейсхолдер, когда сеть недоступна
     * или сидинг запущен в testing (loremflickr.com отвечает 401 и валит сидинг).
     */
    private const PLACEHOLDER_JPEG_BASE64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAMCAgICAgMCAgIDAwMDBAYEBAQEBAgGBgUGCQgKCgkICQkKDA8MCgsOCwkJDRENDg8QEBEQCgwSExIQEw8QEBD/2wBDAQMDAwQDBAgEBAgQCwkLEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBD/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAj/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCdABmX/9k=';

    public function mostbyteImage(string $dir = '', int $width = 500, int $height = 500, ?string $schema = null): string
    {
        $schema = $schema ?? app(ConsoleManager::class)->getSchema();

        $name = $dir.'/'.Str::random(10).'.jpg';
        Storage::disk('public')->put($name, $this->fetchImage($width, $height));

        return "/storage/$schema/$name";
    }

    /**
     * В testing сеть не трогаем вовсе. В остальных окружениях сетевой сбой
     * (недоступность loremflickr, 401 и т.п.) не должен ронять сидинг.
     */
    private function fetchImage(int $width, int $height): string
    {
        if (app()->environment('testing')) {
            return $this->placeholder();
        }

        try {
            $context = stream_context_create(['http' => ['timeout' => 5]]);
            $content = file_get_contents("https://loremflickr.com/$width/$height", false, $context);

            return $content === false ? $this->placeholder() : $content;
        } catch (Throwable $exception) {
            return $this->placeholder();
        }
    }

    private function placeholder(): string
    {
        return base64_decode(self::PLACEHOLDER_JPEG_BASE64);
    }
}
