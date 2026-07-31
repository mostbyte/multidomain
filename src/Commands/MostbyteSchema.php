<?php

namespace Mostbyte\Multidomain\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mostbyte\Multidomain\Services\CommandsService;
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
     */
    public function handle(): int
    {
        /** @var CommandsService $commandService */
        $commandService = app(CommandsService::class);

        try {
            $schema = $commandService->validateSchema($this->argument('schema'));
            $alreadyExists = $commandService->exists($schema);

            // Idempotent: re-running provisioning for a known tenant is a no-op,
            // not a failure.
            DB::statement('CREATE SCHEMA IF NOT EXISTS "'.$schema.'"');
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::INVALID;
        }

        CommandsService::invalidateSchemaCache($schema);

        $this->components->info($alreadyExists
            ? 'Schema already exists, nothing to do.'
            : 'Schema created successfully!');

        return self::SUCCESS;
    }
}
