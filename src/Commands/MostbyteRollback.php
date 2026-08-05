<?php

namespace Mostbyte\Multidomain\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mostbyte\Multidomain\Services\CommandsService;
use Throwable;

class MostbyteRollback extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mostbyte:rollback {schema}  {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will rollback migrations and delete schema';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return self::FAILURE;
        }

        try {
            /** @var CommandsService $commandService */
            $commandService = app(CommandsService::class);
            $schema = $commandService->execute($this->argument('schema'));
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::INVALID;
        }

        $driver = config('multidomain.driver') ?? config('database.default');
        DB::purge($driver);

        try {
            // DROP SCHEMA CASCADE requires AccessExclusiveLock on every object in the
            // schema. Under active traffic each incoming SELECT holds AccessShareLock,
            // so the DROP blocks indefinitely and the proxy returns 504 after ~4 min.
            // Set statement_timeout to fail fast with a clear error instead of hanging.
            DB::unprepared("SET statement_timeout = '30s'");
            DB::statement('DROP SCHEMA "'.$schema.'" CASCADE');
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());
            $this->components->warn('Retry during lower traffic or terminate active connections to this schema first.');

            return self::FAILURE;
        }

        CommandsService::invalidateSchemaCache($schema);

        // Media cleanup is best-effort: the schema is already gone, so a missing
        // or unreachable folder must not report the rollback as failed — the
        // caller would retry a drop that already happened. The S3 driver throws
        // instead of returning false when the prefix does not exist.
        try {
            $deleted = Storage::deleteDirectory("public/$schema");
        } catch (Throwable $exception) {
            $deleted = false;
        }

        if (! $deleted) {
            $this->components->warn("Error when deleting \"$schema\" folder!");

            return self::SUCCESS;
        }

        $this->newLine();

        $this->components->info('Rollback finished successfully!');

        return self::SUCCESS;
    }
}
