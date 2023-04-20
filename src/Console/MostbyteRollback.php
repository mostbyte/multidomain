<?php

namespace Mostbyte\Multidomain\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mostbyte\Multidomain\Services\CommandsService;
use Symfony\Component\Console\Command\Command as CommandAlias;
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
     *
     * @return int
     */
    public function handle(): int
    {
        if (!$this->confirmToProceed()) {
            return CommandAlias::FAILURE;
        }

        try {
            /** @var CommandsService $commandService */
            $commandService = app(CommandsService::class);
            $schema = $commandService->execute($this->argument('schema'));
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());
            return CommandAlias::INVALID;
        }

        $driver = config('database.default');
        config(["database.connections.$driver.schema" => $schema]);
        DB::purge($driver);

        $this->components->task('Dropping all tables', fn () => $this->callSilent('db:wipe', array_filter([
                '--force' => true,
            ])) == 0);

        DB::statement('DROP SCHEMA "'. $schema .'"');

        if (!Storage::deleteDirectory("public/$schema")){
            $this->components->error("Error when deleting \"$schema\" folder!");
            return CommandAlias::INVALID;
        }

        $this->newLine();

        $this->components->info('Rollback finished successfully!');
        return CommandAlias::SUCCESS;
    }
}
