<?php

namespace Mostbyte\Multidomain\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mostbyte\Multidomain\Services\CommandsService;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class MostbyteSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mostbyte:schema {schema}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will create new schema';

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
            $schema = $commandService->validateSchema($this->argument('schema'));
            $commandService->schemaExists($schema);
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());
            return CommandAlias::INVALID;
        }

        DB::statement('CREATE SCHEMA "'. $schema .'"');

        $this->components->info('Schema created successfully!');
        return CommandAlias::SUCCESS;
    }
}
