<?php

namespace Mostbyte\Multidomain\Console;

use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Mostbyte\Multidomain\Services\CommandsService;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class MostbyteFresh extends FreshCommand
{
    use ConfirmableTrait;

//    protected $signature = 'mostbyte:fresh {schema}';
    protected $name = 'mostbyte:fresh {schema}';

    public function handle(): int
    {
        /** @var CommandsService $commandService */
        $commandService = app(CommandsService::class);

        try {
            $commandService->execute($this->argument('schema'));
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());
            return CommandAlias::INVALID;
        }

        return parent::handle();
    }
}