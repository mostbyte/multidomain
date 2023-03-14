<?php

namespace Mostbyte\Multidomain\Console;

use Illuminate\Console\Command;
use Mostbyte\Multidomain\Services\CommandsService;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class MostbyteMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mostbyte:migrate {schema}
                {--force : Force the operation to run when in production}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--pretend : Dump the SQL queries that would be run}
                {--seed : Indicates if the seed task should be re-run}
                {--step : Force the migrations to be run so they can be rolled back individually}';

    /**
     * Execute the console command.
     *
     * @return int
     */
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

        $this->components->task('Migrating tables', fn () => $this->call('migrate', array_filter([
                '--seed' => $this->option('seed'),
                '--realpath' => $this->option('realpath'),
                '--pretend' => $this->option('pretend'),
                '--step' => $this->option('seed'),
                '--force' => $this->option('force'),
            ])) == 0);

        shell_exec("chown -R www-data:www-data ./storage");

        return CommandAlias::SUCCESS;
    }
}
