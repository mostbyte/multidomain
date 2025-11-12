<?php

namespace Mostbyte\Multidomain\Commands;

use Illuminate\Console\Command;

class MultidomainCommand extends Command
{
    public $signature = 'multidomain';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
