<?php

namespace Mostbyte\Multidomain\Commands;

use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Console\Command;
use Mostbyte\Multidomain\Middlewares\MultidomainMiddleware;

class MostbyteInstall extends Command
{
    protected $signature = 'mostbyte:install';

    public function handle(): void
    {
        if (!is_null(config('telescope'))) {
            config([
                'telescope.path' => "{domain}/telescope",
                'telescope.middleware' => [
                    'web',
                    Authorize::class,
                    MultidomainMiddleware::class
                ]
            ]);
        }
    }
}
